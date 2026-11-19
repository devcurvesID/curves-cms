<?php

namespace App\Http\Controllers;

use App\AutoNumber;
use App\Model\Club\ClubServiceFee;
use App\Model\Log\LogMemberStatus;
use App\Model\MasterData\Bank;
use App\Model\MasterData\MembershipType;
use App\Model\MasterData\PaymentMethod;
use App\Model\Member\MemberDeposit;
use App\Model\Payment\MemberPayment;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MembershipChangingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('wm-only');
    }

    public function add_request() {
        $members = User::where('club_id', '=', Auth::user()->club_id)
            ->where('role_id', '=', '7')
            ->whereHas('user_memberstatus', function ($q) {
                $q->where('status', '<>', 'Inactive')
                    ->where('status', '<>', 'Stopped');
//                    ->whereDate('thru_date', '>=', Carbon::today()->toDateString());
            })
            ->orderBy('name', 'asc')
            ->get();

        return view('cf_lite.member.membership-changing-request', compact('members'));
    }

    public function choose_membership($id) {
        $member = User::findOrFail($id);

        session(['membership_changing_member' => $id]);

        $membership_types = MembershipType::where('membership_type', '<>', 'Staff')
            ->where('id', '<>', $member->user_memberstatus->membership_type_id)
            ->where('is_vip', '=', 'general')
            ->where('status', '=', 'Active');

        if (($member->user_memberstatus->memberstatus_membershiptype->duration == '1') || ($member->user_memberstatus->memberstatus_membershiptype->duration == '6')):
            $membership_types->where('duration', '<>', '1');
        elseif ($member->user_memberstatus->memberstatus_membershiptype->duration == '12'):
            $membership_types->where('duration', '<>', '1')->where('duration', '<>', '6');
        elseif ($member->user_memberstatus->memberstatus_membershiptype->duration == '18'):
            $membership_types->where('duration', '=', '18');
        endif;

        if (($member->user_memberstatus->memberstatus_membershiptype->payment_type == 'Cash') || ($member->user_memberstatus->memberstatus_membershiptype->payment_type == 'AD')):
            $membership_types->where('payment_type', '<>', 'Cash');
        elseif ($member->user_memberstatus->memberstatus_membershiptype->payment_type == 'Prepaid'):
            $membership_types->where('payment_type', '=', 'Prepaid');
        endif;

        $membership_types = $membership_types->orderBy('membership_types', 'asc')->simplePaginate(9);
        //dd($membership_types);
        //die();
        return view('cf_lite.member.membership-changing-choose', compact('membership_types', 'member'));
    }

    public function payment_detail($id) {
        //var_dump(session('membership_changing_member'));
        if (!empty(session('membership_changing_member'))):
            session(['payment_type' => 'changing']);
            $membership_type = MembershipType::findOrFail($id);
            $member = User::findOrFail(session('membership_changing_member'));

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

            //Deposit
            $log_payment_date = LogMemberStatus::where('user_id', '=', $member->id)
                ->where('membership_type_id', '=', $member->user_memberstatus->membership_type_id)
                ->whereDate('from_date', '>=', Carbon::parse($member->user_memberstatus->from_date)->toDateString())
                ->whereDate('thru_date', '=', Carbon::parse($member->user_memberstatus->thru_date)->toDateString())
                ->first();

            //Added 18102018
            if (empty($log_payment_date)): //check by thru_date
                $log_payment_date = LogMemberStatus::where('user_id', '=', $member->id)
                    ->where('membership_type_id', '=', $member->user_memberstatus->membership_type_id)
                    ->whereDate('thru_date', '=', Carbon::parse($member->user_memberstatus->thru_date)->toDateString())
                    ->first();
            endif;

            //Added 8Sept2023
            if (empty($log_payment_date)): //check by from_date
                $log_payment_date = LogMemberStatus::where('user_id', '=', $member->id)
                    ->where('membership_type_id', '=', $member->user_memberstatus->membership_type_id)
                    ->whereDate('from_date', '=', Carbon::parse($member->user_memberstatus->from_date)->toDateString())
                    ->first();
            endif;

            //var_dump($member->user_memberstatus);
            //var_dump($log_payment_date);

            $member_payments = MemberPayment::where('user_id', '=', $member->id)
                ->whereDate('created_at', '>=', Carbon::parse($log_payment_date->created_at)->toDateString())
//                ->where('payment_status', '<>', 'Unpaid')
                ->whereDoesntHave('memberpayment_continuemembershiprefund')
                ->orderBy('id', 'desc')
                ->get();

            //Added 23072018
            $member_deposit = MemberDeposit::where('user_id', '=', $member->id)
                ->where('status', '=', 'Pending')
                ->orderBy('created_at', 'desc')
                ->first();

            //Bank Added 28062019
            $banks = Bank::where('id', '<>', 1)
                ->whereHas('bank_club', function ($q){
                    $q->where('club_id', '=', Auth::user()->club_id);
                })->get();

            return view('cf_lite.payments.membership-changing-payment-detail', compact('membership_type', 'member', 'csf', 'payment_methods', 'temp_auto_number', 'fa_coaches', 'member_payments', 'member_deposit', 'banks', 'log_payment_date'));
        endif;
    }

    //DOWNGRADE MEMBERSHIP
    public function choose_membership_downgrade($id) {
        $member = User::findOrFail($id);

        session(['membership_changing_member' => $id]);

        $membership_types = MembershipType::where('membership_type', '<>', 'Staff')
            ->where('id', '<>', $member->user_memberstatus->membership_type_id)
            ->where('is_vip', '=', 'general')
            ->where('status', '=', 'Active');

        if ($member->user_memberstatus->memberstatus_membershiptype->duration == '6'):
            $membership_types->where('duration', '=', '1');
        elseif ($member->user_memberstatus->memberstatus_membershiptype->duration == '12'):
            $membership_types->where('duration', '<>', '18');
        endif;

        if ($member->user_memberstatus->memberstatus_membershiptype->payment_type == 'AD'):
            $membership_types->where('payment_type', '<>', 'Prepaid')->where('membership_type', '<>', 'Corporate');
        endif;

        $membership_types = $membership_types->orderBy('membership_types', 'asc')->simplePaginate(9);

        return view('cf_lite.member.membership-changing-choose-downgrade', compact('membership_types', 'member'));
    }

    public function payment_detail_downgrade($id) {
        if (!empty(session('membership_changing_member'))):
            session(['payment_type' => 'changing']);
            $membership_type = MembershipType::findOrFail($id);
            $member = User::findOrFail(session('membership_changing_member'));

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

            //Deposit
            $log_payment_date = LogMemberStatus::where('user_id', '=', $member->id)
                ->where('membership_type_id', '=', $member->user_memberstatus->membership_type_id)
                ->whereDate('from_date', '>=', Carbon::parse($member->user_memberstatus->from_date)->toDateString())
                ->whereDate('thru_date', '=', Carbon::parse($member->user_memberstatus->thru_date)->toDateString())
                ->first();

            $member_payments = MemberPayment::where('user_id', '=', $member->id)
                ->whereDate('created_at', '>=', Carbon::parse($log_payment_date->created_at)->toDateString())
//                ->where('payment_status', '<>', 'Unpaid')
                ->whereDoesntHave('memberpayment_continuemembershiprefund')
                ->orderBy('id', 'desc')
                ->get();

            //Added 23072018
            $member_deposit = MemberDeposit::where('user_id', '=', $member->id)
                ->where('status', '=', 'Pending')
                ->orderBy('created_at', 'desc')
                ->first();
            $discount = 0; $subtotal = 0;
            return view('cf_lite.payments.membership-changing-payment-detail-downgrade', compact('membership_type', 'member', 'csf', 'payment_methods', 'temp_auto_number', 'fa_coaches', 'member_payments', 'member_deposit', 'log_payment_date', 'discount', 'subtotal'));
        endif;
    }
}
