<?php

namespace App\Http\Controllers;

use App\Model\Member\MemberDepositInformation;
use Session;
use App\AutoNumber;
use App\Model\Club\ClubServiceFee;
use App\Model\Log\LogMemberStatus;
use App\Model\Member\MemberDeposit;
use App\Model\Payment\MemberPayment;
use App\Model\Refund\RefundSubmission;
use App\Model\Voucher\Voucher;
use App\Model\Workout\Workout;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinancialManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('wm-only');
    }

    public function member_deposit_main(Request $request) {
        if (!empty($request->s) && !empty($request->search_option)):
            $members = User::where('club_id', '=', Auth::user()->club_id)->where('role_id', '=', '7');

            if ($request->search_option == 'name'):
                $members->where('name', 'ILIKE', '%'.ucwords($request->s).'%');
            elseif ($request->search_option == 'keytag'):
                $search = $request->s;
                $members->whereHas('user_memberpersonal', function ($q) use ($search) {
                    $q->where('key_tag_id', 'ILIKE', '%'.$search.'%');
                });
            endif;

            $members = $members->paginate(100);
            return view('cf_lite.financial-management.member-deposit-main', compact('members'));
        else:
            $member_deposits = MemberDeposit::where('status', '=', 'Pending')
                ->whereHas('memberdeposit_user', function ($q) {
                    $q->where('club_id', '=', Auth::user()->club_id);
                })
                ->orderBy('created_at', 'desc')
                ->take(100)
                ->get();

            $members = User::where('club_id', '=', Auth::user()->club_id)->where('role_id', '=', '7')->orderBy('name', 'asc')->paginate(100);

            return view('cf_lite.financial-management.member-deposit-main', compact('member_deposits', 'members'));
        endif;
    }

    public function member_deposit_pay($id) {
        $member_deposit = MemberDeposit::findOrFail($id);

        $refund_submissions = RefundSubmission::where('refund_number', '=', $member_deposit->payment_number)
            ->where('user_id', '=', $member_deposit->user_id)
            ->first();

        $refund_submission = RefundSubmission::findOrFail($refund_submissions->id);
        $member = User::findOrFail($refund_submission->user_id);

        //Cek tanggal awal pembayaran keanggotaan, diambil dari log membership terakhir
        $payment_starting_date = LogMemberStatus::where('user_id', '=', $refund_submission->user_id)
            ->where('membership_type_id', '=', $refund_submission->membership_type_id)
            ->whereDate('from_date', '>=', Carbon::parse($refund_submission->start_membership_date))
            ->whereDate('thru_date', '=', Carbon::parse($refund_submission->end_membership_date))
            ->orderBy('created_at', 'desc')
            ->first();

        //Added 06112018
        if (empty($payment_starting_date)):
            $payment_starting_date = LogMemberStatus::where('user_id', '=', $refund_submission->user_id)
                ->where('membership_type_id', '=', $refund_submission->membership_type_id)
                ->whereDate('thru_date', '=', Carbon::parse($refund_submission->end_membership_date))
                ->orderBy('created_at', 'desc')
                ->first();

            if (empty($payment_starting_date)):
                $payment_starting_date = LogMemberStatus::where('user_id', '=', $refund_submission->user_id)
                    ->where('membership_type_id', '=', $refund_submission->membership_type_id)
                    ->whereDate('thru_date', '<=', Carbon::parse($refund_submission->end_membership_date))
                    ->orderBy('created_at', 'desc')
                    ->first();
            endif;
        endif;

        //Cek lama keanggotaan sejak join
        $joined_date = Carbon::parse($refund_submission->start_membership_date);
        $day_diff = Carbon::parse($refund_submission->refund_submission_date)->diffInDays($joined_date);
        $month_diff = ceil($day_diff / 30);

        //First Payment
        $first_payment = MemberPayment::where('user_id', '=', $refund_submission->user_id)
            ->where('payment_category', '=', 'First')
            ->whereDate('created_at', '>=', Carbon::parse($payment_starting_date->created_at)->toDateString())
            ->orderBy('created_at', 'desc')
            ->first();


        //Cek Tarif Keanggotaan MM dari CSF
        $mm_price = ClubServiceFee::where('club_id', '=', $refund_submission->refundsubmission_user->club_id)
            ->where('membership_type_id', '=', '1')
            ->whereDate('from_date', '<=', Carbon::parse($payment_starting_date->from_date)->toDateString())
            ->orderBy('from_date', 'asc')
            ->first();

        //Monthly Payment
        //AD
        if ($refund_submission->refundsubmission_membershiptype->payment_type == 'AD'):
            $monthly_payments = MemberPayment::where('user_id', '=', $refund_submission->user_id)
                ->where('payment_category', '=', 'Monthly')
                ->whereDate('created_at', '>=', Carbon::parse($payment_starting_date->created_at)->toDateString())
//                ->orderBy('created_at', 'asc')
                ->orderBy('payment_date', 'asc')
                ->get();
        //Prepaid
        elseif ($refund_submission->refundsubmission_membershiptype->payment_type == 'Prepaid'):
            $monthly_payments = MemberPayment::where('user_id', '=', $refund_submission->user_id)
                ->where('payment_category', '=', 'Monthly')
                ->where('payment_status', '<>', 'Unpaid')
                ->whereDate('created_at', '>=', Carbon::parse($payment_starting_date->created_at)->toDateString())
//                ->orderBy('created_at', 'asc')
                ->orderBy('payment_date', 'asc')
                ->get();
        endif;

        //FA Coaches
        $fa_coaches = User::where('club_id', '=', Auth::user()->club_id)
            ->where('role_id', '<>', '7')
            ->orderBy('name', 'asc')
            ->pluck('name', 'id');

        //Cek jika telah FWO
        if ($day_diff <= 3):
            $total_workouts = Workout::where('user_id', '=', $member->id)
                ->whereDate('workout_date', '>=', Carbon::parse($member->user_memberstatus->from_date)->toDateString())
                ->get()
                ->count();

            return view('cf_lite.financial-management.financial-management-detail', compact('first_payment', 'monthly_payments', 'month_diff', 'mm_price', 'fa_coaches', 'refund_submission', 'day_diff', 'total_workouts', 'member_deposit'));
        else:
            return view('cf_lite.financial-management.financial-management-detail', compact('first_payment', 'monthly_payments', 'month_diff', 'mm_price', 'fa_coaches', 'refund_submission', 'day_diff', 'member_deposit'));
        endif;
    }

    public function process_member_deposit_payment($id, Request $request) {
        if (!empty($request->use_voucher)):
            $this->validate($request, [
                'cash_amount' => 'required|numeric',
                'nominal' => 'required|numeric|max:'.$request->old_cash,
                'from_date' => 'required|date|after:yesterday',
                'thru_date' => 'required|date|after_or_equal:'.$request->from_date,
                'voucher_code' => 'required|unique:vouchers,identity_number',
            ]);
        endif;
        $transaction = 0;

        $member_deposit = MemberDeposit::findOrFail($id);

        $saldo = MemberDeposit::where('user_id', '=', $member_deposit->user_id)
            ->orderBy('created_at', 'desc')
            ->first();

        if (count($saldo) > 0):
            $current_saldo = $saldo->saldo;
        else:
            $current_saldo = 0;
        endif;


        //Buat Pembayaran Member Deposit Baru
        if ($member_deposit->payment_type == 'Payment'):
            $new_member_deposit = new MemberDeposit;
            $new_member_deposit->payment_number = 'paid/'.$member_deposit->payment_number;
            $new_member_deposit->user_id = $member_deposit->user_id;

            if ($member_deposit->debet == 0):
                $new_member_deposit->debet = $member_deposit->kredit;
                $new_member_deposit->saldo = $current_saldo + $member_deposit->kredit;
            endif;

            if ($member_deposit->kredit == 0):
                $new_member_deposit->kredit = $member_deposit->debet;
                $new_member_deposit->saldo = $current_saldo - $member_deposit->debet;
            endif;

            $new_member_deposit->status = 'Done';
            $new_member_deposit->payment_type = $member_deposit->payment_type;
            $new_member_deposit->save();

//            $transaction = $transaction + $member_deposit->kredit;
        elseif ($member_deposit->payment_type == 'Refund'):
            if (!empty($request->cash_amount)):
                $new_member_deposit_cash = new MemberDeposit;
                $new_member_deposit_cash->payment_number = 'cash/'.$member_deposit->payment_number;
                $new_member_deposit_cash->user_id = $member_deposit->user_id;

                if ($member_deposit->debet == 0):
                    $new_member_deposit_cash->debet = $member_deposit->kredit;
                    $new_member_deposit_cash->saldo = $current_saldo + $member_deposit->kredit;
                endif;

                if ($member_deposit->kredit == 0):
                    $new_member_deposit_cash->kredit = $request->cash_amount;
                    $new_member_deposit_cash->saldo = $current_saldo - $request->cash_amount;
                endif;

                $new_member_deposit_cash->status = 'Done';
                $new_member_deposit_cash->payment_type = $member_deposit->payment_type;
                $new_member_deposit_cash->save();

//                $transaction = $transaction + $request->cash_amount;
            endif;

            if (!empty($request->use_voucher) && !empty($request->nominal)):
                $new_member_deposit_voucher = new MemberDeposit;
                $new_member_deposit_voucher->payment_number = 'voucher/'.$member_deposit->payment_number;
                $new_member_deposit_voucher->user_id = $member_deposit->user_id;

                if ($member_deposit->debet == 0):
                    $new_member_deposit_voucher->debet = $member_deposit->kredit;
                    $new_member_deposit_voucher->saldo = $current_saldo + $member_deposit->kredit;
                endif;

                if ($member_deposit->kredit == 0):
                    $new_member_deposit_voucher->kredit = $request->nominal;
                    $new_member_deposit_voucher->saldo = $current_saldo - $request->nominal;
                endif;

                $new_member_deposit_voucher->status = 'Done';
                $new_member_deposit_voucher->payment_type = $member_deposit->payment_type;
                $new_member_deposit_voucher->save();

//                $transaction = $transaction + $request->nominal;

                //Create Voucher
                $voucher = new Voucher;
                $voucher->identity_number = $request->voucher_code;
                $voucher->from_date = $request->from_date;
                $voucher->thru_date = $request->thru_date;
                $voucher->type_id = '3'; //Credit Voucher Type
                $voucher->status = 'Out';
                $voucher->voucher_base = 'value';
                $voucher->value = $request->nominal;
                $voucher->percent = 0;
                $voucher->save();
            endif;

            //Akunting
            app('App\Http\Controllers\AccountingJournalController')->refund_journal($id, $request->cash_amount);
        endif;

        //Update Member Deposit Lama
        $member_deposit->status = 'Done';
        $member_deposit->save();

        //Add New Member Deposit Balance
//        if (abs($member_deposit->debet + $member_deposit->kredit) != abs($transaction)):
//            $add_member_deposit = new MemberDeposit;
//            $add_member_deposit->payment_number = $member_deposit->payment_number;
//            $add_member_deposit->user_id = $member_deposit->user_id;
//
//            if ($member_deposit->payment_type == 'Payment'):
//                $add_member_deposit->kredit = $member_deposit->kredit - abs($transaction);
//                $add_member_deposit->saldo = $member_deposit->saldo + abs($transaction);
//            elseif ($member_deposit->payment_type == 'Refund'):
//                $add_member_deposit->debet = $member_deposit->debet - abs($transaction);
//                $add_member_deposit->saldo = $member_deposit->saldo - abs($transaction);
//            endif;
//
//            $add_member_deposit->status = 'Pending';
//            $add_member_deposit->payment_type = $member_deposit->payment_type;
//            $add_member_deposit->save();
//        endif;

        return redirect('cf-lite/financial-management/member/'.$member_deposit->user_id);
    }

    public function member_deposit_detail($id) {
        $member = User::findOrFail($id);

        $member_deposits = MemberDeposit::where('user_id', '=', $member->id)->orderBy('created_at', 'asc')->get();

        return view('cf_lite.financial-management.member-deposit-detail', compact('member', 'member_deposits'));
    }

    public function store_member_deposit_correction($id, Request $request) {
        $this->validate($request, [
            'payment_type' => 'required|in:payment,refund',
            'amount' => 'required|numeric',
            'information' => 'sometimes',
        ]);
        $month_roman = array(1=>'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');

        $member = User::findOrFail($id);

        if ($request->payment_type == 'payment'):
            $payment_type = 1;
            $payment_code[0] = 'manual-credit';
            $payment_code[1] = 'PY';
        elseif ($request->payment_type == 'refund'):
            $payment_type = 3;
            $payment_code[0] = 'manual-debet';
            $payment_code[1] = 'RF';
        endif;

        //Update Auto Number
        $temp_auto_number = AutoNumber::findOrFail($payment_type);
        $payment_number = $temp_auto_number->current_number;

        $temp_auto_number->current_number = $temp_auto_number->current_number + 1;
        $temp_auto_number->save();

        //Get the Last Balance
        $saldo = MemberDeposit::where('user_id', '=', $member->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if (count($saldo) > 0):
            $current_saldo = $saldo->saldo;
        else:
            $current_saldo = 0;
        endif;

        //Save Correction
        $member_deposit = new MemberDeposit;
        $member_deposit->payment_number = $payment_code[0].'/'.sprintf('%08d', $payment_number).'/'.$payment_code[1].'/CIND/'.sprintf('%03d', $member->club_id).'/'.$month_roman[Carbon::now()->format('n')].'/'.Carbon::now()->format('Y');
        $member_deposit->user_id = $member->id;

        if ($request->payment_type == 'payment'):
            $member_deposit->kredit = $request->amount;
            $member_deposit->saldo = $current_saldo - $request->amount;
        elseif ($request->payment_type == 'refund'):
            $member_deposit->debet = $request->amount;
            $member_deposit->saldo = $current_saldo + $request->amount;
        endif;

        $member_deposit->status = 'Pending';
        $member_deposit->payment_type = ucwords($request->payment_type);
        $member_deposit->save();

        //If has information
        if (!empty($request->information)):
            $information = new MemberDepositInformation;
            $information->information = $request->information;
            $member_deposit->memberdeposit_information()->save($information);
        endif;

        Session::flash('flash_message', trans('messages.balance_correction_has_been_added'));
//        return redirect('cf-lite/financial-management/member/'.$member->id);
        return redirect('cf-lite/financial-management/member-deposit/'.$member_deposit->id.'/balance-correction');
    }

    public function balance_correction($id) {
        $member_deposit = MemberDeposit::findOrFail($id);

        if ($member_deposit->status == 'Done'):
            $the_balances = MemberDeposit::where('id', '<>', $member_deposit->id)
                ->where('payment_number', 'ILIKE', '%'.$member_deposit->payment_number.'%')
                ->orderBy('created_at', 'desc')
                ->get();

            return view('cf_lite.financial-management.balance-correction-detail-v2', compact('member_deposit', 'the_balances'));
        else:
            return view('cf_lite.financial-management.balance-correction-detail-v2', compact('member_deposit'));
        endif;
    }

    public function store_balance_correction($id, Request $request) {
        $member_deposit = MemberDeposit::findOrFail($id);

        if ($member_deposit->status == 'Pending'):
            $payment_number = explode('/', $member_deposit->payment_number, 2);

            //Get the Last Balance
            $saldo = MemberDeposit::where('user_id', '=', $member_deposit->user_id)
                ->orderBy('created_at', 'desc')
                ->first();

            if (count($saldo) > 0):
                $current_saldo = $saldo->saldo;
            else:
                $current_saldo = 0;
            endif;

            //Update Member Deposit
            if ($request->_type == 'Payment'):
                $this->validate($request, [
                    'amount_paid' => 'required|numeric|max:'.$request->_amount,
                    'payment_date' => 'sometimes',
                ]);

                $new_saldo = $current_saldo + $request->amount_paid;
                if ($new_saldo == 0):
                    $status = 'Done';
                else:
                    $status = 'Pending';
                endif;

                $new_member_deposit = new MemberDeposit;
                $new_member_deposit->payment_number = 'paid-manual-credit/'.$payment_number[1];
                $new_member_deposit->user_id = $member_deposit->user_id;
                $new_member_deposit->debet = $request->amount_paid;
                $new_member_deposit->saldo = $new_saldo;
                $new_member_deposit->status = $status;
                $new_member_deposit->payment_type = 'Payment';

                if (!empty($request->payment_date)):
                    $new_member_deposit->payment_date = $request->payment_date;
                endif;

                $new_member_deposit->save();

                Session::flash('flash_message', trans('messages.balance_correction_has_been_paid'));
            elseif ($request->_type == 'Refund'):
                if (!empty($request->use_voucher)):
                    $this->validate($request, [
                        'cash_amount' => 'required|numeric|max:'.$request->old_cash,
                        'payment_date' => 'sometimes',
                        'nominal' => 'required|numeric|max:'.$request->old_cash,
                        'from_date' => 'required|date|after:yesterday',
                        'thru_date' => 'required|date|after_or_equal:'.$request->from_date,
                        'voucher_code' => 'required|unique:vouchers,identity_number',
                    ]);
                else:
                    $this->validate($request, [
                        'cash_amount' => 'required|numeric|max:'.$request->old_cash,
                        'payment_date' => 'sometimes',
                    ]);
                endif;

                $new_saldo = $current_saldo - $request->cash_amount - $request->nominal;
                if ($new_saldo == 0):
                    $status = 'Done';
                else:
                    $status = 'Pending';
                endif;

                if (!empty($request->cash_amount)):
                    $new_member_deposit_cash = new MemberDeposit;
                    $new_member_deposit_cash->payment_number = 'cash-manual-debet/'.$payment_number[1];
                    $new_member_deposit_cash->user_id = $member_deposit->user_id;
                    $new_member_deposit_cash->kredit = $request->cash_amount;
                    $new_member_deposit_cash->saldo = $current_saldo - $request->cash_amount;

                    if (!empty($request->use_voucher) && !empty($request->nominal)):
                        $new_member_deposit_cash->status = 'Done';
                    else:
                        $new_member_deposit_cash->status = $status;
                    endif;

                    $new_member_deposit_cash->payment_type = 'Refund';
                    $new_member_deposit_cash->save();
                endif;

                if (!empty($request->use_voucher) && !empty($request->nominal)):
                    $new_member_deposit_voucher = new MemberDeposit;
                    $new_member_deposit_voucher->payment_number = 'voucher-manual-debet/'.$payment_number[1];
                    $new_member_deposit_voucher->user_id = $member_deposit->user_id;
                    $new_member_deposit_voucher->kredit = $request->nominal;
                    $new_member_deposit_voucher->saldo = $new_saldo;
                    $new_member_deposit_voucher->status = $status;
                    $new_member_deposit_voucher->payment_type = 'Refund';
                    $new_member_deposit_voucher->save();

                    //Create Voucher
                    $voucher = new Voucher;
                    $voucher->identity_number = $request->voucher_code;
                    $voucher->from_date = $request->from_date;
                    $voucher->thru_date = $request->thru_date;
                    $voucher->type_id = '3'; //Credit Voucher Type
                    $voucher->status = 'Out';
                    $voucher->voucher_base = 'value';
                    $voucher->value = $request->nominal;
                    $voucher->percent = 0;
                    $voucher->save();
                endif;

                Session::flash('flash_message', trans('messages.balance_correction_has_been_refunded'));
            endif;

            $member_deposit->status = 'Done';
            $member_deposit->save();
        endif;

//        return redirect('cf-lite/financial-management/member-deposit/'.$member_deposit->id.'/balance-correction');
        return redirect('cf-lite/financial-management/member/'.$member_deposit->user_id);
    }
}
