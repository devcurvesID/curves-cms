<?php

namespace App\Http\Controllers;

use App\AutoNumber;
use App\Model\Accounting\ChartOfAccount;
use App\Model\HQ\HQCredit;
use App\Model\HQ\HQCreditPayment;
use App\Model\Jubilant\Jubilant;
use App\Model\MasterData\Bank;
use App\Model\MasterData\PaymentMethod;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;

class HQController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('wm-only');
        $this->middleware('hq');
    }

    public function credit_index(Request $request) {
        $hq_credits = HQCredit::where('jubilant_id', '<>', Auth::user()->user_club->jubilant_id);

        // Filter Date
        if (!empty($request->from_date)):
            $hq_credits->whereDate('invoice_date', '>=', Carbon::parse($request->from_date)->toDateString());
        endif;

        if (!empty($request->thru_date)):
            $hq_credits->whereDate('invoice_date', '<=', Carbon::parse($request->thru_date)->toDateString());
        endif;

        // Filter COA
        if (!empty($request->coa)):
            $hq_credits->where('coa_id', '=', $request->coa);
        endif;

        // Filter Jubilant
        if (!empty($request->jubilant)):
            $hq_credits->where('jubilant_id', '=', $request->jubilant);
        endif;

        // Filter Status
        if (!empty($request->status)):
            $hq_credits->where('credit_status', '=', $request->status);
        endif;

        $hq_credits = $hq_credits->orderBy('invoice_date', 'desc')->get();

        // Option COA & Jubilant
        $coas = ChartOfAccount::where('jubilant_id', '=', Auth::user()->user_club->jubilant_id)
            ->where('coa_number', 'ilike', '%1-121%')
            ->where('coa_number', '<>', '1-1210')
            ->where('coa_name', '<>', 'Piutang Produk Investor')
            ->orWhere('coa_number', '=', '1-1299')
            ->pluck('coa_name', 'id');

        $jubilants = Jubilant::where('type', '=', 'Franchise')
            ->pluck('jubilant_name', 'id');

        return view('cf_lite.hqs.credit-index', compact('hq_credits', 'coas', 'jubilants'));
    }

    public function create_credit() {
        $staffs = User::where('club_id','=',Auth::user()->club_id)
            ->where(function ($role) {
                $role->where('role_id', '<', '4')
                    ->orWhere('role_id', '=', '8');
            })
            ->pluck('name','id');

        $jubilants = Jubilant::where('type', '=', 'Franchise')
            ->pluck('jubilant_name', 'id');

        $coas = ChartOfAccount::where('jubilant_id', '=', Auth::user()->user_club->jubilant_id)
            ->where('coa_number', 'ilike', '%1-121%')
            ->where('coa_number', '<>', '1-1210')
            ->where('coa_name', '<>', 'Piutang Produk Investor')
            ->orWhere('coa_number', '=', '1-1299')
            ->pluck('coa_name', 'id');

        $temp_auto_number = AutoNumber::where('name', '=', 'HQCredit')->first();

        return view('cf_lite.hqs.add-credit', compact('staffs', 'jubilants', 'temp_auto_number', 'coas'));
    }

    public function store_credit(Request $request) {
        $this->validate($request, [
            'invoice_number' => 'required|string',
            'staff' => 'required|exists:users,id',
            'invoice_date' => 'required|date',
            'jubilant' => 'required|exists:mst_jubilants,id',
            'coa' => 'required|exists:chart_of_accounts,id',
            'information' => 'sometimes',
            'subtotal' => 'required|numeric',
        ]);

        $hq_credit = new HQCredit;
        $hq_credit->staff_id = $request->staff;
        $hq_credit->jubilant_id = $request->jubilant;
        $hq_credit->invoice_date = $request->invoice_date;
        $hq_credit->coa_id = $request->coa;

        if (!empty($request->information)):
            $hq_credit->information = $request->information;
        endif;

        $hq_credit->subtotal = $request->subtotal;
        $hq_credit->rest_of_bill = $request->subtotal;

        $temp_auto_number = AutoNumber::where('name', '=', 'HQCredit')->first();
        $new_auto_number = AutoNumber::findOrFail($temp_auto_number->id);
        $new_auto_number->current_number = $new_auto_number->current_number + 1;
        $new_auto_number->save();

        $invoice_number = sprintf('%08d', $temp_auto_number->current_number).$request->invoice_number;
        $hq_credit->invoice_number = $invoice_number;

        $hq_credit->save();

        // Masuk ke Jurnal Akunting
        app('App\Http\Controllers\TempJournalController')->add_hq_credit($hq_credit, false);

        Session::flash('flash_message', trans('messages.hq_credit_has_been_successfully_created'));
        return redirect('cf-lite/hq/add-credit');
    }

    public function credit_detail($id) {
        $hq_credit = HQCredit::findOrFail($id);

        $staffs = User::where('club_id','=',Auth::user()->club_id)
            ->where(function ($role) {
                $role->where('role_id', '<', '4')
                    ->orWhere('role_id', '=', '8');
            })
            ->pluck('name','id');

        $jubilants = Jubilant::where('type', '=', 'Franchise')
            ->pluck('jubilant_name', 'id');

        $coas = ChartOfAccount::where('jubilant_id', '=', Auth::user()->user_club->jubilant_id)
            ->where('coa_number', 'ilike', '%1-121%')
            ->where('coa_number', '<>', '1-1210')
            ->where('coa_name', '<>', 'Piutang Produk Investor')
            ->orWhere('coa_number', '=', '1-1299')
            ->pluck('coa_name', 'id');

        $payment_methods = PaymentMethod::availableForPayment()->pluck('payment_method', 'id');

        $banks = Bank::where('id', '<>', '1')
            ->whereHas('bank_chartofaccount', function ($coa) {
                $coa->where('jubilant_id', '=', Auth::user()->user_club->jubilant_id);
            })
            ->pluck('bank_name', 'id');

        return view('cf_lite.hqs.credit-detail', compact('hq_credit', 'staffs', 'jubilants', 'coas', 'payment_methods', 'banks'));
    }

    public function update_credit($id, Request $request) {
        $this->validate($request, [
            'staff' => 'required|exists:users,id',
            'invoice_date' => 'required|date',
            'jubilant' => 'required|exists:mst_jubilants,id',
            'coa' => 'required|exists:chart_of_accounts,id',
            'information' => 'sometimes',
            'subtotal' => 'required|numeric',
        ]);

        $hq_credit = HQCredit::findOrFail($id);

        // Added 16122019
        $temp_hq_credit = HQCredit::findOrFail($id);

        $hq_credit->staff_id = $request->staff;
        $hq_credit->jubilant_id = $request->jubilant;
        $hq_credit->invoice_date = $request->invoice_date;
        $hq_credit->coa_id = $request->coa;

        if (!empty($request->information)):
            $hq_credit->information = $request->information;
        endif;

        $hq_credit->subtotal = $request->subtotal;
        $hq_credit->rest_of_bill = $request->subtotal - $hq_credit->amount_paid;

        if ($hq_credit->rest_of_bill <= 0):
            $hq_credit->credit_status = 'Paid';
        else:
            if (count($hq_credit->hqcredit_hqcreditpayment) > 0):
                $hq_credit->credit_status = 'Halfpaid';
            else:
                $hq_credit->credit_status = 'Unpaid';
            endif;
        endif;

        $hq_credit->save();

        // Masuk Jurnal Akunting
        app('App\Http\Controllers\TempJournalController')->edit_hq_credit($temp_hq_credit, $hq_credit);

        Session::flash('flash_message', trans('messages.hq_credit_has_been_successfully_updated'));
        return redirect('cf-lite/hq/credit-detail/'.$id);
    }

    public function store_credit_payment($id, Request $request) {
        $this->validate($request, [
            'py_staff' => 'required|exists:users,id',
            'payment_date' => 'required|date',
            'payment_method' => 'required|exists:mst_payment_methods,id',
            'bank' => 'sometimes',
            'py_information' => 'sometimes',
            'py_subtotal' => 'required|numeric',
        ]);

        // Cek HQ Credit
        $hq_credit = HQCredit::findOrFail($id);

        // Create HQ Credit Payment
        $hq_payment = new HQCreditPayment;
        $hq_payment->hq_credit_id = $hq_credit->id;
        $hq_payment->payment_date = $request->payment_date;
        $hq_payment->total = $request->py_subtotal;
        $hq_payment->staff_id = $request->py_staff;

        if (!empty($request->py_information)):
            $hq_payment->information = $request->py_information;
        endif;

        $hq_payment->payment_type_id = $request->payment_method;

        if ($request->payment_method == 1):
            $hq_payment->bank_id = 1;
        else:
            if (!empty($request->bank)):
                $hq_payment->bank_id = $request->bank;
            else:
                $hq_payment->bank_id = 1;
            endif;
        endif;

        // Generate Payment Number
        $temp_auto_number = AutoNumber::where('name', '=', 'HQCreditPayment')->first();
        $new_auto_number = AutoNumber::findOrFail($temp_auto_number->id);
        $new_auto_number->current_number = $new_auto_number->current_number + 1;
        $new_auto_number->save();

        $month_roman = array(1=>'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');
        $payment_number = sprintf('%08d', $temp_auto_number->current_number).'/PIU/CIND/'.sprintf('%03d', \Illuminate\Support\Facades\Auth::user()->user_club->jubilant_id).'/'.$month_roman[\Carbon\Carbon::now()->format('n')].'/'.\Carbon\Carbon::now()->format('Y');
        $hq_payment->payment_number = $payment_number;

        if ($hq_payment->save()):
            $hq_credit->amount_paid = $hq_credit->amount_paid + $request->py_subtotal;
            $hq_credit->rest_of_bill = $hq_credit->rest_of_bill - $request->py_subtotal;

            if ($hq_credit->rest_of_bill <= 0):
                $hq_credit->credit_status = 'Paid';
            else:
                if ($hq_credit->rest_of_bill < $hq_credit->subtotal):
                    $hq_credit->credit_status = 'Halfpaid';
                else:
                    $hq_credit->credit_status = 'Unpaid';
                endif;
            endif;

            $hq_credit->save();

            // Masuk Jurnal Akunting
            app('App\Http\Controllers\TempJournalController')->add_hq_credit_payment($hq_payment, false);
        endif;

        Session::flash('flash_message', trans('messages.hq_credit_payment_has_been_successfully_added'));
        return redirect('cf-lite/hq/credit-detail/'.$id);
    }

    public function get_credit_payment(Request $request) {
        $cp = HQCreditPayment::findOrFail($request->cp_id);

        return response()->json($cp);
    }

    public function update_credit_payment(Request $request) {
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

        // Update HQ Credit Payment
        $cp = HQCreditPayment::findOrFail($request->cp_id);

        // Added 16122019
        $temp_cp = HQCreditPayment::findOrFail($request->cp_id);

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

        // Update HQ Credit
        if ($diff != 0):
            $hq_credit = HQCredit::findOrFail($cp->hq_credit_id);
            $hq_credit->amount_paid = $hq_credit->amount_paid + $diff;
            $hq_credit->rest_of_bill = $hq_credit->rest_of_bill - $diff;

            if ($hq_credit->rest_of_bill <= 0):
                $hq_credit->credit_status = 'Paid';
            else:
                if ($hq_credit->rest_of_bill < $hq_credit->subtotal):
                    $hq_credit->credit_status = 'Halfpaid';
                else:
                    $hq_credit->credit_status = 'Unpaid';
                endif;
            endif;

            $hq_credit->save();
        endif;

        // Masuk Jurnal Akunting
        app('App\Http\Controllers\TempJournalController')->edit_hq_credit_payment($temp_cp, $cp);

        Session::flash('flash_message', trans('messages.hq_credit_payment_has_been_successfully_updated'));
        return redirect('cf-lite/hq/credit-detail/'.$cp->hq_credit_id);
    }
}
