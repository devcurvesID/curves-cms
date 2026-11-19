<?php

namespace App\Http\Controllers;

use App\AutoNumber;
use App\Model\Locker\LockerPayment;
use App\Model\Locker\MemberLocker;
use App\Model\Log\LogMemberStatus;
use App\Model\MasterData\Bank;
use App\Model\MasterData\Locker;
use App\Model\MasterData\MembershipType;
use App\Model\MasterData\PaymentMethod;
use App\Model\Payment\MemberPayment;
use App\Model\Payment\MemberDebtPayment;
use App\Model\Product\ProductInventory;
use App\Model\Product\ProductPayment;
use App\Model\Product\ProductPaymentDetail;
use App\Model\Product\ProductRefund;
use App\Model\Product\ProductRefundInventory;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Session;

use App\AccurateAuth;
use App\Helpers\Accurate;
use stdClass;
use Storage;

require_once __DIR__.'/../../WebClientPrint/WebClientPrint.php';
use Neodynamic\SDK\Web\WebClientPrint;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('wm-only');
    }

    public function member_payment_invoice($id) {
        $member_payment = MemberPayment::findOrFail($id);
        $payment_methods = PaymentMethod::availableForPayment()->pluck('payment_method', 'id');
        $banks = Bank::where('id', '<>', 1)
            ->whereHas('bank_club', function ($q){
                $q->where('club_id', '=', Auth::user()->club_id);
            })->get();

        $membership_types = LogMemberStatus::where('user_id', '=', $member_payment->user_id)
            ->whereDate('created_at', '<=', Carbon::parse($member_payment->created_at)->toDateString())
            ->orderBy('created_at', 'desc')
            ->get();

        //Modified 13032018
        $the_membership_type = '';
        foreach ($membership_types as $membership_type):
            if (empty($the_membership_type)):
                $the_membership_type = $membership_type->membership_type_id;
            else:
                if (Carbon::parse($member_payment->created_at)->format('dmYhi') == Carbon::parse($membership_type->created_at)->format('dmYhi')):
                    $the_membership_type = $membership_type->membership_type_id;
                endif;
            endif;
        endforeach;

        $membership = MembershipType::findOrFail($the_membership_type);
        $user =
            User::where('club_id', '=', Auth::user()->club_id)
                ->where('role_id', '<>', '7')
                ->where(function ($q) {
                    $q->whereDoesntHave('user_staffinactive')
                        ->orWhere(function ($r) {
                            $r->whereHas('user_staffinactive', function ($s) {
                                $s->where('status', '=', 'Reactivated')
                                    ->where('thru_date', null);
                            });
                        });
                })
                ->orderBy('name', 'asc')->first();
        $staffs = $user->pluck('name', 'id');

        $wcpScript = WebClientPrint::createScript(action('WebClientPrintController@processRequest'), action('PrintESCPOSController@cashierInstallment'), Session::getId());

        //ACCURATE
        /* $accurate = AccurateAuth::find(1);

        $redirectURI = env('ACCURATE_REDIRECT_URI_MEMBERSHIP');
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
            if($openDB){
                $x = 1;

                while($x <= 100) {
                    $_accurateItems = Accurate::accurateCheckItems($openDB, 'SERVICE', $x, 100);

                    if($_accurateItems){
                        //do nothing
                        $accurateItems = array_replace_recursive($accurateItems, $_accurateItems);
                    }else{
                        break;
                    }
                    $x++;
                }
            }
        }

        return view('cf_lite.payments.invoice-view', compact('user','member_payment', 'membership', 'staffs', 'payment_methods', 'banks', 'wcpScript', 'accurateItems'));
        */
        return view('cf_lite.payments.invoice-view', compact('user','member_payment', 'membership', 'staffs', 'payment_methods', 'banks', 'wcpScript'));
    }

    public function member_payment_installment_invoice($id, Request $request) {
        $first_payment = MemberPayment::findOrFail($id);

        //Update Auto Number
        $temp_auto_number = AutoNumber::findOrFail('1');
        if ($request->id == 1):
            $payment_number = $temp_auto_number->current_number;
        else:
            $payment_number = $temp_auto_number->current_number - 1;
        endif;
        $month_roman = array(1 => 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');

        // Create new member payment (monthly)
        $monthly_payment = new MemberPayment;
        $monthly_payment->payment_number = sprintf('%08d', $payment_number) . '/PY/CIND/' . sprintf('%03d', $first_payment->memberpayment_user->club_id) . '/' . $month_roman[Carbon::now()->format('n')] . '/' . Carbon::now()->format('Y');
        $monthly_payment->user_id = $first_payment->user_id;
        $monthly_payment->sf_amount = 0;
        $monthly_payment->mf_amount = $request->i_amount;
        $monthly_payment->dc_amount = 0;
        $monthly_payment->tax_type_id = '1';
        $monthly_payment->tax_amount = round($request->i_amount / 11);
        $monthly_payment->tax_percentage = 10;
        $monthly_payment->amount_due = $request->i_amount;
        $monthly_payment->payment_category = 'Monthly';
        $monthly_payment->payment_date = $request->i_payment_date;
        $monthly_payment->payment_method_id = $request->i_payment_method;

        if ($request->i_payment_method == 1):
            $monthly_payment->bank_id = 1;
        else:
            $monthly_payment->bank_id = $request->i_bank;
        endif;

        $monthly_payment->amount_paid = $request->i_amount;
        $monthly_payment->rest_of_bill = 0;
        $monthly_payment->payment_status = 'Paid';
        $monthly_payment->renewal = true;
        $monthly_payment->save();

        // Update rest_of_bill First Payment
        $first_payment->rest_of_bill = $first_payment->rest_of_bill - $request->i_amount;
        $first_payment->save();

        // Amortization Update
        app('App\Http\Controllers\AccountingAmortizationController')->update_amortization($monthly_payment);

        // Accounting
        app('App\Http\Controllers\TempJournalController')->membership_monthly_payment($monthly_payment, false);


        //ACCURATE
        /* $user = User::find($first_payment->user_id);
        $userData = [
            'name' => str_replace("%20", " ", $user->name),
            'accurate_code' => $request->accurate_code,
            'fa_date' => $request->i_payment_date,
            'pay' => $request->i_amount,
        ]; */

        //$this->store_accurate($user->club_id, $userData);

        return redirect('cf-lite/payment/invoice/'.$monthly_payment->id.'/member');
    }

    public function edit_member_payment_invoice($id, Request $request) {
        $member_payment = MemberPayment::findOrFail($id);
        if ($member_payment->payment_category == 'First') {
            $temp_total = ($member_payment->sf_amount + $member_payment->mf_amount) - $request->discount;

            //Set minus value lama
            app('App\Http\Controllers\AccountingJournalController')->edit_membership_journal($member_payment, $request->membership_type);

            $member_payment->payment_date = Carbon::parse($request->date)->toDateTimeString();
            $member_payment->dc_amount = $request->discount;
            $member_payment->tax_type_id = '1';
            $member_payment->tax_amount = round($temp_total / 11);
            $member_payment->tax_percentage = 10;
            $member_payment->amount_due = $temp_total;
            if ($request->payment_method == 1) {
                $member_payment->payment_method_id = $request->payment_method;
                $member_payment->bank_id = 1;
            } else {
                $member_payment->payment_method_id = $request->payment_method;
                $member_payment->bank_id = $request->bank;
            }
            $member_payment->amount_paid = $request->pay;
            if (($temp_total - $request->pay) > 0):
                $member_payment->rest_of_bill = ($temp_total - $request->pay);
                if ($member_payment->memberpayment_user->user_memberstatus->payment_status == 'NotSet') {
                    $member_payment->payment_status = 'Halfpaid';
                }
            endif;

            $member_payment->save();

            //Add new accounting journal
            app('App\Http\Controllers\AccountingJournalController')->new_membership_journal($member_payment, $request->membership_type);

        } else if ($member_payment->payment_category == 'Monthly') {
            //Set minus value lama
            app('App\Http\Controllers\AccountingJournalController')->edit_membership_journal($member_payment, $request->membership_type);

            $member_payment->payment_date = Carbon::parse($request->payment_date_temp)->toDateTimeString();
            $member_payment->mf_amount = $request->bill;
            $member_payment->tax_amount = round($request->bill / 11);
            $member_payment->amount_due = $request->bill;
            $member_payment->amount_paid = $request->bill;

            $member_payment->save();

            //Add new accounting journal
            app('App\Http\Controllers\AccountingJournalController')->new_membership_journal($member_payment, $request->membership_type);


        }

        return redirect('cf-lite/payment/paid-list/'.$member_payment->user_id);
    }

    /*****************************/
    /**** ACCURATE SCRIPT ***/
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

            //CHECK IF USER EXIST IN ACCURATE
            //IF USER DO NOT EXIST, CREATE USING THE API
            $accurateUser = Accurate::AccurateCheckMember($openDB, $userData);
            if($accurateUser){
                //do nothing
            }else{
                $accurateUser = Accurate::AccurateCreateMember($openDB, $userData);
            }

            $accurateItem = $userData['accurate_code'];

            $invoiceData = [
                "customerNo" => $accurateUser['customerNo'],
                "transDate" => date("d/m/Y"),
                "branchId" => $branchID,
                "detailItem" => array(
                    array(
                        "itemNo" => $accurateItem,
                        "quantity" => 1,
                        "unitPrice" => $userData['pay']
                        )
                    )
            ];

            //CREATE SALES INVOICE ONE CUSTOMER & ITEM IS TRUE
            $accurateInvoice = Accurate::AccurateCreateInvoice($openDB, $invoiceData);
            Storage::put('invoices/'.date("d-m-Y").'_'.$accurateUser['customerNo'].'_installment.txt', json_encode($accurateInvoice));
        }

    }

    public function product_invoice_list() {
        $product_payments = ProductPayment::where('club_id','=',Auth::user()->club_id)->orderBy('payment_date','desc')->get();
        $payment_methods = PaymentMethod::get();
        $banks = Bank::where('id', '<>', 1)
            ->whereHas('bank_club', function ($q){
                $q->where('club_id', '=', Auth::user()->club_id);
            })->get()->pluck('pretty_bank','id');
        $users = User::where('club_id','=',Auth::user()->club_id)->pluck('name','id');

        return view('cf_lite.document.product-invoice.product-invoice-list', compact('product_payments', 'payment_methods', 'banks','users'));
    }

    public function invoice_list_filter(Request $request){
        if (!empty($request->payment_number)) {
            $product_payments = ProductPayment::where('club_id', '=', Auth::user()->club_id)->where('payment_number', 'like', '%' . $request->payment_number . '%')->orderBy('payment_date', 'desc')->get();
            $payment_methods = PaymentMethod::get();
            $banks = Bank::where('id', '<>', 1)
                ->whereHas('bank_club', function ($q){
                    $q->where('club_id', '=', Auth::user()->club_id);
                })->get();
            $users = User::where('club_id', '=', Auth::user()->club_id)->pluck('name', 'id');
            return view('cf_lite.document.product-invoice.product-invoice-list', compact('product_payments', 'payment_methods', 'banks','users'));
        } else {
            return redirect('cf-lite/invoice-list');
        }
    }

    public function invoice_detail_list($id){
        $invoice = ProductPayment::findOrFail($id);
    	$invoice_details = ProductPaymentDetail::where('product_payment_id','=',$id)->get();
        $product_refunds = ProductRefund::where('product_payment_id','=',$id)->get();

        $wcpScript = WebClientPrint::createScript(action('WebClientPrintController@processRequest'), action('PrintESCPOSController@reprintProduct'), Session::getId());

        return view('cf_lite.document.product-invoice.product-invoice-detail', compact('invoice', 'invoice_details','id','product_refunds', 'wcpScript'));
    }

    public function product_invoice_refund_inventory_list(){
        $product_refunds = ProductRefund::whereHas('productrefund_productpayment',function($q){
            $q->where('club_id','=',Auth::user()->club_id);
        })->orderBy('refund_date','desc')->get();

        return view('cf_lite.document.product-invoice.product-invoice-refund-list', compact('product_refunds'));
    }

    public function invoice_refund_inventory_list_filter(Request $request){
        $product_refunds = ProductRefund::where('refund_number','like','%'.$request->refund_number.'%')->whereHas('productrefund_productpayment',function($q){
            $q->where('club_id','=',Auth::user()->club_id);
        })->orderBy('refund_date','desc')->get();

        return view('cf_lite.document.product-invoice.product-invoice-refund-list', compact('product_refunds'));
    }

    public function invoice_refund_inventory_detail_list($id){
        $product_refund_inventory = ProductRefundInventory::where('product_refund_id','=',$id)->get();

        return view('cf_lite.document.product-invoice.product-invoice-refund-detail', compact('product_refund_inventory','id'));
    }

    public function update_invoice(Request $request) {
        $temp = ProductPayment::findOrFail($request->payment_id);
        $invoice = ProductPayment::findOrFail($request->payment_id);
        if (!empty($invoice)) {
            $invoice->payment_date = $request->edit_invoice_date;
            $invoice->member_id = empty($request->buyer) ? null : $request->buyer;
            $invoice->payment_method_id = $request->payment_method;
            $invoice->pay = $request->pay;
            $invoice->change = $request->pay - $invoice->total;
            $invoice->bank_id = $request->bank != '' ? $request->bank : 1;
            if ($invoice->save()) {
                app('App\Http\Controllers\TempJournalController')->edit_invoice_product_payment($temp, $invoice);
            }
        }

        return redirect('cf-lite/invoice-detail-list/'.$invoice->id);
    }

    public function locker_invoice_list(Request $request){
        if(isset($request->payment_number)){
            $locker_payments = LockerPayment::where('payment_number','ILIKE','%'.$request->payment_number.'%')->whereHas('lockerpayment_memberlocker', function ($q) {
                $q->whereHas('memberlocker_lockerinfo', function ($w) {
                    $w->where('club_id', Auth::user()->club_id);
                });
            })->orderBy('payment_date', 'desc')->get();
        }else{
            $locker_payments = LockerPayment::whereHas('lockerpayment_memberlocker', function ($q) {
                $q->whereHas('memberlocker_lockerinfo', function ($w) {
                    $w->where('club_id', Auth::user()->club_id);
                });
            })->orderBy('payment_date', 'desc')->get();
        }
        $payment_methods = PaymentMethod::get();
        $banks = Bank::where('id', '<>', 1)
            ->whereHas('bank_club', function ($q) {
                $q->where('club_id', '=', Auth::user()->club_id);
            })->get()->pluck('pretty_bank', 'id');
        $users = User::where('club_id', '=', Auth::user()->club_id)->pluck('name', 'id');

        return view('cf_lite.document.locker-invoice.locker-invoice-list', compact('locker_payments', 'payment_methods', 'banks', 'users'));
    }

    public function locker_invoice_detail_list($id){
        $invoice = LockerPayment::findOrFail($id);

        $wcpScript = WebClientPrint::createScript(action('WebClientPrintController@processRequest'), action('PrintESCPOSController@reprintLocker'), Session::getId());

        return view('cf_lite.document.locker-invoice.locker-invoice-detail', compact('invoice', 'id', 'wcpScript'));
    }

    public function update_locker_invoice(Request $request)
    {
        info($request);
        DB::beginTransaction();

        try{
            $invoice = LockerPayment::findOrFail($request->payment_id);
            $member_locker = MemberLocker::findOrFail($invoice->memberlocker_id);

            $invoice->payment_date = $request->edit_invoice_date;
            $invoice->paymentmethod_id = $request->payment_method;
            $invoice->pay = $request->pay;
            $invoice->bank_id = $request->bank != '' ? $request->bank : 1;
            $invoice->save();

            $member_locker->user_id = empty($request->buyer) ? null : $request->buyer;
            $member_locker->save();

            // app('App\Http\Controllers\TempJournalController')->edit_invoice_product_payment($temp, $invoice);
            DB::commit();
        }catch(Exception $e){
            Log::error($e);
            DB::rollBack();
        }

        return redirect('cf-lite/locker-invoice-detail-list/' . $invoice->id);
    }

    public function product_invoice_detail_ajax(Request $request) {
        $invoice = ProductPayment::with('productpayment_productpaymentdetail', 'productpayment_user')
            ->where('id', '=', $request->id)
            ->first();

        $productinv = ProductInventory::with('productinv_product')
            ->where('club_id', '=', Auth::user()->club_id)
            ->where('qty', '>', 0)
            ->orWhere(function ($q) use ($invoice) {
                foreach ($invoice->productpayment_productpaymentdetail as $detail) {
                    $q->where('product_id', '=', $detail->product_id);
                }
            })
            ->get();

        return response()->json(['product_payment' => $invoice, 'product_inventory' => $productinv], 200);
    }

    public function locker_invoice_detail_ajax(Request $request){
        $invoice = LockerPayment::with('lockerpayment_memberlocker', 'lockerpayment_user', 'lockerpayment_locker')
                    ->where('id',$request->id)->first();

        $member_locker = MemberLocker::findOrFail($invoice->memberlocker_id);

        $locker_packages = Locker::where('club_id','=',Auth::user()->club_id)->get();

        return response()->json(['locker_payment' => $invoice, 'member_locker' => $member_locker, 'locker_packages' => $locker_packages], 200);
    }

    public function store_product_invoice_detail_ajax(Request $request) {
        $inv_detail = ProductPaymentDetail::findOrFail($request->invoice_id);
        $productinv = ProductInventory::where('club_id', '=', Auth::user()->club_id)->where('product_id', '=', $request->newproduct_id)->first();

        if (!empty($inv_detail)) {
            if ($inv_detail->product_id != $request->newproduct_id) {
                //Tambah stok barang lama
                $oldproduct = ProductInventory::where('product_id', '=', $inv_detail->product_id)->first();
                if (!empty($oldproduct)) {
                    $oldproduct->qty = $oldproduct->qty + $inv_detail->qty;
                    $oldproduct->save();
                }
                //Kurangin stok barang baru
                $productinv->qty = $productinv->qty - $inv_detail->qty;
                $productinv->save();

                $inv_detail->product_id = $productinv->product_id;
                $inv_detail->save();
            }
        }

        return response()->json(['invoice_detail' => $inv_detail, 'product_inv' => $productinv]);
    }
}
