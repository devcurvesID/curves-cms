<?php

namespace App\Http\Controllers;

use App\AutoNumber;
use App\Http\Requests\UpdateBulkQTYConsignmentOrderDetail;
use App\Model\Club\ClubInfo;
use App\Model\HQ\ConsignmentOrder;
use App\Model\HQ\ConsignmentOrderBill;
use App\Model\HQ\ConsignmentOrderBillDetail;
use App\Model\HQ\ConsignmentOrderBillPayment;
use App\Model\HQ\ConsignmentOrderDetail;
use App\Model\Jubilant\Jubilant;
use App\Model\MasterData\Bank;
use App\Model\MasterData\PaymentMethod;
use App\Model\Product\ProductInventory;
use App\Model\Product\ProductPayment;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;

class ConsignmentOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('wm-only');
        $this->middleware('hq');
    }

    public function index() {
        $cos = ConsignmentOrder::orderBy('order_date', 'DESC')->get();

        return view('cf_lite.hqs.consignment-order', compact('cos'));
    }

    public function create_order() {
        $products = ProductInventory::where('club_id', '=', Auth::user()->club_id)
            ->whereHas('productinv_product', function ($q) {
                $q->where('status', '=', 'Active');
            })->get();

        $clubs = ClubInfo::where('jubilant_id', '<>', Auth::user()->user_club->jubilant_id)
            ->whereHas('club_jubilant', function ($jubilant) {
                $jubilant->where('type', '=', 'Franchise');
            })
            ->get();

        $staffs = User::where('club_id', '=', Auth::user()->club_id)
            ->where('role_id', '<>', '7')
            ->whereHas('user_memberstatus', function ($q) {
                $q->where('status', '<>', 'Inactive')
                    ->where('status', '<>', 'Stopped');
            })
            ->orderBy('name', 'asc')->get();

        return view('cf_lite.hqs.create-consignment-order', compact('products', 'clubs', 'staffs'));
    }

    public function store_order(Request $request) {
//        return json_decode($request->item_list);
        // Consignment Order
        $co = new ConsignmentOrder;
        $co->club_dest_id = $request->buyer;
        $co->hq_from_id = Auth::user()->club_id;
        $co->staff_id = $request->staff;
        $co->order_date = $request->order_date;
        $co->subtotal = $request->subtotal;
        $co->discount = $request->invoice_discount;
        $co->total = $request->billing_total;

        // Order Number
        $temp_auto_number = AutoNumber::where('name', '=', 'ConsOrder')->first();
        $new_auto_number = AutoNumber::findOrFail($temp_auto_number->id);
        $new_auto_number->current_number = $new_auto_number->current_number + 1;
        $new_auto_number->save();

        $month_roman = array(1=>'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');
        $payment_number = sprintf('%08d', $temp_auto_number->current_number).'/CO/CIND/'.sprintf('%03d', \Illuminate\Support\Facades\Auth::user()->user_club->jubilant_id).'/'.$month_roman[\Carbon\Carbon::now()->format('n')].'/'.\Carbon\Carbon::now()->format('Y');
        $co->order_number = $payment_number;

        $co->save();

        // CO Detail
        if (count(json_decode($request->item_list)) > 0):
            foreach (json_decode($request->item_list) as $key => $value):
                // Create CO Detail
                $co_detail = new ConsignmentOrderDetail;
                $co_detail->consignment_order_id = $co->id;

                $product = ProductInventory::findOrFail($value->prid);
                $co_detail->product_id = $product->product_id;

                $co_detail->qty = $value->qty;
                $co_detail->price_per_pcs = $value->price;
                $co_detail->save();

                // Update Stock
                $product->qty = $product->qty - $value->qty;
                $product->save();
            endforeach;
        endif;

        // Masuk Jurnal Akunting
        app('App\Http\Controllers\TempJournalController')->add_co_credit($co, false);

        return redirect('cf-lite/consignment-order/detail/'.$co->id);
    }

    public function update_consignment_order_detail(UpdateBulkQTYConsignmentOrderDetail $request, $id){
        $qtys = $request->qty;
        $temp_subtotal = 0;

        foreach ($request->id as $key => $value) {
            $co_detail = ConsignmentOrderDetail::findOrFail($value);
            $product = ProductInventory::findOrFail($co_detail->product_id);

            $product_qty_temp = $product->qty + $co_detail->qty;

            $co_detail->qty = $qtys[$key];
            $temp_subtotal = $temp_subtotal + ($co_detail->qty * $co_detail->price_per_pcs);
            $co_detail->save();

            //update stock
            $product->qty = $product_qty_temp - $co_detail->qty;
            $product->save();
        }

        //update subtotal dan total di consignment order
        $co = ConsignmentOrder::findOrFail($id);

        // Added 08012020
        $temp_co = ConsignmentOrder::findOrFail($id);

        if (!empty($request->order_date)){
            $co->order_date = $request->order_date;
        }
        $co->subtotal = $temp_subtotal;
        $co->total = $temp_subtotal - $co->discount;
        $co->save();

        // Masuk Jurnal Akunting
        app('App\Http\Controllers\TempJournalController')->edit_co_credit($temp_co, $co);

        return redirect('cf-lite/consignment-order/detail/'.$id);
    }

    public function order_detail($id) {
        $co = ConsignmentOrder::findOrFail($id);

        return view('cf_lite.hqs.consignment-order-detail', compact('co'));
    }

    public function delivery_order($id) {
        $co = ConsignmentOrder::findOrFail($id);
        return view('cf_lite.hqs.delivery-order', compact('co'));
    }

    public function delivery_order_ajax(Request $request) {
        $co = ConsignmentOrder::findOrFail($request->consignment_id);
        if ($co->delivery_order == false) {
            $co->delivery_order = true;
            $co->save();
        }
        return response()->json($co);
    }
	
	public function bill_index(Request $request) {
        $co_bills = ConsignmentOrderBill::where('status', '<>', 'Paid');

        // Filter Date
        if (!empty($request->from_date)):
            $co_bills->whereDate('co_bill_date', '>=', Carbon::parse($request->from_date)->toDateString());
        endif;

        if (!empty($request->thru_date)):
            $co_bills->whereDate('co_bill_date', '<=', Carbon::parse($request->thru_date)->toDateString());
        endif;

        // Filter Jubilant
        if (!empty($request->jubilant)):
            $co_bills->whereHas('cobill_club', function ($jubilant) use ($request) {
                $jubilant->where('jubilant_id', '=', $request->jubilant);
            });
        endif;

        $co_bills = $co_bills->orderBy('co_bill_due_date', 'ASC')->get();

        $jubilants = Jubilant::where('type', '=', 'Franchise')
            ->pluck('jubilant_name', 'id');

        return view('cf_lite.hqs.consignment-order-bill', compact('co_bills', 'jubilants'));
    }

    public function pre_generate_bill(Request $request) {
        $co_bills = ProductPayment::where('is_billed', '=', false)
            ->where('is_reward', '=', false);

        // Filter Date
        if (!empty($request->from_date)):
            $co_bills->whereDate('payment_date', '>=', Carbon::parse($request->from_date)->toDateString());
        endif;

        if (!empty($request->thru_date)):
            $co_bills->whereDate('payment_date', '<=', Carbon::parse($request->thru_date)->toDateString());
        endif;

        // Filter Jubilant
        if (!empty($request->jubilant)):
            $co_bills->whereHas('productpayment_club', function ($jubilant) use ($request) {
                $jubilant->where('jubilant_id', '=', $request->jubilant);
            });
        endif;

        $co_bills = $co_bills->orderBy('payment_date', 'ASC')->get();

        $jubilants = Jubilant::where('type', '=', 'Franchise')
            ->pluck('jubilant_name', 'id');

        return view('cf_lite.hqs.consignment-order-generate-bill', compact('co_bills', 'jubilants'));
    }

    public function generate_bill(Request $request) {
        $this->validate($request, [
            'co_bill_date' => 'required|date',
            'co_bill_due_date' => 'required|date',
            'total' => 'required|numeric|min:1',
            'jbl' => 'required|exists:mst_jubilants,id',
        ]);

        // Create CO Bill
        $cob = new ConsignmentOrderBill;
        $cob->hq_from_id = Auth::user()->club_id;
        $cob->jubilant_id = $request->jbl;
        $cob->staff_id = Auth::id();
        $cob->co_bill_date = $request->co_bill_date;
        $cob->co_bill_due_date = $request->co_bill_due_date;
        $cob->total = $request->total;
        $cob->rest_of_bill = $request->total;

        // Order Number
        $temp_auto_number = AutoNumber::where('name', '=', 'HQCredit')->first();
        $new_auto_number = AutoNumber::findOrFail($temp_auto_number->id);
        $new_auto_number->current_number = $new_auto_number->current_number + 1;
        $new_auto_number->save();

        $month_roman = array(1=>'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');
        $payment_number = sprintf('%08d', $temp_auto_number->current_number).'/PIU/CIND/'.sprintf('%03d', \Illuminate\Support\Facades\Auth::user()->user_club->jubilant_id).'/'.$month_roman[\Carbon\Carbon::now()->format('n')].'/'.\Carbon\Carbon::now()->format('Y');
        $cob->co_bill_number = $payment_number;
        $cob->save();

        // Get Product Payment
        $co_bills = ProductPayment::where('is_billed', '=', false)
            ->where('is_reward', '=', false);

        // Filter Date
        if (!empty($request->f_date)):
            $co_bills->whereDate('payment_date', '>=', Carbon::parse($request->f_date)->toDateString());
        endif;

        if (!empty($request->t_date)):
            $co_bills->whereDate('payment_date', '<=', Carbon::parse($request->t_date)->toDateString());
        endif;

        // Filter Jubilant
        if (!empty($request->jbl)):
            $co_bills->whereHas('productpayment_club', function ($jubilant) use ($request) {
                $jubilant->where('jubilant_id', '=', $request->jbl);
            });
        endif;

        $co_bills = $co_bills->get();
        foreach ($co_bills as $co_bill):
            // Create CO Bill Details
            $co_detail = new ConsignmentOrderBillDetail;
            $co_detail->consignment_order_bill_id = $cob->id;
            $co_detail->product_payment_id = $co_bill->id;
            $co_detail->save();

            // Update is_billed
            $product = ProductPayment::findOrFail($co_bill->id);
            $product->is_billed = true;
            $product->save();
        endforeach;

        return redirect('cf-lite/consignment-order/bill/detail/'.$cob->id);
    }

    public function bill_detail($id) {
        $cob = ConsignmentOrderBill::findOrFail($id);

        $staffs = User::where('club_id','=',Auth::user()->club_id)
            ->where(function ($role) {
                $role->where('role_id', '<', '4')
                    ->orWhere('role_id', '=', '8');
            })
            ->pluck('name','id');

        $payment_methods = PaymentMethod::availableForPayment()->pluck('payment_method', 'id');

        $banks = Bank::where('id', '<>', '1')
            ->whereHas('bank_chartofaccount', function ($coa) {
                $coa->where('jubilant_id', '=', Auth::user()->user_club->jubilant_id);
            })
            ->pluck('bank_name', 'id');

        return view('cf_lite.hqs.consignment-order-bill-detail', compact('cob', 'staffs', 'payment_methods', 'banks'));
    }

    public function store_bill_payment($id, Request $request) {
        $this->validate($request, [
            'py_staff' => 'required|exists:users,id',
            'payment_date' => 'required|date',
            'payment_method' => 'required|exists:mst_payment_methods,id',
            'bank' => 'sometimes',
            'py_information' => 'sometimes',
            'py_subtotal' => 'required|numeric',
        ]);

        // Cek COB
        $cob = ConsignmentOrderBill::findOrFail($id);

        // Create HQ CO Bill Payment
        $cob_payment = new ConsignmentOrderBillPayment;
        $cob_payment->consignment_order_bill_id = $cob->id;
        $cob_payment->payment_date = $request->payment_date;
        $cob_payment->total = $request->py_subtotal;
        $cob_payment->staff_id = $request->py_staff;

        if (!empty($request->py_information)):
            $cob_payment->information = $request->py_information;
        endif;

        $cob_payment->payment_type_id = $request->payment_method;

        if ($request->payment_method == 1):
            $cob_payment->bank_id = 1;
        else:
            if (!empty($request->bank)):
                $cob_payment->bank_id = $request->bank;
            else:
                $cob_payment->bank_id = 1;
            endif;
        endif;

        // Generate Payment Number
        $temp_auto_number = AutoNumber::where('name', '=', 'HQCreditPayment')->first();
        $new_auto_number = AutoNumber::findOrFail($temp_auto_number->id);
        $new_auto_number->current_number = $new_auto_number->current_number + 1;
        $new_auto_number->save();

        $month_roman = array(1=>'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');
        $payment_number = sprintf('%08d', $temp_auto_number->current_number).'/PIU/CIND/'.sprintf('%03d', \Illuminate\Support\Facades\Auth::user()->user_club->jubilant_id).'/'.$month_roman[\Carbon\Carbon::now()->format('n')].'/'.\Carbon\Carbon::now()->format('Y');
        $cob_payment->payment_number = $payment_number;

        if ($cob_payment->save()):
            $cob->amount_paid = $cob->amount_paid + $request->py_subtotal;
            $cob->rest_of_bill = $cob->rest_of_bill - $request->py_subtotal;

            if ($cob->rest_of_bill <= 0):
                $cob->status = 'Paid';
            else:
                if ($cob->rest_of_bill < $cob->total):
                    $cob->status = 'Halfpaid';
                else:
                    $cob->status = 'Unpaid';
                endif;
            endif;

            $cob->save();

            // Masuk Jurnal Akunting
            app('App\Http\Controllers\TempJournalController')->add_co_credit_payment($cob_payment, false);
        endif;

        Session::flash('flash_message', trans('messages.hq_credit_payment_has_been_successfully_added'));
        return redirect('cf-lite/consignment-order/bill/detail/'.$cob->id);
    }

    public function product_payment_ajax(Request $request) {
        $cob = ConsignmentOrderBillPayment::findOrFail($request->cp_id);

        return response()->json($cob);
    }

    public function update_bill_payment(Request $request) {
        $this->validate($request, [
            'cp_id' => 'required|exists:hq_credit_payments,id',
            'edit_py_staff' => 'required|exists:users,id',
            'edit_payment_date' => 'required|date',
            'edit_payment_method' => 'required|exists:mst_payment_methods,id',
            'edit_bank' => 'sometimes',
            'edit_py_information' => 'sometimes',
            'edit_py_subtotal' => 'required|numeric',
        ]);

        $diff = 0;

        // Update CO Bill Payment
        $cp = ConsignmentOrderBillPayment::findOrFail($request->cp_id);
        $temp_cp = ConsignmentOrderBillPayment::findOrFail($request->cp_id);

        $cp->staff_id = $request->edit_py_staff;
        $cp->payment_date = $request->edit_payment_date;
        $cp->payment_type_id = $request->edit_payment_method;

        if ($request->edit_payment_method == 1):
            $cp->bank_id = 1;
        else:
            if (!empty($request->edit_bank)):
                $cp->bank_id = $request->edit_bank;
            else:
                $cp->bank_id = 1;
            endif;
        endif;

        $cp->information = $request->edit_py_information;

        if ($cp->total != $request->edit_py_subtotal):
            $diff = $request->edit_py_subtotal - $cp->total;
            $cp->total = $request->edit_py_subtotal;
        endif;

        $cp->save();

        // Update CO Bill
        if ($diff != 0):
            $cob = ConsignmentOrderBill::findOrFail($cp->consignment_order_bill_id);
            $cob->amount_paid = $cob->amount_paid + $diff;
            $cob->rest_of_bill = $cob->rest_of_bill - $diff;

            if ($cob->rest_of_bill <= 0):
                $cob->status = 'Paid';
            else:
                if ($cob->rest_of_bill < $cob->total):
                    $cob->status = 'Halfpaid';
                else:
                    $cob->status = 'Unpaid';
                endif;
            endif;

            $cob->save();
        endif;

        // Masuk Jurnal Akunting
        app('App\Http\Controllers\TempJournalController')->edit_co_credit_payment($temp_cp, $cp);

        Session::flash('flash_message', trans('messages.hq_credit_payment_has_been_successfully_updated'));
        return redirect('cf-lite/consignment-order/bill/detail/'.$cp->consignment_order_bill_id);
    }
}
