<?php

namespace App\Http\Controllers;

use App\Model\MasterData\Bank;
use App\Model\MasterData\NoSaleReason;
use App\Model\Payment\MemberPayment;
use App\Model\Refund\CancellationReason;
use App\Model\Refund\ContinueMembershipRefund;
use App\Model\Refund\RefundSubmission;
use Session;
use App\AutoNumber;
use App\Model\Club\ClubServiceFee;
use App\Model\MasterData\MembershipType;
use App\Model\MasterData\PaymentMethod;
use App\Model\Member\ContinueMembership;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContinueMembershipController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('wm-only');
    }

    public function main_menu() {
        $need_activations = ContinueMembership::where('status', '=', 'Pending')
            ->whereDate('from_date', '<=', Carbon::today()->toDateString())
            ->whereHas('continuemembership_user', function ($q) {
                $q->where('club_id', '=', Auth::user()->club_id);
            })
            ->get()
            ->count();

        return view('cf_lite.member.membership-continue-main', compact('need_activations'));
    }

    public function add_request() {
        $members = User::where('club_id', '=', Auth::user()->club_id)
            ->whereHas('user_memberstatus', function ($q) {
                $q->where('status', '=', 'Active');
//                    ->whereDate('thru_date', '<=', Carbon::today()->addMonths(3)->toDateString());
            })
            ->orderBy('name', 'asc')
//            ->paginate(100);
            ->get();

        return view('cf_lite.member.membership-continue-request', compact('members'));
    }

    public function choose_membership($id) {
        $member = User::findOrFail($id);

        //Added 12112018
        $member_payments = MemberPayment::where('user_id', '=', $member->id)
            ->where('payment_status', '<>', 'Paid')
            ->get();

        if (!empty($member_payments) && count($member_payments) > 0):
            Session::flash('flash_message', trans('messages.please_finish_the_payment_first'));
            return redirect('cf-lite/continue-membership/request');
        else:
            session(['continue_member' => $id]);

            $membership_types = MembershipType::where('membership_type', '<>', 'Staff')->orderBy('membership_types', 'asc')->simplePaginate(9);

            return view('cf_lite.member.membership-continue-choose', compact('membership_types', 'member'));
        endif;
    }

    public function payment_detail($id) {
        if (!empty(session('continue_member'))):
            session(['payment_type' => 'continue']);
            $membership_type = MembershipType::findOrFail($id);
            $member = User::findOrFail(session('continue_member'));

            $club_id = $member->club_id;

            $csf = ClubServiceFee::where('club_id', '=', $club_id)
                ->where('membership_type_id', '=', $id)
                ->where('thru_date', null)
                ->first();

            $payment_methods = PaymentMethod::availableForPayment()->orderBy('payment_method', 'desc')->pluck('payment_method', 'id');

            //Payment Number
            $temp_auto_number = AutoNumber::findOrFail('1');

            //FA Coaches
            $fa_coaches = User::where('club_id', '=', Auth::user()->club_id)
                ->where('role_id', '<>', '7')
                ->orderBy('name', 'asc')
                ->pluck('name', 'id');

            //Bank Added 28062019
            $banks = Bank::where('id', '<>', 1)
                ->whereHas('bank_club', function ($q){
                    $q->where('club_id', '=', Auth::user()->club_id);
                })->get();

            return view('cf_lite.payments.continue-payment-detail', compact('membership_type', 'member', 'csf', 'payment_methods', 'temp_auto_number', 'fa_coaches', 'banks'));
        endif;
    }

    public function activation_list() {
        $members = ContinueMembership::where('status', '=', 'Pending')
            ->whereDate('from_date', '<=', Carbon::today()->toDateString())
            ->whereHas('continuemembership_user', function ($q) {
                $q->where('club_id', '=', Auth::user()->club_id);
            })
            ->orderBy('from_date', 'asc')
            ->get();

        return view('cf_lite.member.membership-continue-activation', compact('members'));
    }

    public function activate_continue_membership(Request $request) {
        foreach ($request->continue as $key => $value):
            //Continue Membership
            $continue_membership = ContinueMembership::findOrFail($value);
            $continue_membership->status = 'Active';
            $continue_membership->save();

            //Member Status
            $member = User::findOrFail($continue_membership->user_id);

            $member_status = $member->user_memberstatus;
            $member_status->status = 'Active';
            $member_status->membership_type_id = $continue_membership->membership_type_id;
            $member_status->from_date = $continue_membership->from_date;
            $member_status->thru_date = $continue_membership->thru_date;
            $member_status->payment_status = $continue_membership->payment_status;
            $member_status->member_vip_status = $continue_membership->member_vip_status;
            $member->user_memberstatus()->save($member_status);

            Session::flash('flash_message', trans('messages.member_has_been_activated'));
            return redirect('cf-lite/continue-membership/activation');
        endforeach;
    }

    public function pending_list() {
        $members = ContinueMembership::where('status', '=', 'Pending')
            ->whereHas('continuemembership_user', function ($q) {
                $q->where('club_id', '=', Auth::user()->club_id);
            })
            ->orderBy('from_date', 'asc')
            ->paginate(100);

        return view('cf_lite.member.membership-continue-pending-list', compact('members'));
    }

    public function cancel_membership_request_detail($id) {
        $continue = ContinueMembership::findOrFail($id);

        $payment = MemberPayment::where('user_id', '=', $continue->user_id)
            ->whereDate('created_at', '<=', Carbon::parse($continue->created_at)->toDateString())
            ->orderBy('created_at', 'desc')
            ->first();

        $nosalereasons = NoSaleReason::pluck('nosale_reason', 'id');

        $fa_coaches = User::where('club_id', '=', Auth::user()->club_id)
            ->where('role_id', '<>', '7')
            ->orderBy('name', 'asc')
            ->pluck('name', 'id');

        return view('cf_lite.cancelling-membership.cancelling-membership-continue-detail', compact('payment', 'continue', 'nosalereasons', 'fa_coaches'));
    }

    public function create_refund($id, Request $request) {
        $this->validate($request, [
            'payment' => 'required|numeric',
            'cancellation_reason_id' => 'required|numeric',
            'cancellation_reason' => 'sometimes',
            'submit_by' => 'required',
        ]);

        $continue = ContinueMembership::findOrFail($id);
        $payment = MemberPayment::findOrFail($request->payment);

        //Refund
        $refund = new ContinueMembershipRefund();
        $refund->continue_id = $continue->id;
        $refund->payment_id = $payment->id;
        $refund->refund_status = 'Paid';
        $refund->refund_process_date = Carbon::createFromFormat('Y-m-d H:i', Carbon::now()->format('Y-m-d H:i'), session('timezone'))->setTimezone('UTC');

        if (!empty($request->submit_by)):
            $refund->submit_by = $request->submit_by;
        else:
            if (!empty(session('staff_session'))):
                $refund->submit_by = session('staff_session');
            else:
                $refund->submit_by = Auth::id();
            endif;
        endif;

        if (!empty($request->cancellation_reason_id)):
            $refund->cancellation_reason_id = $request->cancellation_reason_id;
        endif;

        if (!empty($request->cancellation_reason)):
            $refund->cancellation_reason = $request->cancellation_reason;
        endif;

        $refund->save();


        //Continue Membership
        $continue->status = 'Done';
        $continue->save();

        Session::flash('flash_message', trans('messages.refund_submission_has_been_made'));

        return redirect('cf-lite/refund/'.$refund->id.'/continue-membership');
    }

    //Reactivate Membership
    public function reactivate($id) {
        $member = User::findOrFail($id);
        $exist = 0;

        if (!empty($member->user_continuemembership)):
            foreach ($member->user_continuemembership as $continue):
                if ($continue->status == 'Pending'):
                    $exist++;
                endif;
            endforeach;
        endif;

        if ($exist == 0):
            session(['reactivation' => $member->id]);
            return redirect('cf-lite/continue-membership/'.$member->id.'/membership');
        else:
            Session::flash('flash_message', trans('messages.reactivation_has_been_made'));
            return redirect('cf-lite/continue-membership/activation');
        endif;
    }
}
