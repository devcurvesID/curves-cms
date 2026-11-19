<?php

namespace App\Http\Controllers;

use App\Model\DigitalProduct\DigitalProductInventory;
use App\Model\Log\LogDigitalProductInventory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Model\DigitalProduct\DigitalProduct;

class DigitalProductController extends Controller
{
    // Digital Product
     public function digital_product_list(){
        $digital_products = DigitalProduct::where('deleted_date','=',null)->orderBy('item_name')->get();
        return view('cf_lite.digital-product.digital-product-list', compact('digital_products'));
    }
    public function digital_product_detail(Request $request){
        $digital_products = DigitalProduct::find($request->id);
        return response()->json($digital_products);
    }
    public function store_digital_product(Request $request){
        $digital_product = DigitalProduct::create([
            'item_name'=>$request->item_name,
            'default_price'=>$request->default_price,
            'notes'=>$request->notes,
        ]);
        DigitalProductInventory::create([
            'club_id'=>Auth::user()->user_club->id,
            'digital_product_id'=>$digital_product->id,
            'selling_price'=>$digital_product->default_price,
            'qty'=>1000,
            'status'=>FALSE,
        ]);
        return back();
    }
    public function update_digital_product(Request $request){
        DigitalProduct::find($request->id)
            ->update([
                'item_name'=>$request->item_name,
                'default_price'=>$request->default_price,
                'notes'=>$request->notes,
            ]);
        DigitalProductInventory::where('digital_product_id','=',$request->id)
            ->update([
                'selling_price'=>$request->default_price,
            ]);
        return back();
    }
    public function delete_digital_product(Request $request){
        $current_date_time = Carbon::now()->toDateTimeString();
        DigitalProduct::find($request->id)
            ->update([
                'deleted_date'=>$current_date_time,
            ]);
        return back();
    }

    // Digital Product Inventory

    public function digital_product_inventory_list() {
        $digitalProduct = DigitalProductInventory::where('club_id', '=', Auth::user()->club_id)
            ->where('status', '=', true)
            ->get();

        return view('cf_lite.digital-product.digital-product-inventory-list', compact('digitalProduct'));
    }

    public function update_digital_product_inventory(Request $request) {
        $updateDigitalProduct = DigitalProductInventory::findOrFail($request->digproduct_id);
        $tempQty = $updateDigitalProduct->qty;
        if (!empty($updateDigitalProduct)) {
            $updateDigitalProduct->selling_price = $request->selling_price;
            $updateDigitalProduct->qty = $request->qty;
            if ($request->status == 'Active') {
                $updateDigitalProduct->status = true;

                $affected = DB::table('digital_product_inventories')
                    ->where('club_id', '=', Auth::user()->id)
                    ->where('status', '=', true)
                    ->where('id', '<>', $request->digproduct_id)
                    ->update(array('status' => false));
            } else {
                $updateDigitalProduct->status = false;
            }

            if ($updateDigitalProduct->save()) {
                $logDigitalProduct = new LogDigitalProductInventory();
                $logDigitalProduct->digital_product_inventory_id = $updateDigitalProduct->id;
                $logDigitalProduct->user_id = Auth::user()->id;
                $logDigitalProduct->qty_before = $tempQty;
                $logDigitalProduct->qty_after = $request->qty;
                $logDigitalProduct->date = Carbon::now();
                $logDigitalProduct->save();
            }
        }

        $digitalProduct = DigitalProductInventory::where('club_id', '=', Auth::user()->club_id)
            ->orderBy('status', '=', true)
            ->get();

        return view('cf_lite.digital-product.digital-product-inventory-list', compact('digitalProduct'));
    }

    public function digital_product_inventory_detail(Request $request) {
        $digitalProduct = DigitalProductInventory::findOrFail($request->id);

        return response()->json($digitalProduct);
    }
}
