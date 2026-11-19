<?php

namespace App\Http\Controllers;

class DocsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('wm-only');
    }

    public function club()
    {
        return view('docs.club');
    }

    public function staff()
    {
        return view('docs.staff');
    }

    public function product_management()
    {
        return view('docs.product-management');
    }

    public function voucher()
    {
        return view('docs.voucher');
    }

    public function tax_type()
    {
        return view('docs.tax-type');
    }
    public function privilege_access()
    {
        return view('docs.privilege-access');
    }
    public function functional_role()
    {
        return view('docs.functional-role');
    }
    public function attribute()
    {
        return view('docs.attribute');
    }
    public function standard_message()
    {
        return view('docs.standard-message');
    }
    public function challenge()
    {
        return view('docs.challenge');
    }
    public function mutate_staff()
    {
        return view('docs.mutate-staff');
    }
    public function global_activity()
    {
        return view('docs.global-activity');
    }
    public function global_message()
    {
        return view('docs.global-message');
    }
    public function membership_type()
    {
        return view('docs.membership-type');
    }
    public function locker_setting()
    {
        return view('docs.locker-setting');
    }
    public function company()
    {
        return view('docs.company');
    }
    public function certification()
    {
        return view('docs.certification');
    }

    public function traffic_control_report()
    {
        return view('docs.traffic-control-report');
    }
    public function fa_source_report()
    {
        return view('docs.fa-source-report');
    }
    public function membership_type_report()
    {
        return view('docs.membership-type-report');
    }
    public function birthday_report()
    {
        return view('docs.birthday-report');
    }
    public function member_status_report()
    {
        return view('docs.member-status-report');
    }
    public function shirt_size_report()
    {
        return view('docs.shirt-size-report');
    }
    public function product_report()
    {
        return view('docs.product-report');
    }
    public function staff_selling_report()
    {
        return view('docs.staff-selling-report');
    }
    public function fa_staff_report()
    {
        return view('docs.fa-staff-report');
    }
    public function wm_difference_report()
    {
        return view('docs.wm-difference-report');
    }
    public function top_losers_report()
    {
        return view('docs.top-losers-report');
    }
    public function wo_report()
    {
        return view('docs.wo-report');
    }
    public function challenge_report()
    {
        return view('docs.challenge-report');
    }
    public function reward_report()
    {
        return view('docs.reward-report');
    }
    public function member_cancellation_report()
    {
        return view('docs.member-cancellation-report');
    }
    public function appointment_report()
    {
        return view('docs.appointment-report');
    }
    public function member_hold_report()
    {
        return view('docs.member-hold-report');
    }
    public function absent_member_report()
    {
        return view('docs.absent-member-report');
    }
    public function not_wo_member_report()
    {
        return view('docs.not-wo-member-report');
    }

    public function sms()
    {
        return view('docs.sms');
    }
    public  function wa()
    {
        return view('docs.wa');
    }
    public function message_for_member()
    {
        return view('docs.message-for-member');
    }

    public function login_manual()
    {
        return view('docs.login-manual');
    }

    public function travel_pass()
    {
        return view('docs.travel-pass');
    }

    public function staff_report()
    {
        return view('docs.staff-report');
    }
    public function club_assessment()
    {
        return view('docs.club-assessment');
    }
    public function projection_sheet()
    {
        return view('docs.projection-sheet');
    }
    public function invoice_product()
    {
        return view('docs.invoice-product');
    }

    public function product_request()
    {
        return view('docs.product-request');
    }
    public function community_partner()
    {
        return view('docs.community-partner');
    }
    public function locker_rental()
    {
        return view('docs.locker-rental');
    }

    public function workout_member_monitoring()
    {
        return view('docs.workout-member-monitoring');
    }
    public function birthday_member_monitoring()
    {
        return  view('docs.birthday-member-monitoring');
    }

    public function product()
    {
        return view('docs.product');
    }

    public function financial_management()
    {
        return view('docs.financial-management');
    }

    public function refund()
    {
        return view('docs.refund');
    }

    public function member_hold()
    {
        return view('docs.member-hold');
    }

    public function create_new_member()
    {
        return view('docs.create-new-member');
    }
    public function weight_and_measurement()
    {
        return view('docs.weight-and-measurement');
    }
    public function payment()
    {
        return view('docs.payment');
    }

    public function change_club()
    {
        return view('docs.change-club');
    }
    public function extend_membership()
    {
        return view('docs.extend-membership');
    }
    public function membership_cancellation()
    {
        return view('docs.membership-cancellation');
    }
    public function other_setting()
    {
        return view('docs.other-setting');
    }
    public function login()
    {
        return view('docs.login');
    }
    public function password_recovery()
    {
        return view('docs.password-recovery');
    }

}