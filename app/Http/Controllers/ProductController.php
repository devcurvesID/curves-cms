<?php

namespace App\Http\Controllers;

use App\AutoNumber;
use App\Http\Requests\AddBulkProductConsignmentAcceptanceRequest;
use App\Http\Requests\AddBulkProductPromoRequest;
use App\Model\HQ\ConsignmentOrder;
use App\Model\HQ\ConsignmentOrderDetail;
use App\Model\Log\RewardHistory;
use App\Model\MasterData\Bank;
use App\Model\MasterData\Challenge;
use App\Model\Product\PromoProduct;
use App\User;
use App\Http\Requests\AddBulkProductRequest;
use App\Http\Requests\ProductDetailRequest;
use App\Http\Requests\ProductDiscountGlobalRequest;
use App\Http\Requests\ProductInventoryDiscountRequest;
use App\Http\Requests\ProductPaymentRequest;
use App\Http\Requests\ProductTypeRequest;
use App\Http\Requests\UpdateBulkProductInventoryCorrectionRequest;
use App\Http\Requests\UpdateBulkProductInventoryRequest;
use App\Model\Club\ClubInfo;
use App\Model\Log\LogProductDiscount;
use App\Model\Log\LogProductDiscountGlobal;
use App\Model\Log\LogProductInventoryCorrection;
use App\Model\Log\LogProductInventoryTransfer;
use App\Model\MasterData\AccessMenu;
use App\Model\MasterData\PaymentMethod;
use App\Model\Product\BrokenProduct;
use App\Model\Product\LogProductInventory;
use App\Model\Product\LogProductPrice;
use App\Model\Product\Product;
use App\Model\Product\ProductDiscount;
use App\Model\Product\ProductInventory;
use App\Model\Product\ProductPayment;
use App\Model\Product\ProductTypes;
use App\Model\Product\ProductRefund;
use App\Model\Product\ProductRefundDetail;
use App\Model\Product\ProductRefundInventory;
use App\Model\Product\ProductRequest;
use App\Model\Product\ProductRequestDetail;
use App\Model\Product\ProductShipment;
use App\Model\Product\ProductShipmentDetail;
use App\Model\Product\ProductShipmentArrival;
use App\Model\Product\ProductShipmentArrivalDetail;
use App\Model\Tax\Taxtype;
use App\Model\Voucher\Voucher;
use App\Model\Product\ProductPaymentDetail;
use Carbon\Carbon;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Auth;
use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use App\Services\ApiClientService;

use App\AccurateAuth;
use App\Helpers\Accurate;
use stdClass;

//Includes WebClientPrint classes
//include_once(app_path().'/WebClientPrint/WebClientPrint.php');
require_once __DIR__.'/../../WebClientPrint/WebClientPrint.php';
use Neodynamic\SDK\Web\WebClientPrint;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('wm-only');
        $this->middleware('master-product', ['except' => [
            'type_index',
            'type_store',
            'type_update',
            'type_destroy',
            'product_setting_page',
            'product_inventory_choose_club',
            'product_inventory_correction_choose_club',
            'product_inventory',
            'edit_inventory',
            'edit_inventory_correction',
            'update_bulk_product_inventory',
            'update_bulk_product_inventory_correction',
            'inventory_detail',
            'update_product_inventory_detail',
            'update_product_discount',
            'sales_product_list_filtered',
            'sales_product_list',
            'add_to_cart',
            'cart_list',
            'update_cart_qty',
            'remove_from_cart',
            'cart_payment',
            'store_product_payment',
            'product_payment_detail',
            'product_cashier',
            'store_product_cashier',
        ]]);
    }

    //Product Type
    public function type_index() {
//        return "Product Type: ini index";
//        $roles = Role::all()->sortBy('role');
//        $count = $roles->count();
//        return view('cf_back.roles.roles', compact('roles', 'count'));

        $open = 0;
        foreach (Auth::user()->user_accessmenu as $access):
            if ($access->id == 3):
                $open = 1;
            endif;
        endforeach;

        if ($open == 1):
            $types = ProductTypes::all()->sortBy('type');
            $count = $types->count();
            //return $types;
            return view('cf_lite.product.product-type-list', compact('types', 'count'));
        elseif ($open == 0):
            return redirect('cf-lite/product-setting');
        endif;
    }


    public function type_store(ProductTypeRequest $request) {
        $input = $request->all();

//        ProductTypes::create($input);
//        $request->create($input);

        $newData = new ProductTypes;
        $newData->type = $request->input('type');
        $newData->save();

        Session::flash('cf_lite.includes.flash_message', trans('messages.type_create_message'));

        return redirect('cf-lite/product/type');
    }


    public function type_update($id, ProductTypeRequest $request) {
//        return "Product type: update, id:".$type->id." type:".$type->type;

        $type = ProductTypes::findOrFail($id);

        $type->type = $request->input('type');
//        $type->type = $type->type."new";
        $type->save();

        Session::flash('cf_back.flash_message', trans('messages.type_update_message'));

        return redirect('cf-lite/product/type');
    }

    public function type_destroy($id) {
//        return "Product type: destroy, id:".$id;
        $type = ProductTypes::findOrFail($id);
        $type->delete();

        Session::flash('cf_back.flash_message', trans('messages.type_delete_message'));
//        return "deleted: ".$type;
        return redirect('cf-lite/product/type');
    }




    //Product
    public function product_list() {
        $product_types = ProductTypes::orderBy('type', 'asc')->get();
        $product_type_list = $product_types->pluck('type', 'id');
        $latest_products = Product::orderBy('updated_at', 'desc')->take(25)->get();

        return view('cf_lite.product.product-main', compact('product_types', 'latest_products', 'product_type_list'));
    }

    public function store_new_product(Request $request) {
        $this->validate($request, [
            'item_name' => 'required|string|max:255|unique:mst_products',
            'product_type' => 'required',
        ]);

        $product = new Product;
        $product->product_type_id = $request->product_type;
        $product->item_name = $request->item_name;
        $product->default_price = '0';
        $product->is_taxable = 'Yes';
        $product->accessibility = 'Retail';
        $product->status = 'Inactive';
        if ($product->save()) {
            $clubs = ClubInfo::where('hq', '=', true)->get();
            foreach ($clubs as $club) {
                $product_inv = new ProductInventory;
                $product_inv->club_id = $club->id;
                $product_inv->product_id = $product->id;
                $product_inv->selling_price = $product->default_price;
                $product_inv->qty = '0';
                $product_inv->latest_update_date = Carbon::now();
                $product_inv->save();
            }
        }

        return redirect('cf-lite/master-product/'.$product->id.'/edit');
    }

    public function edit_product($id) {
        $product = Product::findOrFail($id);
        $allproduct = Product::where('status', '=', 'Active')
            ->where('is_promo', '=', false)
            ->get();
        $categories = ProductTypes::orderBy('type', 'asc')->get();
        $last_discount = LogProductDiscountGlobal::where('product_id', '=', $product->id)->orderBy('thru_date', 'desc')->first();

        return view('cf_lite.product.product-detail', compact('product', 'categories', 'last_discount', 'allproduct'));
    }

    public function update_product($id, ProductDetailRequest $request) {
        $product = Product::findOrFail($id);
        $last_price = $product->default_price;
        $last_consignment_price = $product->consignment_price;

        $product->product_type_id = $request->product_type;
        $product->item_name = $request->item_name;
        $product->default_price = $request->default_price;
        $product->is_taxable = $request->is_taxable;
        $product->accessibility = $request->accessibility;
        $product->status = $request->status;
        $product->consignment_price = $request->consignment_price;

        if ($request->hasFile('product_image')) {
//            if (!empty($product->product_image)):
//                $this->deletePhoto($product);
//            endif;

            $path = 'images/product_images';
            $product->product_image = $this->uploadPhoto($path ,$request);

            //Symfony Process added 23032018
            //$wow = getcwd();
            //$process = new Process('chmod 664 images/product_images/*');
            //$process->setWorkingDirectory($wow);
        }

        if (!empty($request->notes)):
            $product->notes = $request->notes;
        endif;

        if ($product->save()) {
            // TODO : Perbaiki koding ini, rentan error kalau belum ada produk ini di club manapun
            $product_inv = ProductInventory::where('product_id', '=', $product->id)->first();
            $product_inv->selling_price = $product->default_price;
            if (!empty($product->notes)) {
                $product_inv->notes = $product->notes;
            }
            $product_inv->save();
        }

        //Symfony Process added 23032018
        if (!empty($process)):
            $process->run();
        endif;

        //Update LogProductPrice
        if (count($product->product_logproductprice) > 0):
            foreach ($product->product_logproductprice as $logprice):
                if (($logprice->thru_date == null) && ($last_price != $request->default_price || $last_consignment_price != $request->consignment_price)):
                    $logprice->thru_date = Carbon::now();
                    $logprice->save();

                    $logproductprice = new LogProductPrice;
                    $logproductprice->product_id = $product->id;
                    $logproductprice->price = $request->default_price;
                    $logproductprice->from_date = Carbon::now();
                    $logproductprice->consignment_price = $request->consignment_price;
                    $logproductprice->save();
                endif;
            endforeach;
        endif;

        Session::flash('flash_message', trans('messages.new_product_has_been_added'));
        return redirect('cf-lite/master-product/'.$product->id.'/edit');
    }

    public function update_product_discount_global($id, ProductDiscountGlobalRequest $request) {
        $product = Product::findOrFail($id);

        $log_global = new LogProductDiscountGlobal;
        $log_global->product_id = $product->id;
        $log_global->discount_base = $request->discount_base;

        if ($request->discount_base == 'percent'):
            $log_global->discount_percent = $request->discount_percent;
            $log_global->discount_value = '0';
        elseif ($request->discount_base == 'value'):
            $log_global->discount_value = $request->discount_value;
            $log_global->discount_percent = ($request->discount_value * 100 / $product->default_price);
        endif;

        $log_global->discount_category = $request->discount_category;
        $log_global->from_date = $request->from_date;
        $log_global->thru_date = $request->thru_date;
        $log_global->save();


        //Set to All
        $product_inventories = ProductInventory::where('product_id', '=', $product->id)->get();
        if ($product_inventories->count() > 0):
            foreach ($product_inventories as $product_inventory):
                $the_discount = 1;
                if (!empty($product_inventory->productinv_productdiscount)):
                    if ($product_inventory->productinv_productdiscount->discount_type == 'Club'):
                        if (((strtotime($log_global->from_date) >= strtotime($product_inventory->productinv_productdiscount->from_date)) && (strtotime($log_global->from_date) <= strtotime($product_inventory->productinv_productdiscount->thru_date))) ||
                            ((strtotime($log_global->thru_date) >= strtotime($product_inventory->productinv_productdiscount->from_date)) && (strtotime($log_global->thru_date) <= strtotime($product_inventory->productinv_productdiscount->thru_date)))):
                            $the_discount = 0;
                        endif;
                    endif;
                endif;

                if ($the_discount == 1):
                    //Product Discount
                    if (!empty($product_inventory->productinv_productdiscount)):
                        $product_discount = $product_inventory->productinv_productdiscount;
                    else:
                        $product_discount = new ProductDiscount;
                    endif;

                    $product_discount->product_inventory_id = $product_inventory->id;

                    if ($request->discount_base == 'percent'):
                        $product_discount->discount_percent = $log_global->discount_percent;
                        $product_discount->discount_value = $product_inventory->selling_price * $log_global->discount_percent / 100;
                    elseif ($request->discount_base == 'value'):
                        $product_discount->discount_value = $log_global->discount_value;
                        $product_discount->discount_percent = ($log_global->discount_value * 100 / $product_inventory->selling_price);
                    endif;

                    $product_discount->discount_category = $log_global->discount_category;
                    $product_discount->discount_type = 'Global';
                    $product_discount->from_date = $log_global->from_date;
                    $product_discount->thru_date = $log_global->thru_date;
                    $product_inventory->productinv_productdiscount()->save($product_discount);


                    //Log Product Discount
                    $log_discount = new LogProductDiscount;
                    $log_discount->product_inventory_id = $product_inventory->id;

                    if ($request->discount_base == 'percent'):
                        $log_discount->discount_percent = $log_global->discount_percent;
                        $log_discount->discount_value = $product_inventory->selling_price * $log_global->discount_percent / 100;
                    elseif ($request->discount_base == 'value'):
                        $log_discount->discount_value = $log_global->discount_value;
                        $log_discount->discount_percent = ($log_global->discount_value * 100 / $product_inventory->selling_price);
                    endif;

                    $log_discount->discount_category = $log_global->discount_category;
                    $log_discount->discount_type = 'Global';
                    $log_discount->from_date = $log_global->from_date;
                    $log_discount->thru_date = $log_global->thru_date;
                    $log_discount->save();
                endif;
            endforeach;
        endif;

        Session::flash('cf_back.flash_message', trans('messages.global_discount_has_been_set'));

        return redirect('cf-lite/master-product/'.$product->id.'/edit');
    }

    public function product_search(Request $request) {
        $search_req = $request->product;
        $product_types = ProductTypes::orderBy('type', 'asc')->get();
        $latest_products = Product::where('item_name', 'ILIKE', '%'.$search_req.'%')
            ->orderBy('updated_at', 'desc')
            ->get();
        $product_type_list = $product_types->pluck('type', 'id');

        return view('cf_lite.product.product-main', compact('product_types', 'latest_products', 'search_req', 'product_type_list'));
    }

    public function product_list_by_category($id) {
        $category = ProductTypes::findOrFail($id);

        $products = Product::where('product_type_id', '=', $category->id)->orderBy('updated_at', 'desc')->paginate(50);

        return view('cf_lite.product.product-by-category', compact('category', 'products'));
    }

    public function product_search_by_category($id, Request $request) {
        $category = ProductTypes::findOrFail($id);
        $search_req = $request->product;

        $products = Product::where('product_type_id', '=', $category->id)
            ->where('item_name', 'LIKE', '%'.$search_req.'%')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('cf_lite.product.product-by-category', compact('category', 'products', 'search_req'));
    }

    public function add_bulk_product() {
        $categories = ProductTypes::orderBy('type', 'asc')->pluck('type', 'id');

        return view('cf_lite.product.add-bulk-product', compact('categories'));
    }

    public function store_bulk_product(AddBulkProductRequest $request) {
        $item_names = $request->item_name;
        $default_prices = $request->default_price;
        $is_taxables = $request->is_taxable;
        $accessibilities = $request->accessibility;
        $status = $request->status;

        foreach ($item_names as $key => $value):
            if (!empty($value)):
                $product = new Product;
                $product->product_type_id = $request->product_type;
                $product->item_name = $value;
                $product->default_price = $default_prices[$key];
                $product->is_taxable = $is_taxables[$key];
                $product->accessibility = $accessibilities[$key];
                $product->status = $status[$key];
                if ($product->save()) {
                    $clubs = ClubInfo::where('hq', '=', true)->get();
                    foreach ($clubs as $club) {
                        $product_inv = new ProductInventory;
                        $product_inv->club_id = $club->id;
                        $product_inv->product_id = $product->id;
                        $product_inv->selling_price = $product->default_price;
                        $product_inv->qty = '0';
                        $product_inv->latest_update_date = Carbon::now();
                        $product_inv->save();
                    }
                }

                $logproductprice = new LogProductPrice;
                $logproductprice->product_id = $product->id;
                $logproductprice->price = $default_prices[$key];
                $logproductprice->from_date = Carbon::now();
                $logproductprice->save();
            endif;
        endforeach;

        Session::flash('cf_back.flash_message', trans('messages.all_products_has_been_added'));

        return redirect('cf-lite/master-product');
    }

    //Log Product Prices


    //Product Inventories
    public function product_inventory_choose_club() {
        $open = 0;
        foreach (Auth::user()->user_accessmenu as $access):
            if ($access->id == 5):
                $open = 1;
            endif;
        endforeach;

        if ($open == 1):
            $clubs = ClubInfo::orderBy('club_name', 'asc')->paginate(12);

            return view('cf_lite.product.product-inventory-choose-club', compact('clubs'));
        elseif ($open == 0):
            return redirect('cf-lite/product-inventory/'.Auth::user()->club_id);
        endif;
    }

    public function product_inventory($id) {
        $club = ClubInfo::findOrFail($id);
        $club_id = $club->id;

        $stock_limit = 5;

        $need_stock_products = ProductInventory::where('club_id', '=', $club->id)
            ->where('qty', '<=', $stock_limit)
            ->orderBy('qty', 'asc')
            ->take(4)
            ->get();

        /*$inventories = Product::whereHas('product_productinv', function ($q) use ($club_id) {
            $q->where('club_id', '=', $club_id);
        })->orderBy('item_name', 'asc')
        ->paginate(25);*/

        $inventories = ProductInventory::where('club_id', '=', $club->id)
            ->orderBy('qty', 'asc')
            ->get();
//            ->paginate(100);

        return view('cf_lite.product.product-inventory', compact('club', 'need_stock_products', 'inventories'));
    }

    public function edit_inventory($id) {
        $club = ClubInfo::findOrFail($id);

        $products = Product::orderBy('item_name', 'asc')->get();

        return view('cf_lite.product.edit-bulk-product-inventory', compact('club', 'products'));
    }

    public function update_bulk_product_inventory($id, UpdateBulkProductInventoryRequest $request) {
        $club = ClubInfo::findOrFail($id);

        $qtys = $request->qty;
        $notes = $request->notes;

        foreach ($request->product_inv as $key => $value):
            $inventory = ProductInventory::where('club_id', '=', $club->id)->where('product_id', '=', $value)->first();
            if (!empty($inventory)):
                $inventory = ProductInventory::findOrFail($inventory->id);
                $inventory->qty = $inventory->qty + $qtys[$key];
            else:
                $product = Product::findOrFail($value);
                $inventory = new ProductInventory;
                $inventory->club_id = $club->id;
                $inventory->product_id = $product->id;
                $inventory->selling_price = $product->default_price;
                $inventory->qty = $qtys[$key];
            endif;
            if (!empty($notes[$key])):
                $inventory->notes = $notes[$key];
            endif;
            $inventory->latest_update_date = $request->update_date;
            $inventory->save();

            $log_inv = new LogProductInventoryTransfer;
            $log_inv->product_inventory_id = $inventory->id;
            $log_inv->qty = $qtys[$key];
            $log_inv->to_club = Auth::user()->club_id;
            $log_inv->update_date = $request->update_date;
            $log_inv->save();
        endforeach;

        Session::flash('cf_back.flash_message', trans('messages.inventory_product_has_been_updated'));

        return redirect('cf-lite/product-inventory/'.$club->id);
    }

    public function inventory_detail($id) {
        $product = ProductInventory::findOrFail($id);

        if ($product->club_id = Auth::user()->club_id):
            return view('cf_lite.product.product-inventory-detail', compact('product'));
        else:
            Session::flash('cf_back.flash_message', trans('messages.you_dont_have_permission_to_access_this_inventory'));

            return redirect('cf-lite/product-inventory/'.Auth::user()->club_id);
        endif;
    }

    public function update_product_inventory_detail($id, Request $request) {
        $product = ProductInventory::findOrFail($id);

        $this->validate($request, [
            'selling_price' => 'required|numeric',
            'notes' => 'sometimes',
            'update_date' => 'required'
        ]);

        $product->selling_price = $request->selling_price;
        if (!empty($request->notes)):
            $product->notes = $request->notes;
        endif;
        $product->latest_update_date = $request->update_date;
        $product->save();

        $log_product_inv_transfer = LogProductInventoryTransfer::where('product_inventory_id','=',$id)->orderBy('created_at','desc')->first();
        if(!empty($log_product_inv_transfer)){
            $log_id = $log_product_inv_transfer->id;
            $log_product_inv_transfer = LogProductInventoryTransfer::findOrFail($log_id);
            $log_product_inv_transfer->update_date = $request->update_date;
            $log_product_inv_transfer->save();
        }else{
            $log_product_inv_transfer = new LogProductInventoryTransfer;
            $log_product_inv_transfer->product_inventory_id = $id;
            $log_product_inv_transfer->qty = 0;
            $log_product_inv_transfer->to_club = Auth::user()->club_id;
            $log_product_inv_transfer->update_date = $request->update_date;
            $log_product_inv_transfer->save();
        }

        Session::flash('cf_back.flash_message', trans('messages.inventory_has_been_updated'));

        return redirect('cf-lite/product-inventory/edit/'.$product->id);
    }

    public function update_product_discount($id, ProductInventoryDiscountRequest $request) {
        $product = ProductInventory::findOrFail($id);

        if (!empty($product->productinv_productdiscount)):
            $product_discount = $product->productinv_productdiscount;
        else:
            $product_discount = new ProductDiscount;
        endif;

        $product_discount->discount_percent = $request->discount_percent;
        $product_discount->discount_value = $request->discount_value;
        $product_discount->discount_category = $request->discount_category;
        $product_discount->discount_type = 'Club';
        $product_discount->from_date = $request->from_date;
        $product_discount->thru_date = $request->thru_date;
        $product->productinv_productdiscount()->save($product_discount);


        //Set Log
        $log = new LogProductDiscount;
        $log->product_inventory_id = $product->id;
        $log->discount_percent = $request->discount_percent;
        $log->discount_value = $request->discount_value;
        $log->discount_category = $request->discount_category;
        $log->discount_type = 'Club';
        $log->from_date = $request->from_date;
        $log->thru_date = $request->thru_date;
        $log->save();

        Session::flash('cf_back.flash_message', trans('messages.discount_has_been_set'));

        return redirect('cf-lite/product-inventory/edit/'.$product->id);
    }


    //Product Sales
    public function sales_product_list() {
        $products = ProductInventory::where('club_id', '=', Auth::user()->club_id)
            ->whereHas('productinv_product', function ($q) {
                $q->where('accessibility', '<>', 'Reward')
                ->where('status', '=', 'Active');
            })
            ->get();

        Cart::destroy();

        return view('cf_lite.product.sales-product.product-list', compact('products'));
    }

    public function sales_product_list_filtered(Request $request) {
        $products = ProductInventory::where('club_id', '=', Auth::user()->club_id)
            ->whereHas('productinv_product', function($t) {
                $t->where('status', '=', 'Active');
            });

        if (!empty($request->access)):
            $access = $request->access;
            $products->whereHas('productinv_product', function ($q) use ($access) {
                if ($access == 'Member'):
                    $q->where('accessibility', '<>', 'Reward');
                elseif ($access == 'Retail'):
                    $q->where('accessibility', '=', $access);
                endif;
            });
        endif;

        if (!empty($request->category)):
            $category = $request->category;
            $products->whereHas('productinv_product', function ($r) use ($category) {
                $r->where('product_type_id', '=', $category);
            });
        endif;

        if (!empty($request->s)):
            $search = $request->s;
            $products->whereHas('productinv_product', function ($s) use ($search) {
                // $s->where('item_name', 'LIKE', '%'.ucwords($search).'%');
                $s->whereRaw('"mst_products"."item_name" ILIKE \'%'.$search.'%\'');
            });
        endif;

        $products = $products->get();

        return view('cf_lite.product.sales-product.product-list', compact('products'));
    }

    public function add_to_cart($id, Request $request) {
        $product = ProductInventory::findOrFail($id);

        $discount_percent = null;
        $discount_value = null;
        if (!empty($product->productinv_productdiscount)):
            if ((strtotime($product->productinv_productdiscount->from_date) <= strtotime(Carbon::now())) && (strtotime($product->productinv_productdiscount->thru_date) >= strtotime(Carbon::now()))):
                $discount_percent = $product->productinv_productdiscount->discount_percent;
                $discount_value = $product->productinv_productdiscount->discount_value;
            endif;
        endif;
        Cart::add($product->id, $product->productinv_product->item_name, $request->qty, $product->selling_price, ['image' => $product->productinv_product->product_image, 'disc_percent' => $discount_percent, 'disc_value' => $discount_value]);

        return redirect('cf-lite/product/f');
    }

    public function cart_list() {
        $carts = Cart::content();

        return view('cf_lite.product.sales-product.cart-list', compact('carts'));
    }

    public function update_cart_qty(Request $request) {
        if (!empty($request->qty)):
            $cart = Cart::get($request->input('rowid-up'));
            $product = ProductInventory::findOrFail($cart->id);

            if ($request->qty <= $product->qty):
                $qty = $request->qty;
            else:
                $qty = $product->qty;
            endif;

            Cart::update($request->input('rowid-up'), $qty);
        endif;

        return redirect('cf-lite/product/cart');
    }

    public function remove_from_cart(Request $request) {
        if (!empty($request->input('rowid-del'))):
            Cart::remove($request->input('rowid-del'));
        endif;

        return redirect('cf-lite/product/cart');
    }

    public function cart_payment() {
        $payment_methods = PaymentMethod::availableForPayment()->pluck('payment_method', 'id');

        $club_id = Auth::user()->club_id;
        /*$taxes = Taxtype::whereHas('taxtype_taxconfig', function($q) use ($club_id) {
            $q->where('club_id', '=', $club_id)
                ->where('thru_date', null);
        })->pluck('tax_type', 'id');*/

        //Payment Number added 190218
        $temp_auto_number = AutoNumber::findOrFail('1');

        //Member & Challenge added 22032018
        $members = User::where('club_id', '=', Auth::user()->club_id)
            ->where('role_id', '>', '2')
            ->whereHas('user_memberstatus', function ($q) {
                $q->where('status', '<>', 'Inactive')
                    ->where('status', '<>', 'Stopped');
            })
            ->orderBy('name', 'asc')
            ->pluck('name', 'id');

        $challenges = Challenge::orderBy('challenge', 'asc')->pluck('challenge', 'id');

        return view('cf_lite.payments.product-payment-detail', compact('payment_methods', 'temp_auto_number', 'club_id', 'members', 'challenges'/*, 'taxes'*/));
    }

    public function store_product_payment(ProductPaymentRequest $request) {
        $carts = Cart::content();

        if (Cart::count() > 0):
            //Update Auto Number
            $temp_auto_number = AutoNumber::findOrFail('1');
            $payment_number = $temp_auto_number->current_number;

            $temp_auto_number->current_number = $temp_auto_number->current_number + 1;
            $temp_auto_number->save();

            //Set Product Payment
            $product_payment = new ProductPayment;
            $product_payment->club_id = Auth::user()->club_id;
            $product_payment->cashier_id = Auth::id();
            $product_payment->payment_method_id = $request->payment_method;
            $product_payment->payment_number = sprintf('%08d', $payment_number).$request->payment_number;

            if (empty($request->reward)):
                $product_payment->subtotal = round(10 / 11 * $request->input('billing-total'));
                $product_payment->tax_value = $request->input('billing-total') - $product_payment->subtotal;

                if (!empty($request->voucher_code)):
                    $voucher = Voucher::where('identity_number', '=', $request->voucher_code)
                        ->whereDate('from_date', '<=', Carbon::now()->toDateString())
                        ->whereDate('thru_date', '>=', Carbon::now()->toDateString())
                        ->whereHas('voucher_vouchertype', function ($q) {
                            $q->where('voucher_type', '=', 'gift');
                        })
                        ->where('status', '=', 'Out')
                        ->first();

                    if (!empty($voucher)):
                        $product_payment->voucher_code = $request->voucher_code;
                        $product_payment->voucher_value = $request->voucher_value;

                        $voucher->status = 'Used';
                        $voucher->save();
                    endif;
                endif;

                $product_payment->total = $request->input('billing-total');
                $product_payment->pay = $request->pay;
                $product_payment->change = $request->pay - $request->input('billing-total');
            else:
                $product_payment->subtotal = 0;
                $product_payment->tax_value = 0;
                $product_payment->total = 0;
                $product_payment->pay = 0;
                $product_payment->change = 0;
            endif;

//            if (!empty($request->tax)):
//                $product_payment->tax_type_id = $request->tax;
//                $product_payment->tax_value = $request->tax_value;
//            endif;

            $product_payment->tax_type_id = 1;
            $product_payment->invoice_discount = $request->input('invoice_discount');

            if (!empty($request->payment_date)):
                $product_payment->payment_date = $request->payment_date;
            else:
                $product_payment->payment_date = Carbon::now();
            endif;

            if (!empty($request->member)):
                $product_payment->member_id = $request->member;
            endif;

            // Cek jika club adalah franchise
            $club = ClubInfo::findOrFail(Auth::user()->club_id);
            if ($club->club_jubilant->type == 'Franchise'):
                $product_payment->is_billed = false;
            endif;

            $product_payment->save();

            // Jonviter DEV
            // $product_payment->payment_date = $product_payment->payment_date->toDateTimeString(); 
            // $paymentData = $product_payment->toArray();
            // $apiClient = new ApiClientService();
            // $mobile_curves_api = $apiClient->post("/mobile-curves/payment-product/{$request->member}", $paymentData);
            // Jonviter DEV
            //Set Product Payment Detail
            foreach ($carts as $cart):
                $product_inv = ProductInventory::findOrFail($cart->id);
                //Add Product Payment Detail
                $product_payment_detail = new ProductPaymentDetail;
                $product_payment_detail->product_payment_id = $product_payment->id;
                $product_payment_detail->product_id = $product_inv->product_id;
                $product_payment_detail->price = $cart->price;
                $product_payment_detail->qty = $cart->qty;

                if (($cart->options->disc_percent != null) || ($cart->options->disc_value != null)):
                    $product_payment_detail->discount_percent = $cart->options->disc_percent;
                    $product_payment_detail->discount_value = $cart->options->disc_value;
                endif;

                $product_payment_detail->save();


                //Update Product Inventory Stock
                $product_inv->qty = $product_inv->qty - $cart->qty;
                $product_inv->save();


                //Reward History
                if (!empty($request->reward)):
                    if (!empty($request->member)):
                        $reward_history = new RewardHistory;
                        $reward_history->user_id = $request->member;
                        $reward_history->challenge_id = $request->challenge;
                        $reward_history->product_id = $product_inv->product_id;
                        $reward_history->save();
                    endif;
                endif;
            endforeach;

            Cart::destroy();



            app('App\Http\Controllers\AccountingJournalController')->product_payment($product_payment);

            return redirect('cf-lite/product/cart/payment/'.$product_payment->id.'/detail');
        endif;

        return redirect('cf-lite/product/cart/payment');
    }

    public function product_payment_detail($id) {
        $product_payment = ProductPayment::findOrFail($id);

        return view('cf_lite.payments.product-payment-detail-history', compact('product_payment'));
    }

    public function product_setting_page() {
        $access_list = AccessMenu::all();

        return view('cf_lite.product.product-setting-choice', compact('access_list'));
    }


    private function uploadPhoto($path, Request $request)
    {
        $photo = $request->file('product_image');
        $ext = $photo->getClientOriginalExtension();

        if ($request->file('product_image')->isValid()) {
            $photo_name = date('YmdHis') . ".$ext";
            $upload_path = $path;
            $request->file('product_image')->move($upload_path, $photo_name);
            return $photo_name;
        }

        return false;
    }

    private function deletePhoto(Product $product)
    {
        $exist = Storage::disk('product_images')->exists($product->product_image);

        if (isset($product->product_image) && $exist) {
            $delete = Storage::disk('product_images')->delete($product->product_image);
            if ($delete) {
                return true;
            }
            return false;
        }
    }

    //Product Refund
    public function refund_product_list($id){
        $invoice_details = ProductPaymentDetail::where('product_payment_id','=',$id)->get();
        $staffs = User::where('role_id','<>','7')->where('club_id','=',Auth::user()->club_id)->pluck('name','id');
        $product_payment = ProductPayment::findOrFail($id);
        $club_id = $product_payment->club_id;
        //Payment Number added 190218
        $temp_auto_number = AutoNumber::findOrFail('3');

        return view('cf_lite.document.product-invoice.refund-product', compact('invoice_details','id','staffs','club_id','temp_auto_number'));
    }

    public function refund_product($id, Request $request) {
        $this->validate($request,[
            'product_ref' => 'required',
            'staff' => 'required',
            'payment_number' => 'required',
            'refund_date' => 'required',
            'refund_reason' => 'sometimes',
            'refund_type' => 'required',
            'voucher_code' => 'required_if:refund_type,voucher',
            'from_date' => 'required_if:refund_type,voucher|date',
            'thru_date' => 'required_if:refund_type,voucher|date|after_or_equal:from_date'
        ]);

        $product_payment = ProductPayment::findOrFail($id);

        //hitung refund amount
        $total = 0;
        $totalQtys = 0;

        // Tambah stok barang sebanyak yang di refund
        $qtys = $request->refund_qty;

        foreach ($request->product_ref as $key => $value):
            $total += ($request->product_price[$key]*$qtys[$key]);
            $totalQtys += $qtys[$key];
        endforeach;

        if($totalQtys > 0){

            //Update Auto Number
            $temp_auto_number = AutoNumber::findOrFail('3');
            $payment_number = $temp_auto_number->current_number;

            $temp_auto_number->current_number = $temp_auto_number->current_number + 1;
            $temp_auto_number->save();

            //Tambah ke product refund
            $product_refund = new ProductRefund;
            $product_refund->product_payment_id = $id;
            $product_refund->staff_id = $request->staff;
            $product_refund->refund_number = sprintf('%08d', $payment_number).$request->payment_number;
            $product_refund->refund_date = $request->refund_date;
            $product_refund->refund_reason = $request->refund_reason;
            $product_refund->refund_amount = $total;
            $product_refund->refund_type = $request->refund_type;
            $product_refund->refund_status = 'pending';

            if($request->refund_type == 'voucher'){

                $voucher = new Voucher;
                $voucher->identity_number = $request->voucher_code;
                $voucher->from_date = $request->from_date;
                $voucher->thru_date = $request->thru_date;
                $voucher->type_id = '1';
                $voucher->status = 'Out';
                $voucher->voucher_base = 'value';
                $voucher->value = $total;
                $voucher->percent = '0';
                $voucher->save();

                $product_refund->voucher_id = $voucher->id;
            }
            $product_refund->save();

            //Tambah ke product refund detail
            foreach ($request->product_ref as $key => $value):
                $product_payment_detail = ProductPaymentDetail::findOrFail($value);
                $prd = new ProductRefundDetail;
                $prd->product_refund_id = $product_refund->id;
                $prd->product_payment_detail_id = $product_payment_detail->id;
                $prd->refund_quantity = $qtys[$key];
                $prd->price = $request->product_price[$key];
                $prd->save();

                $prinventory = new ProductRefundInventory;
                $prinventory->product_id = $product_payment_detail->product_id;
                $prinventory->quantity = $qtys[$key];
                $prinventory->product_refund_id = $product_refund->id;
                $prinventory->save();
            endforeach;

            if($request->refund_type == 'cash'){

                Session::flash('flash_message', trans('messages.refund_success'));

                return redirect('cf-lite/refund-product-list/'.$product_refund->id.'/detail');
            }else{

                Session::flash('flash_message', trans('messages.refund_success').' Voucher Code : '.$request->voucher_code);

                return redirect('cf-lite/refund-product-list/'.$product_refund->id.'/detail');
            }
        }
        Session::flash('flash_message', trans('messages.nothing_to_refund'));

        return redirect('cf-lite/invoice-list');
    }

    public function product_refund_detail($id) {
        $product_refund = ProductRefund::findOrFail($id);
        $product_refund_detail = ProductRefundDetail::where('product_refund_id','=',$id)->get();

        return view('cf_lite.document.product-invoice.refund-product-detail', compact('product_refund','product_refund_detail'));
    }

    public function product_refund_inventory($id, Request $request){

        $this->validate($request,[
            'product_inv' => 'required',
        ]);

        $product_refund = ProductRefund::findOrFail($id);
        $qtys = $request->qty;

        foreach ($request->product_inv as $key => $value):
            $product_refund_inventory = ProductRefundInventory::findOrFail($value);
            $inventory = ProductInventory::where('club_id', '=', $product_refund->productrefund_productpayment->club_id)->where('product_id', '=', $product_refund_inventory->product_id)->first();
            if (!empty($inventory)):
                $inventory = ProductInventory::findOrFail($inventory->id);
                $inventory->qty = $inventory->qty + $qtys[$key];
            else:
                $product = Product::findOrFail($value);
                $inventory = new ProductInventory;
                $inventory->club_id = $product_refund->productrefund_productpayment->club_id;
                $inventory->product_id = $product->id;
                $inventory->selling_price = $product->default_price;
                $inventory->qty = $qtys[$key];
            endif;
            $inventory->save();

            $product_refund_inventory->quantity -= $qtys[$key];
            $product_refund_inventory->save();
        endforeach;

        return redirect('cf-lite/invoice-list-refund');
    }

    //Product Inventory Correction
    public function product_inventory_correction_choose_club() {
        $open = 0;
        foreach (Auth::user()->user_accessmenu as $access):
            if ($access->id == 5):
                $open = 1;
            endif;
        endforeach;

        if ($open == 1):
            $clubs = ClubInfo::orderBy('club_name', 'asc')->paginate(12);

            return view('cf_lite.product.product-inventory-correction-choose-club', compact('clubs'));
        elseif ($open == 0):
            return redirect('cf-lite/product-inventory/'.Auth::user()->club_id);
        endif;
    }

    public function edit_inventory_correction($id) {
        $club = ClubInfo::findOrFail($id);
        $staffs = User::where('role_id','<>','7')->where('club_id','=',$id)->pluck('name','id');

        $products = ProductInventory::where('club_id','=',$id)->get();

        return view('cf_lite.product.edit-bulk-product-inventory-correction', compact('club', 'products','staffs'));
    }

    public function update_bulk_product_inventory_correction($id, UpdateBulkProductInventoryCorrectionRequest $request) {
        $club = ClubInfo::findOrFail($id);

        $qtys = $request->qty;
        $cordates = $request->cordate;
        $is_broken = $request->product_brk;
        $notes = $request->notes;
        $staff = $request->staff;

        if(count($request->product_inv) != 0){

            foreach ($request->product_inv as $key => $value):
                $inventory = ProductInventory::where('club_id', '=', $club->id)->where('id', '=', $value)->first();
                $current_stock = $inventory->qty;
                if (!empty($inventory)):
                    $inventory = ProductInventory::findOrFail($inventory->id);
                    $current_stock = $inventory->qty;
                    $inventory->qty =  $qtys[$key];
                endif;
                $inventory->save();

                if($qtys[$key] < $current_stock && !empty($is_broken[$key])){
                    $broken = new BrokenProduct;
                    $broken->product_inventory_id = $inventory->id;
                    $broken->quantity = $current_stock - $qtys[$key];
                    $broken->input_date = $cordates[$key];
                    $broken->staff_id = $staff;
                    $broken->save();
                }

                $log_inv_correction = new LogProductInventoryCorrection;
                $log_inv_correction->product_inventory_id = $inventory->id;
                $log_inv_correction->staff_id = $staff;
                $log_inv_correction->qty = $qtys[$key];
                $log_inv_correction->correction_date = $cordates[$key];
                if(!empty($notes[$key])){
                    $log_inv_correction->correction_reason = $notes[$key];
                }
                $log_inv_correction->save();

            endforeach;

            Session::flash('flash_message', trans('messages.inventory_product_has_been_updated'));
        }

        return redirect('cf-lite/product-inventory-correction/'.$club->id);
    }

    //Product Request
    public function product_request_main(){
//        return view('cf_lite.product.product-request-main');

        // Modified 27032020
        $clubs = ClubInfo::where('jubilant_id', '=', Auth::user()->user_club->jubilant_id)
            ->orderBy('club_name', 'asc')
            ->get();

        return view('cf_lite.product.product-choose-club', compact('clubs'));
    }

    public function product_request_club_list(){
        $club = ClubInfo::findOrFail(Auth::user()->club_id);
        // $requests = ProductRequest::where('club_id','=',$club->id)->whereDoesntHave('productrequest_productshipment')->orWhereHas('productrequest_productshipment',function($r){
        //         $r->where('shipment_status','<>','Sending');
        //         $r->where('shipment_status','<>','Finished');
        // })->paginate(100);

        // $requests = ProductRequest::whereDoesntHave('productrequest_productshipment')->orWhere(function($q){
        //         $q->whereHas('productrequest_productshipment',function($r){
        //             $r->where('shipment_status','<>','Sending');
        //             $r->where('shipment_status','<>','Finished');
        //         });
        // })->where('club_id','=',$club->id)->paginate(100);

        $requests = ProductRequest::where(function($q){
            $q->whereDoesntHave('productrequest_productshipment');
            $q->orWhere(function($r){
                $r->whereHas('productrequest_productshipment',function($s){
                    $s->where('shipment_status','<>','Sending');
                    $s->where('shipment_status','<>','Finished');
                });
            });
        })->where('club_id','=',$club->id)->paginate(100);

        Cart::destroy();

        return view('cf_lite.product.product-request-club-list',compact('requests','club'));
    }

    public function product_request_cancel($id, Request $request){
        $request = ProductRequest::findOrFail($id);
        $request->request_status = 'Canceled';
        $request->save();

        if(!empty($request->productrequest_productshipment)){
            $request->productrequest_productshipment->shipment_status = 'Failed';
        }

        return redirect('cf-lite/product-request');
    }

    public function product_request_item_list(Request $request) {
        // $products = ProductInventory::rightJoin('mst_products',function($join){
        //     $join->on('product_inventories.product_id','=','mst_products.id');
        //     $join->where('status','=','Active');
        // })->where('club_id','=',Auth::user()->club_id)->get(['mst_products.*','product_inventories.*']);

        if (!empty($request->p)):
            $products = Product::leftJoin('product_inventories',function($join){
                $join->on('product_inventories.product_id','=','mst_products.id');
                $join->where('club_id','=',Auth::user()->club_id);
            })
                ->where('status','=','Active')
                ->where('item_name', 'ILIKE', '%'.$request->p.'%')
                ->get(['*','mst_products.id as pr_id']);
        else:
            $products = Product::leftJoin('product_inventories',function($join){
                $join->on('product_inventories.product_id','=','mst_products.id');
                $join->where('club_id','=',Auth::user()->club_id);
            })->where('status','=','Active')->get(['*','mst_products.id as pr_id']);
        endif;

        return view('cf_lite.product.product-request-item-list', compact('products'));
    }

    public function product_request_detail($id, Request $request) {

        $products = ProductRequestDetail::where('product_request_id','=',$id)->paginate(100);

        return view('cf_lite.product.product-request-detail', compact('products'));
    }

    public function add_to_request_list($id, Request $request) {
        $product = Product::findOrFail($id);

        Cart::add($product->id, $product->item_name, $request->qty, $product->default_price, ['image' => $product->product_image]);

        return redirect('cf-lite/product-request-item-list');
    }

    public function request_list() {
        $carts = Cart::content();
        $club_id = Auth::user()->club_id;
        $temp_auto_number = AutoNumber::findOrFail('5');
        $clubs = ClubInfo::where('id','<>',$club_id)->pluck('club_name','id');

        return view('cf_lite.product.request-list', compact('carts','temp_auto_number','club_id','clubs'));
    }

    public function update_request_qty(Request $request) {
        if (!empty($request->qty)):
            $cart = Cart::get($request->input('rowid-up'));

            $qty = $request->qty;

            Cart::update($request->input('rowid-up'), $qty);
        endif;

        return redirect('cf-lite/product-request/cart');
    }

    public function remove_from_request(Request $request) {
        if (!empty($request->input('rowid-del'))):
            Cart::remove($request->input('rowid-del'));
        endif;

        return redirect('cf-lite/product-request/cart');
    }

    public function store_product_request(Request $request) {
        $this->validate($request,[
            'target_club' => 'required',
        ]);
        $carts = Cart::content();

        if (Cart::count() > 0):
            //Update Auto Number
            $temp_auto_number = AutoNumber::findOrFail('5');
            $request_number = $temp_auto_number->current_number;

            $temp_auto_number->current_number = $temp_auto_number->current_number + 1;
            $temp_auto_number->save();

            //Set Product Request
            $product_request = new ProductRequest;
            $product_request->club_id = Auth::user()->club_id;
            $product_request->request_status = 'Pending';
            $product_request->request_number = sprintf('%08d', $request_number).$request->request_number;
            $product_request->request_date = Carbon::now();
            $product_request->target_club = $request->target_club;

            $product_request->save();

            //Set Product Request Detail
            foreach ($carts as $cart):
                $product = Product::findOrFail($cart->id);
                //Add Product Request Detail
                $product_request_detail = new ProductRequestDetail;
                $product_request_detail->product_request_id = $product_request->id;
                $product_request_detail->product_id = $cart->id;
                $product_request_detail->amount = $cart->qty;

                $product_request_detail->save();

            endforeach;

            Cart::destroy();

            return redirect('cf-lite/product-request');
        endif;

        return redirect('cf-lite/product-request');
    }

    //Shipment
    public function product_request_all_club_list(){
        $club = ClubInfo::findOrFail(Auth::user()->club_id);
        $requests = ProductRequest::where('target_club','=',$club->id)->where('request_status','=','Pending')->orderBy('request_date','desc')->paginate(100);

        return view('cf_lite.product.product-shipment-list',compact('requests','club'));
    }

    public function product_shipment_detail($id, Request $request) {

        $productrequest = ProductRequest::findOrFail($id);
        $products = ProductRequestDetail::where('product_request_id','=',$id)->get();
        $staffs = User::where('role_id','<>','7')->where('club_id','=',Auth::user()->club_id)->pluck('name','id');
        $productrequestid = $id;
        $requeststatus = '';
        if(empty($productreq->productrequest_productshipment)){
            $requeststatus = 'notyet';
        }else{
            if($productreq->productrequest_productshipment->club_id != Auth::user()->club_id){
                if($productreq->productrequest_productshipment->shipment_status == 'Canceled'){
                    $requeststatus = 'notyet';
                }else{
                    $requeststatus = 'notthisnone';
                }
            }else{
                if($productreq->productrequest_productshipment->shipment_status == 'Canceled'){
                    $requeststatus = 'notyet';
                }else{
                    if($productreq->productrequest_productshipment->shipment_status == 'Accepted'){
                        $requeststatus = 'thisopennext';
                    }else{
                        $requeststatus = 'thisclose';
                    }
                }
            }
        }

        return view('cf_lite.product.product-shipment-detail', compact('products','staffs','productrequestid','requeststatus'));
    }

    public function accept_shipment($id, Request $request){
        $productreq = ProductRequest::findOrFail($id);

        if(empty($productreq->productrequest_productshipment)){
            $productshipment = new ProductShipment;
        }else{
            $productshipment = $productreq->productrequest_productshipment;
        }
        $productshipment->staff_id = $request->staff;
        $productshipment->club_id = Auth::user()->club_id;
        $productshipment->accept_date = Carbon::now();
        $productshipment->send_date = Carbon::now();
        $productshipment->shipment_status = 'Accepted';
        $productreq->productrequest_productshipment()->save($productshipment);

        $productreq->request_status = 'Accepted';
        $productreq->save();

        return redirect('cf-lite/product-request/accepted-requests');
    }

    public function product_request_accepted_list(){
        $club = ClubInfo::findOrFail(Auth::user()->club_id);
        $clubid = $club->id;
        $requests = ProductRequest::whereHas('productrequest_productshipment',function($q) use ($clubid){
            $q->where('club_id','=',$clubid)->where(function($r){
                $r->where('shipment_status','=','Accepted');
                $r->orWhere('shipment_status','=','Finished');
                $r->orWhere('shipment_status','=','Sending');
            });
        })->paginate(100);

        return view('cf_lite.product.product-shipment-accepted-list',compact('requests','club'));
    }

    public function product_request_send_list($id, Request $request){

        $club = ClubInfo::findOrFail(Auth::user()->club_id);
        $productrequest = ProductRequest::findOrFail($id);
        $products = ProductRequestDetail::where('product_request_id','=',$id)->get();
        $productrequestid = $id;

        foreach($products as $product){
            $inventory = ProductInventory::where('club_id','=',Auth::user()->club_id)->where('product_id','=',$product->productrequestdetail_product->id)->first();
            if(!empty($inventory)){
                $product->setAttribute('stok',$inventory->qty);
            }else{
                $product->setAttribute('stok','0');
            }
        }

        return view('cf_lite.product.product-request-send-list', compact('products','productrequest','club'));
    }

    public function product_shipment_cancel($id, Request $request){


        $prrequest = ProductRequest::findOrFail($id);
        $prrequest->request_status = 'Pending';
        $prrequest->save();

        $shipment = $prrequest->productrequest_productshipment;
        if($request->cancelorfail == 'cancel'){
            $shipment->shipment_status = 'Canceled';
            $shipment->save();
            return redirect('cf-lite/product-request/accepted-requests');
        }elseif($request->cancelorfail == 'fail'){
            $shipment->shipment_status = 'Failed';
            $shipment->save();
            return redirect('cf-lite/product-request');
        }


        return redirect('cf-lite/dashboard');
    }

    public function update_bulk_product_shipment_detail($id, Request $request) {
        $this->validate($request, [
            'product_inv' => 'required|min:1',
            'qty' => 'required|min:1',
        ]);

        $club = ClubInfo::findOrFail($id);
        $qtys = $request->qty;

        $productrequestdetail = new ProductRequestDetail;

        foreach ($request->product_inv as $key => $value):
            $productrequestdetail = ProductRequestDetail::findOrFail($value);

            $inventory = ProductInventory::where('club_id', '=', $club->id)->where('product_id', '=', $productrequestdetail->product_id)->first();
            if (!empty($inventory)){
                $inventory = ProductInventory::findOrFail($inventory->id);
                $inventory->qty = $inventory->qty - $qtys[$key];
                $inventory->save();
            }

            $detailShipment = new ProductShipmentDetail;
            $detailShipment->send_amount = $qtys[$key];
            $productrequestdetail->productrequestdetail_productshipmentdetail()->save($detailShipment);

        endforeach;

        $productrequest = $productrequestdetail->productrequestdetail_productrequest;
        $productshipment = $productrequest->productrequest_productshipment;
        $productshipment->shipment_status = 'Sending';
        $productshipment->send_date = Carbon::now();
        if(!empty($request->notes)){
            $productshipment->notes = $request->notes;
        }
        $productrequest->productrequest_productshipment()->save($productshipment);

        Session::flash('cf_back.flash_message', trans('messages.inventory_product_has_been_updated'));

        return redirect('cf-lite/product-request/product-request-main');
    }

    //ProductshipmentArrival
    public function product_shipment_arrival_list(){
        $club = ClubInfo::findOrFail(Auth::user()->club_id);
        $requests = ProductRequest::where('club_id','=',$club->id)->whereHas('productrequest_productshipment',function($r){
                $r->where('shipment_status','=','Sending');
                $r->orWhere('shipment_status','=','Finished');
        })->paginate(100);

        return view('cf_lite.product.product-shipment-arrival-list',compact('club','requests'));
    }

    public function product_shipment_arrival_detail_list($id, Request $request){
        $club = ClubInfo::findOrFail(Auth::user()->club_id);
        $productrequest = ProductRequest::findOrFail($id);
        $shipments = ProductRequestDetail::where('product_request_id','=',$id)->whereHas('productrequestdetail_productshipmentdetail')->get();

        return view('cf_lite.product.product-shipment-arrival-detail-list',compact('shipments','club','productrequest'));
    }

    public function update_bulk_product_shipment_arrival_detail($id, Request $request) {
        $this->validate($request, [
            'qty' => 'required',
        ]);

        $club = ClubInfo::findOrFail($id);
        $qtys = $request->qty;
        $notes = $request->notes;

        $productrequestdetail = new ProductRequestDetail;

        foreach ($request->qty as $key => $value):
            $productrequestdetail = ProductRequestDetail::findOrFail($key);

            $inventory = ProductInventory::where('club_id', '=', $club->id)->where('product_id', '=', $productrequestdetail->product_id)->first();
            if (!empty($inventory)){
                $inventory = ProductInventory::findOrFail($inventory->id);
                $inventory->qty = $inventory->qty + $qtys[$key];
                $inventory->save();
            }else{
                $inventory = new ProductInventory;
                $inventory->club_id = $club->id;
                $inventory->product_id = $productrequestdetail->product_id;
                $inventory->selling_price = $productrequestdetail->productrequestdetail_product->default_price;
                $inventory->qty = $qtys[$key];
            }
            $inventory->latest_update_date = Carbon::now();
            $inventory->save();

            $detailshipmentarrival = new ProductShipmentArrivalDetail;
            if (!empty($notes[$key])):
                $detailshipmentarrival->receiver_notes = $notes[$key];
            endif;

            $detailshipmentarrival->received_amount = $qtys[$key];
            $detailshipmentarrival->deficit = $qtys[$key] - $productrequestdetail->productrequestdetail_productshipmentdetail->send_amount;
            $productrequestdetail->productrequestdetail_productshipmentarrivaldetail()->save($detailshipmentarrival);

        endforeach;

        $productrequest = $productrequestdetail->productrequestdetail_productrequest;
        $productshipment = $productrequest->productrequest_productshipment;
        $productshipment->shipment_status = 'Finished';
        $productrequest->productrequest_productshipment()->save($productshipment);

        $productshipmentarrival = new ProductShipmentArrival;
        $productshipmentarrival->arrival_date = Carbon::now();
        $productrequest->productrequest_productshipmentarrival()->save($productshipmentarrival);

        Session::flash('cf_back.flash_message', trans('messages.inventory_product_has_been_updated'));

        return redirect('cf-lite/product-request/product-request-main');
    }

    public function product_cashier(){
        $products = ProductInventory::where('club_id', '=', Auth::user()->club_id)
            ->whereHas('productinv_product', function ($q) {
                $q->where('accessibility', '<>', 'Reward')
                    ->where('status', '=', 'Active');
            })->get();
        $payment_methods = PaymentMethod::availableForPayment()->pluck('payment_method', 'id');
        $members = User::where('club_id', '=', Auth::user()->club_id)
            ->where('role_id', '>', '2')
            ->whereHas('user_memberstatus', function ($q) {
                $q->where('status', '<>', 'Inactive')
                    ->where('status', '<>', 'Stopped');
            })
            ->orderBy('name', 'asc')->get();

//        $banks = Bank::where('id', '<>', 1)->get();
        $banks = Bank::where('id', '<>', 1)
            ->whereHas('bank_club', function ($q){
                $q->where('club_id', '=', Auth::user()->club_id);
            })->get();

        $wcpScript = WebClientPrint::createScript(action('WebClientPrintController@processRequest'), action('PrintESCPOSController@cashierProduct'), Session::getId());

        //ACCURATE
        /* $accurate = AccurateAuth::find(1);

        $redirectURI = env('ACCURATE_REDIRECT_URI_PRODUCT');
        //$databaseID = Auth::user() ? Accurate::accurateClub(Auth::user()->club_id) : env('ACCURATE_DATABASE_ID');
        //$openDB = Accurate::AccurateOpenDB($databaseID, $accurate->access_token );

        $accurateClub = Auth::user() ? Accurate::accurateClub(Auth::user()->club_id) : '';
        $openDB = '';
        $branchID = '';
        $databaseID = '';

        if($accurateClub){
            $databaseID = $accurateClub->accurate_id;
            $openDB = Accurate::AccurateOpenDB($databaseID, $accurate->access_token );
            $branchID = $accurateClub->accurate_branch;
        }

        $code = '';
        $accurateItems = [];

        if($accurateClub){
            if(isset($_GET['code'])){
                $code = $_GET["code"];
            }
            if($openDB){
                //echo 'DB opened..';
                //$accurateItems = Accurate::accurateCheckItems($openDB, 'INVENTORY', 1, 100);
                $x = 1;

                while($x <= 100) {
                    $_accurateItems = Accurate::accurateCheckItems($openDB, 'INVENTORY', $x, 100);

                    if($_accurateItems){
                        //do nothing
                        $accurateItems = array_replace_recursive($accurateItems, $_accurateItems);
                    }else{
                        break;
                    }
                    $x++;
                }
            }else{
                Accurate::oauthAOL($code, $accurate, $databaseID, $redirectURI);
            }
        }

        return view('cf_lite.cashiers.product-cashier-v1', compact('products', 'members', 'payment_methods','banks', 'wcpScript', 'accurateItems')); */

        return view('cf_lite.cashiers.product-cashier-v1', compact('products', 'members', 'payment_methods','banks', 'wcpScript'));

//        return view('cf_lite.cashiers.product-cashier', compact('products', 'members', 'payment_methods','banks', 'wcpScript'));
    }

    public function product_cashier_test(){
        $products = ProductInventory::where('club_id', '=', Auth::user()->club_id)
            ->whereHas('productinv_product', function ($q) {
                $q->where('accessibility', '<>', 'Reward')
                    ->where('status', '=', 'Active');
            })->get();
        $payment_methods = PaymentMethod::availableForPayment()->pluck('payment_method', 'id');
        $members = User::where('club_id', '=', Auth::user()->club_id)
            ->where('role_id', '>', '2')
            ->whereHas('user_memberstatus', function ($q) {
                $q->where('status', '<>', 'Inactive')
                    ->where('status', '<>', 'Stopped');
            })
            ->orderBy('name', 'asc')->get();

//        $banks = Bank::where('id', '<>', 1)->get();
        $banks = Bank::where('id', '<>', 1)
            ->whereHas('bank_club', function ($q){
                $q->where('club_id', '=', Auth::user()->club_id);
            })->get();

        $wcpScript = WebClientPrint::createScript(action('WebClientPrintController@processRequest'), action('PrintESCPOSController@cashierProduct'), Session::getId());

        //ACCURATE
        /* $accurate = AccurateAuth::find(1);

        $redirectURI = env('ACCURATE_REDIRECT_URI_PRODUCT');
        //$databaseID = Auth::user() ? Accurate::accurateClub(Auth::user()->club_id) : env('ACCURATE_DATABASE_ID');
        //$openDB = Accurate::AccurateOpenDB($databaseID, $accurate->access_token );

        $accurateClub = Auth::user() ? Accurate::accurateClub(Auth::user()->club_id) : '';
        $openDB = '';
        $branchID = '';
        $databaseID = '';

        if($accurateClub){
            $databaseID = $accurateClub->accurate_id;
            $openDB = Accurate::AccurateOpenDB($databaseID, $accurate->access_token );
            $branchID = $accurateClub->accurate_branch;
        }

        $accurateItems = [];

        if($accurateClub){
            $code = '';

            if(isset($_GET['code'])){
                $code = $_GET["code"];
            }
            if($openDB){
                //echo 'DB opened..';
                $x = 1;

                while($x <= 100) {
                    $_accurateItems = Accurate::accurateCheckItems($openDB, 'INVENTORY', $x, 100);

                    if($_accurateItems){
                        //do nothing
                        $accurateItems = array_replace_recursive($accurateItems, $_accurateItems);
                    }else{
                        break;
                    }
                    $x++;
                }
            }else{
                Accurate::oauthAOL($code, $accurate, $databaseID, $redirectURI);
            }
        }

        return view('cf_lite.cashiers.product-cashier-v1-test', compact('products', 'members', 'payment_methods','banks', 'wcpScript', 'accurateItems')); */


    }


    public function store_product_cashier(Request $request){

        /* $accurate = AccurateAuth::find(1);

        //$databaseID = Auth::user() ? Accurate::accurateClub(Auth::user()->club_id) : env('ACCURATE_DATABASE_ID');
        //$openDB = Accurate::AccurateOpenDB($databaseID, $accurate->access_token );
        //$branchID = Accurate::accurateBranch(Auth::user()->club_id);

        $accurateClub = Auth::user() ? Accurate::accurateClub(Auth::user()->club_id) : '';
        $openDB = '';
        $branchID = '';
        $databaseID = '';

        if($accurateClub){
            $databaseID = $accurateClub->accurate_id;
            $openDB = Accurate::AccurateOpenDB($databaseID, $accurate->access_token );
            $branchID = $accurateClub->accurate_branch;
        } */

        $item_list = json_decode($request->item_list);
        //dd($item_list);
        //die();
        // Update auto number
        $temp_auto_number = AutoNumber::findOrFail('1');
        $payment_number = $temp_auto_number->current_number;
        $temp_auto_number->current_number = $temp_auto_number->current_number + 1;
        //$temp_auto_number->save();

        // Adding product payment
        $month_roman = array(1=>'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');

        $product_payment = new ProductPayment;
        $product_payment->club_id = Auth::user()->club_id;
        $product_payment->cashier_id = $request->staff;
        $product_payment->payment_method_id = $request->payment_type;
        $product_payment->payment_number = sprintf('%08d', $payment_number).'/PY/CIND/'.sprintf('%03d', Auth::user()->club_id).'/'.$month_roman[Carbon::now()->format('n')].'/'.Carbon::now()->format('Y');

        $product_payment->subtotal = round(10 / 11 * $request->subtotal);
        $product_payment->tax_type_id = 1;
        $product_payment->tax_value = $request->subtotal - $product_payment->subtotal;

        $product_payment->voucher_code = null;
        $product_payment->voucher_value = 0;
        $product_payment->total = $request->subtotal;
        $product_payment->pay = $request->cash;
        $product_payment->change = $request->cash - $request->subtotal;

        if (!empty($request->payment_date)):
            $product_payment->payment_date = $request->payment_date;
        else:
            $product_payment->payment_date = Carbon::now();
        endif;

        $product_payment->member_id = $request->buyer;
        //$product_payment->save();

        // Adding to product payment detail
        foreach ($item_list as $item):
            $product_inv = ProductInventory::findOrFail($item->id);
            //Add Product Payment Detail
            $product_payment_detail = new ProductPaymentDetail;
            $product_payment_detail->product_payment_id = $product_payment->id;
            $product_payment_detail->product_id = $product_inv->product_id;
            $product_payment_detail->price = $item->price;
            $product_payment_detail->qty = $item->qty;

            $product_payment_detail->discount_percent = 0;
            $product_payment_detail->discount_value = 0;

            //$product_payment_detail->save();

            //Update Product Inventory Stock
            $product_inv->qty = $product_inv->qty - $item->qty;
            //$product_inv->save();
        endforeach;


        /*
        $buyer = User::find($request->buyer);
         $userData = [
            'name' => str_replace("%20", " ", $buyer->name),
            //'membership_type' => $request->membership_type,
            'fa_date' => $request->payment_date,
            'pay' => $request->pay,
            'item_list' => $item_list
        ];
        $this->store_accurate(Auth::user()->club_id, $userData); */
        return redirect('cf-lite/cashier/product-test');
    }

    /*****************************/
    /**** ACCURATE INVOICE SCRIPT ***/
    /*****************************/

    public function store_accurate($club_id, $userData)
    {
        //GET VARIABLES
        $accurate = AccurateAuth::find(1);
        //$databaseID = Accurate::accurateClub($club_id);
        //$openDB = Accurate::AccurateOpenDB($databaseID, $accurate->access_token );
        //$branchID = Accurate::accurateBranch($club_id);

        $accurateClub = Accurate::accurateClub($club_id);
        $openDB = '';
        $branchID = '';
        $databaseID = '';

        if($accurateClub){
            $databaseID = $accurateClub->accurate_id;
            $openDB = Accurate::AccurateOpenDB($databaseID, $accurate->access_token );
            $branchID = $accurateClub->accurate_branch;
        }

        $itemList = [];

        if($accurateClub){
            //CHECK IF USER EXIST IN ACCURATE
            //IF USER DO NOT EXIST, CREATE USING THE API
            $accurateUser = Accurate::AccurateCheckMember($openDB, $userData);
            if($accurateUser){
                //do nothing
            }else{
                $accurateUser = Accurate::AccurateCreateMember($openDB, $userData);
            }


            foreach($userData['item_list'] as $item){
                //CHECK IF ITEM CODE EXIST
                //IF ITEM CODE DO NOT EXIST, CREATE USING THE API

                $itemList[] = [
                    //"itemNo" => $accurateItem['no'],
                    "itemNo" => $item->accurate,
                    "quantity" => $item->qty,
                    "unitPrice" => $item->price
                ];
            }
            $invoiceData = [
                "customerNo" => $accurateUser['customerNo'],
                "transDate" => date("d/m/Y"),
                "branchId" => $branchID,
                "detailItem" => $itemList
            ];

            //CREATE SALES INVOICE ONE CUSTOMER & ITEM IS TRUE
            $accurateInvoice = Accurate::AccurateCreateInvoice($openDB, $invoiceData);
            Storage::put('invoices/'.date("d-m-Y").'_'.$accurateUser['customerNo'].'_product.txt', json_encode($accurateInvoice));
        }

    }

    public function product_consignment_acceptance() {
        $cos = ConsignmentOrder::where('club_dest_id', '=', Auth::user()->club_id)->where('delivery_order', '=', true)->where('is_receive', '=', false)->get();
        return view('cf_lite.product.product-consignment-acceptance', compact('cos'));
    }

    public function product_consignment_acceptance_detail($id){
        $co = ConsignmentOrder::findOrFail($id);
        return view('cf_lite.product.product-consignment-acceptance-detail', compact('co'));
    }

    public function store_product_consignment_acceptance(AddBulkProductConsignmentAcceptanceRequest $request, $id) {
        $amount_receives = $request->amount_receive;
        $co = ConsignmentOrder::findOrFail($id);
        $co->is_receive = true;
        if ($co->save()){
            foreach ($request->id as $key => $value) {
                $co_detail = ConsignmentOrderDetail::findOrFail($value);
                $product = ProductInventory::where('club_id', '=', Auth::user()->club_id)->where('product_id', '=', $co_detail->product_id)->first();

                if (!empty($product)) {
                    $product->qty = $product->qty + $amount_receives[$key];
                    $product->save();
                } else {
                    $new_product = new ProductInventory;
                    $new_product->club_id = Auth::user()->club_id;
                    $new_product->product_id = $co_detail->product_id;
                    $new_product->selling_price = $co_detail->price_per_pcs;
                    $new_product->qty = $amount_receives[$key];
                    $new_product->save();
                }
            }
            Session::flash('cf_back.flash_message', trans('messages.inventory_product_has_been_updated'));
        } else {
            Session::flash('cf_back.error_message', trans('messages.failed_update_product_inventory_from_consignment_order'));
        }

        return redirect('cf-lite/product-consignment-acceptance');
    }

    public function club_product_stock($club_id) {
        $club = ClubInfo::findOrFail($club_id);

        $inventories = ProductInventory::where('club_id', '=', $club_id)
            ->orderBy('qty', 'desc')
            ->get();
        $allowedUsers = ['1','4973','17184', '966'];
        if( in_array(Auth::user()->id, $allowedUsers)){
            $clubs = ClubInfo::where('id', '<>', $club_id)
                ->orderBy('club_name', 'asc')
                ->get();
        }else{
            $clubs = ClubInfo::where('jubilant_id', '=', Auth::user()->user_club->jubilant_id)
                ->where('id', '<>', $club_id)
                ->orderBy('club_name', 'asc')
                ->get();
        }
        /* foreach($clubs as $club){
            echo $club->club_name.' | ';
        } */
        return view('cf_lite.product.product-transfer-stock', compact('club', 'inventories', 'clubs'));
    }

    public function transfer_product_stock(Request $request) {
        $this->validate($request, [
            'inv_id' => 'required',
            'qty_each' => 'required|numeric'
        ]);

        $inventory = ProductInventory::findOrFail($request->inv_id);
        $clubs = $request->clubs;

        if ((count($clubs) * $request->qty_each) <= $inventory->qty):
            foreach ($clubs as $club):
                // Cek inventory club ybs
                $club_inv = ProductInventory::where('club_id', '=', $club)
                    ->where('product_id', '=', $inventory->product_id)
                    ->first();

                if (!empty($club_inv)):
                    // Update inventory club ybs
                    $update_club_inv = ProductInventory::findOrFail($club_inv->id);
                    $update_club_inv->qty = $update_club_inv->qty + $request->qty_each;
                    $update_club_inv->latest_update_date = Carbon::now();
                    $update_club_inv->save();
                else:
                    // Insert club inv
                    $update_club_inv = new ProductInventory;
                    $update_club_inv->club_id = $club;
                    $update_club_inv->product_id = $inventory->product_id;
                    $update_club_inv->selling_price = $inventory->selling_price;
                    $update_club_inv->qty = $request->qty_each;
                    $update_club_inv->latest_update_date = Carbon::now();
                    $update_club_inv->save();
                endif;

                // Check if product is promo Added 19042020
                if ($inventory->productinv_product->is_promo):
                    $promo_product = Product::findOrFail($inventory->product_id);
                    foreach ($promo_product->product_promoproduct as $promo):
                        $club_promo_inv = ProductInventory::where('club_id', '=', $club)
                            ->where('product_id', '=', $promo->product_id)
                            ->first();

                        if (!empty($club_promo_inv)):
                            // Update inventory club ybs
                            $update_club_promo_inv = ProductInventory::findOrFail($club_promo_inv->id);
                            $update_club_promo_inv->qty = $update_club_promo_inv->qty + ($request->qty_each * $promo->qty);
                            $update_club_promo_inv->latest_update_date = Carbon::now();
                            $update_club_promo_inv->save();
                        else:
                            // Insert club inv
                            $update_club_promo_inv = new ProductInventory;
                            $update_club_promo_inv->club_id = $club;
                            $update_club_promo_inv->product_id = $promo->product_id;
                            $update_club_promo_inv->selling_price = 0;
                            $update_club_promo_inv->qty = $request->qty_each * $promo->qty;
                            $update_club_promo_inv->latest_update_date = Carbon::now();
                            $update_club_promo_inv->save();
                        endif;
                    endforeach;
                endif;

                // Create log inventory transfer
                $log_inv = new LogProductInventoryTransfer;
                $log_inv->product_inventory_id = $update_club_inv->id;
                $log_inv->qty = $request->qty_each;
                $log_inv->from_club = $inventory->club_id;
                $log_inv->to_club = $club;
                $log_inv->update_date = $update_club_inv->latest_update_date;
                $log_inv->save();
            endforeach;

            // Update inventory club yg mentransfer
            $inventory->qty = $inventory->qty - (count($clubs) * $request->qty_each);
            $inventory->latest_update_date = Carbon::now();
            $inventory->save();

            // Update inventory is product is promo Added 19042020
            if ($inventory->productinv_product->is_promo):
                foreach ($inventory->productinv_product->product_promoproduct as $sender_promo):
                    $club_sender_promo_inv = ProductInventory::where('club_id', '=', $inventory->club_id)
                        ->where('product_id', '=', $sender_promo->product_id)
                        ->first();

                    if (!empty($club_sender_promo_inv)):
                        $update_club_sender_promo_inv = ProductInventory::findOrFail($club_sender_promo_inv->id);
                        $update_club_sender_promo_inv->qty = $update_club_sender_promo_inv->qty - ($request->qty_each * $sender_promo->qty);
                        $update_club_sender_promo_inv->latest_update_date = Carbon::now();
                        $update_club_sender_promo_inv->save();
                    endif;
                endforeach;
            endif;

            return response()->json($inventory);
        endif;

        return response()->json('failed');
    }

    // PROMO PRODUCT
    public function add_promo_product() {
        $products = Product::where('status', '=', 'Active')
            ->where('is_promo', '=', false)
            ->get();

        $categories = ProductTypes::orderBy('type', 'asc')->pluck('type', 'id');

        return view('cf_lite.product.promo.add-promo-product', compact('products', 'categories'));
    }

    public function store_promo_product(AddBulkProductPromoRequest $request) {
        $items = $request->item;
        $qtys = $request->qty;

        $product = new Product;
        $product->product_type_id = $request->product_type;
        $product->item_name = $request->promo_name;
        $product->default_price = 0;
        $product->is_taxable = 'Yes';
        $product->accessibility = 'Retail';
        $product->status = 'Active';
        $product->is_promo = true;
        if ($product->save()) {
            foreach ($items as $key => $value) {
                $promoProduct = new PromoProduct;
                $promoProduct->promo_id = $product->id;
                $promoProduct->product_id = $items[$key];
                $promoProduct->qty = $qtys[$key];
                $promoProduct->save();
            }

            Session::flash('cf_back.flash_message', trans('messages.product_promo_has_been_added'));
            return redirect('cf-lite/master-product');
        }
    }

    public function product_inventory_history($club_id, $product_id){
        $logs = LogProductInventory::where('club_id',$club_id)->where('product_id',$product_id)->get();

        return view('cf_lite.product.product-inventory-log', compact('logs','club_id'));
    }

    public function update_promo_product_ajax(Request $request) {
        $promo = PromoProduct::findOrFail($request->promoid);
        $promo->product_id = $request->newitem;
        $promo->qty = $request->qty;
        $promo->save();

        return response()->json($promo);
    }
    // END PROMO PRODUCT
}
