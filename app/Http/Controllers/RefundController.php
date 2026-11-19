<?php

namespace App\Http\Controllers;

use App\Model\Club\ClubServiceFee;
use App\Model\Guest\BuddyReferralLead;
use App\Model\Log\LogMemberStatus;
use App\Model\Log\LogQuitMemberFlag;
use App\Model\MasterData\NoSaleReason;
use App\Model\Member\MemberDeposit;
use App\Model\Payment\MemberPayment;
use App\Model\Refund\CancellationReason;
use App\Model\Refund\ContinueMembershipRefund;
use App\Model\Refund\RefundSubmission;
use App\Model\Staff\TransferHistory;
use App\Model\Workout\Workout;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RefundController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('wm-only');
    }

    public function refund_list() {
//        $refunds = RefundSubmission::where('refund_status', '=', 'Pending')->orderBy('refund_submission_date', 'desc')->take(3)->get();
//        $paid_refunds = RefundSubmission::where('refund_status', '=', 'Paid')->orderBy('refund_submission_date', 'desc')->take(3)->get();

        $member_deposits = MemberDeposit::where('status', '=', 'Pending')
            ->whereHas('memberdeposit_user', function ($q) {
                $q->where('club_id', '=', Auth::user()->club_id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(100);

        return view('cf_lite.refund.refund-list-v2', compact('member_deposits'));
    }

    public function refund_detail($id) {
        $refund = RefundSubmission::findOrFail($id);
        $member = User::findOrFail($refund->user_id);

        //Cek jumlah uang yang telah dibayarkan
        $payments = MemberPayment::where('user_id', '=', $member->id)
            ->where('payment_status', '<>', 'Unpaid')
            ->whereDate('payment_date', '>=', $member->user_memberpersonal->joined)
            ->get();

        //Cek jika telah FWO
        $workouts = Workout::where('user_id', '=', $member->id)
            ->whereDate('workout_date', '>=', $member->user_memberpersonal->joined)
            ->get();
        $total_workouts = count($workouts);

        $nosalereasons = NoSaleReason::pluck('nosale_reason', 'id');

        return view('cf_lite.refund.refund-detail', compact('refund', 'member', 'payments', 'total_workouts', 'nosalereasons'));
    }

    public function refund_process($id, Request $request) {
        $refund = RefundSubmission::findOrFail($id);

        if ($request->has('process')):
            $this->validate($request, [
                'cancellation_reason_id' => 'required',
                'cancellation_reason' => 'sometimes',
            ]);

            $refund->refund_status = 'Paid';
            $refund->refund_process_date = Carbon::now();
            $refund->save();

            //Update member_status
            $member = User::findOrFail($refund->user_id);

            $member_status = $member->user_memberstatus;
            $member_status->status = 'Stopped';
            $member_status->from_date = Carbon::now();
            $member_status->thru_date = Carbon::now();
            $member->user_memberstatus()->save($member_status);

            //Cancellation Reason
            $cancellation_reason = new CancellationReason;
            $cancellation_reason->membership_type_id = $member_status->membership_type_id;

            if (!empty($request->cancellation_reason_id)):
                $cancellation_reason->cancellation_reason_id = $request->cancellation_reason_id;
            else:
                $cancellation_reason->cancellation_reason = $request->cancellation_reason;
            endif;

            $refund->refundsubmission_cancellationreason()->save($cancellation_reason);

            //Change status_gm to Guest in buddy_referral_leads table
            $brl = BuddyReferralLead::findOrFail($member->user_memberpersonal->guest_id);
            $brl->status_gm = 'Guest';
            $brl->save();
        elseif ($request->has('cancel')):
            $refund->refund_status = 'Cancelled';
            $refund->refund_process_date = Carbon::now();
            $refund->save();
        endif;

        return redirect('cf-lite/refund/'.$refund->id.'/detail');
    }

    public function continue_membership_refund_detail($id) {
        $refund = ContinueMembershipRefund::findOrFail($id);

        return view('cf_lite.refund.continue-membership-refund-detail', compact('refund'));
    }

    public function refund_cancellation_detail($id) {
        $refund_submission = RefundSubmission::findOrFail($id);
        $member = User::findOrFail($refund_submission->user_id);

        //Cek tanggal awal pembayaran keanggotaan, diambil dari log membership terakhir
        $payment_starting_date = LogMemberStatus::where('user_id', '=', $refund_submission->user_id)
            ->where('membership_type_id', '=', $refund_submission->membership_type_id)
            ->whereDate('from_date', '>=', Carbon::parse($refund_submission->start_membership_date))
            ->whereDate('thru_date', '=', Carbon::parse($refund_submission->end_membership_date))
            ->orderBy('created_at', 'desc')
            ->first();

        //Added 05112018
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
//            if ((strtotime($member->user_memberstatus->from_date) >= strtotime(Carbon::now()->subMonth())) && (strtotime($member->user_memberstatus->from_date) < strtotime(Carbon::now()->subDays(3)))):
//                $month_diff = 1;
//            else:
//                $month_diff = ceil($day_diff / 30);
//            endif;

        //First Payment
        $first_payment = MemberPayment::where('user_id', '=', $refund_submission->user_id)
            ->where('payment_category', '=', 'First')
            ->whereDate('created_at', '>=', Carbon::parse($payment_starting_date->created_at)->toDateString())
            ->orderBy('created_at', 'desc')
            ->first();


        //Cek jika ada transfer (Added 12022019)
        $transfer = TransferHistory::where('user_id', '=', $refund_submission->user_id)
            ->whereDate('from_date', '>=', Carbon::parse($refund_submission->refundsubmission_user->user_memberstatus->from_date)->toDateString())
            ->first();

        if (!empty($transfer)):
            $original_club = $transfer->club_id;
        else:
            $original_club = $refund_submission->refundsubmission_user->club_id;
        endif;
        //Cek jika ada transfer (Added 12022019)

        //Cek Tarif Keanggotaan MM dari CSF
        $mm_price = ClubServiceFee::where('club_id', '=', $original_club)
            ->where('membership_type_id', '=', '1')
            ->whereDate('from_date', '<=', Carbon::parse($payment_starting_date->from_date)->toDateString())
            ->orderBy('from_date', 'asc')
            ->first();

        //Monthly Payment
        //AD
        $monthly_payments = '';
        if ($refund_submission->refundsubmission_membershiptype->payment_type == 'AD'):
//            if ($month_diff == 1):
//                $monthly_payments = MemberPayment::where('user_id', '=', $refund_submission->user_id)
//                    ->where('payment_category', '=', 'Monthly')
//                    ->whereDate('created_at', '>=', Carbon::parse($payment_starting_date->created_at)->toDateString())
//                    ->orderBy('created_at', 'asc')
//                    ->get();
//            elseif ($month_diff > 1):
//                $monthly_payments = MemberPayment::where('user_id', '=', $refund_submission->user_id)
//                    ->where('payment_category', '=', 'Monthly')
//                    ->whereDate('created_at', '>=', Carbon::parse($payment_starting_date->created_at)->toDateString())
//                    ->orderBy('created_at', 'asc')
//                    ->get();
//            endif;
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

        //Cancellation Reason added 06042018
        $nosalereasons = NoSaleReason::pluck('nosale_reason', 'id');

        //Cek jika telah FWO
        if ($day_diff <= 3):
            $total_workouts = Workout::where('user_id', '=', $member->id)
                ->whereDate('workout_date', '>=', Carbon::parse($member->user_memberstatus->from_date)->toDateString())
                ->get()
                ->count();

            return view('cf_lite.refund.refund-detail-v2', compact('member', 'first_payment', 'monthly_payments', 'month_diff', 'mm_price', 'fa_coaches', 'refund_submission', 'day_diff', 'total_workouts', 'nosalereasons'));
        else:
            return view('cf_lite.refund.refund-detail-v2', compact('member', 'first_payment', 'monthly_payments', 'month_diff', 'mm_price', 'fa_coaches', 'refund_submission', 'day_diff', 'nosalereasons'));
        endif;
    }

    public function refund_cancellation_process($id, Request $request) {
        $refund_submission = RefundSubmission::findOrFail($id);
        $this->validate($request, [
            'date' => 'required|date_format:Y-m-d|after:'.Carbon::parse($refund_submission->refund_submission_date)->addDays(29)->format('Y-m-d'),
        ]);

        if ($request->has('process')):
            $member_deposit = new MemberDeposit;
            $member_deposit->payment_number = $refund_submission->refund_number;
            $member_deposit->user_id = $refund_submission->user_id;

            $saldo = MemberDeposit::where('user_id', '=', $refund_submission->user_id)->orderBy('created_at', 'desc')->first();
            if (!empty($saldo)):
                $real_saldo = $saldo->saldo;
            else:
                $real_saldo = 0;
            endif;

            if ($refund_submission->billing_type == 'Refund'):
                $member_deposit->debet = $refund_submission->refund_amount;
                $member_deposit->saldo = $real_saldo + $refund_submission->refund_amount;
                $member_deposit->payment_type = 'Refund';
            elseif ($refund_submission->billing_type == 'Bill'):
                $member_deposit->kredit = $refund_submission->refund_amount;
                $member_deposit->saldo = $real_saldo - $refund_submission->refund_amount;
                $member_deposit->payment_type = 'Payment';
            endif;

            $member_deposit->status = 'Pending';
            $member_deposit->save();

            $refund_status = 'Paid';


            //Update Member Status
            $member = User::findOrFail($refund_submission->user_id);
            $member_status = $member->user_memberstatus;
            $member_status->status = 'Stopped';
            $member->user_memberstatus()->save($member_status);


            //Add Cancellation Reason added 06042018
            if (!empty($request->cancellation_reason_id) || !empty($request->cancellation_reason)):
                $cancellation_reason = new CancellationReason;
                $cancellation_reason->membership_type_id = $member->user_memberstatus->membership_type_id;

                if (!empty($request->cancellation_reason_id)):
                    $cancellation_reason->cancellation_reason_id = $request->cancellation_reason_id;
                endif;

                if (!empty($request->cancellation_reason)):
                    $cancellation_reason->cancellation_reason = $request->cancellation_reason;
                endif;

                $refund_submission->refundsubmission_cancellationreason()->save($cancellation_reason);
            endif;

            // Log Quit Member Flag
            $log_quit = new LogQuitMemberFlag;

            if (!empty($member->memberuser_coachmember)):
                $log_quit->staff_id = $member->memberuser_coachmember->coach_id;
            endif;

            $log_quit->member_id = $member->id;
            $log_quit->flag = $member->user_memberpersonal->flag;
            $log_quit->club = $member->user_club->club_name;
            $log_quit->membership_type = $member->user_memberstatus->memberstatus_membershiptype->membership_type_name;
            $log_quit->cancellation_date = $request->date;
            $log_quit->save();
        elseif ($request->has('cancel')):
            $refund_status = 'Cancelled';
        endif;

        $refund_submission->refund_status = $refund_status;
        $refund_submission->refund_process_date = $request->date;
        $refund_submission->submit_by = $request->fa_coach;
        $refund_submission->save();

        //Cek tanggal awal pembayaran keanggotaan, diambil dari log membership terakhir
        $payment_starting_date = LogMemberStatus::where('user_id', '=', $refund_submission->user_id)
            ->where('membership_type_id', '=', $refund_submission->membership_type_id)
            ->whereDate('from_date', '>=', Carbon::parse($refund_submission->start_membership_date))
            ->whereDate('thru_date', '=', Carbon::parse($refund_submission->end_membership_date))
            ->orderBy('created_at', 'desc')
            ->first();

        //Added 01112018
        if (empty($payment_starting_date)):
            $payment_starting_date = LogMemberStatus::where('user_id', '=', $member->id)
                ->where('membership_type_id', '=', $member->user_memberstatus->membership_type_id)
                ->whereDate('thru_date', '=', Carbon::parse($member->user_memberstatus->thru_date))
                ->orderBy('created_at', 'desc')
                ->first();

            if (empty($payment_starting_date)):
                $payment_starting_date = LogMemberStatus::where('user_id', '=', $member->id)
                    ->where('membership_type_id', '=', $member->user_memberstatus->membership_type_id)
                    ->whereDate('thru_date', '<=', Carbon::parse($member->user_memberstatus->thru_date))
                    ->orderBy('created_at', 'desc')
                    ->first();
            endif;
        endif;

        //Monthly Payment
        //AD
        if ($refund_submission->refundsubmission_membershiptype->payment_type == 'AD'):
            $monthly_payments = MemberPayment::where('user_id', '=', $refund_submission->user_id)
                ->where('payment_category', '=', 'Monthly')
                ->whereDate('created_at', '>=', Carbon::parse($payment_starting_date->created_at)->toDateString())
//                ->orderBy('created_at', 'asc')
                ->orderBy('payment_date', 'asc')
                ->get();

            //Update Monthly Payment
            $month_count = 1;
            $month_diff = ceil($refund_submission->refund_diff_days / 30);
            foreach ($monthly_payments as $payment):
                if ($month_count < $month_diff):
                    $set_paid_payment = MemberPayment::findOrFail($payment->id);
                    $set_paid_payment->payment_status = 'Paid';
                    $set_paid_payment->save();
                else:
                    if ($payment->payment_status == 'Unpaid'):
                        $del_payment = MemberPayment::findOrFail($payment->id);
                        $del_payment->delete();
                    endif;
                endif;
                $month_count++;
            endforeach;

        //Prepaid
        elseif ($refund_submission->refundsubmission_membershiptype->payment_type == 'Prepaid'):
            $monthly_payments = MemberPayment::where('user_id', '=', $refund_submission->user_id)
                ->where('payment_category', '=', 'Monthly')
                ->where('payment_status', '<>', 'Paid')
                ->whereDate('created_at', '>=', Carbon::parse($payment_starting_date->created_at)->toDateString())
//                ->orderBy('created_at', 'asc')
                ->orderBy('payment_date', 'asc')
                ->get();

            //Update Installment
            foreach ($monthly_payments as $payment):
                if ($payment->payment_status == 'Unpaid'):
                    $del_payment = MemberPayment::findOrFail($payment->id);
                    $del_payment->delete();
                elseif ($payment->payment_status == 'Halfpaid'):
                    $set_paid_payment = MemberPayment::findOrFail($payment->id);
                    $set_paid_payment->payment_status = 'Paid';
                    $set_paid_payment->save();
                endif;
            endforeach;
        endif;

        if ($request->has('process')):
            // Update Log Member Status
            $current_log = LogMemberStatus::findOrFail($payment_starting_date->id);
            $current_log->thru_date = $request->date;
            $current_log->save();

            return redirect('cf-lite/financial-management/member-deposit/'.$member_deposit->id);
        elseif ($request->has('cancel')):
            return redirect('cf-lite/refund/'.$refund_submission->id.'/cancellation-detail');
        endif;
    }
}
