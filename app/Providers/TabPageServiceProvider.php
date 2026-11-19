<?php

namespace App\Providers;

use Request;
use Illuminate\Support\ServiceProvider;

class TabPageServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $tab_title = '';
        if (Request::segment(2) == 'dashboard'):
            $tab_title = 'labels.main_menu';
        elseif (Request::segment(2) == 'member'):
            $tab_title = 'labels.member';
        elseif (Request::segment(2) == 'payment'):
            $tab_title = 'labels.payment';
        elseif (Request::segment(2) == 'weigh-measure'):
            $tab_title = 'labels.weigh_n_measure';
        elseif (Request::segment(2) == 'lead'):
            $tab_title = 'labels.lead';
        elseif (Request::segment(2) == 'new-member'):
            $tab_title = 'labels.new_member';
        elseif (Request::segment(2) == 'cancelling-membership'):
            $tab_title = 'labels.membership_cancellation';
        elseif (Request::segment(2) == 'member-hold'):
            $tab_title = 'labels.member_hold';
        elseif (Request::segment(2) == 'member-message-note'):
            $tab_title = 'labels.member_message_note';
        elseif (Request::segment(2) == 'product'):
            $tab_title = 'labels.product';
        elseif (Request::segment(2) == 'refund'):
            $tab_title = 'labels.refund';
        elseif (Request::segment(2) == 'laporan'):
            $tab_title = 'labels.report';
        elseif (Request::segment(2) == 'appointment'):
            $tab_title = 'labels.appointment';
        elseif (Request::segment(2) == 'attendance'):
            $tab_title = 'labels.appointment_attendace';
        elseif (Request::segment(2) == 'pre-member'):
            $tab_title = 'labels.pre_member';
        elseif (Request::segment(2) == 'settings'):
            $tab_title = 'labels.setting';
        elseif (Request::segment(2) == 'club'):
            $tab_title = 'labels.club';
        elseif (Request::segment(2) == 'staff'):
            $tab_title = 'labels.staff';
        elseif (Request::segment(2) == 'product-setting'):
            $tab_title = 'labels.product_setting';
        elseif (Request::segment(2) == 'master-product'):
            $tab_title = 'labels.product';
        elseif (Request::segment(2) == 'product-inventory'):
            $tab_title = 'labels.product_inventory';
        elseif (Request::segment(2) == 'voucher'):
            $tab_title = 'labels.voucher';
        elseif (Request::segment(2) == 'tax-type'):
            $tab_title = 'labels.tax_type';
        elseif (Request::segment(2) == 'access-menu'):
            $tab_title = 'labels.access_menu';
        elseif (Request::segment(2) == 'roles'):
            $tab_title = 'labels.role';
        elseif (Request::segment(2) == 'attributes'):
            $tab_title = 'labels.attributes';
        elseif (Request::segment(2) == 'predefined-message'):
            $tab_title = 'labels.predefined_message';
        elseif (Request::segment(2) == 'document'):
            $tab_title = 'labels.document';
        elseif (Request::segment(2) == 'staff-report'):
            $tab_title = 'labels.staff_report';
        elseif (Request::segment(2) == 'challenges'):
            $tab_title = 'labels.challenge';
        elseif (Request::segment(2) == 'transfer-staff'):
            $tab_title = 'labels.transfer_staff';
        elseif (Request::segment(2) == 'event'):
            $tab_title = 'labels.global_event';
        elseif (Request::segment(2) == 'corporate'):
            $tab_title = 'labels.corporate';
        elseif (Request::segment(2) == 'manual-login'):
            $tab_title = 'labels.manual_login';
        elseif (Request::segment(2) == 'membership-changing'):
            $tab_title = 'labels.membership_changing';
        elseif (Request::segment(2) == 'continue-membership'):
            $tab_title = 'labels.continue_membership';
        elseif (Request::segment(2) == 'community-partner'):
            $tab_title = 'labels.community_partner';
        elseif (Request::segment(2) == 'debt-list'):
            $tab_title = 'labels.debt';
        elseif (Request::segment(2) == 'financial-management'):
            $tab_title = 'labels.financial_management';
        elseif (Request::segment(2) == 'locker'):
            $tab_title = 'labels.locker';
        elseif (Request::segment(2) == 'product-request'):
            $tab_title = 'labels.product_request';
        elseif (Request::segment(2) == 'club-assessment'):
            $tab_title = 'labels.club_assessment';
        elseif (Request::segment(2) == 'pj'):
            $tab_title = 'labels.projection_sheet';
        elseif (Request::segment(2) == 'product-inventory-correction'):
            $tab_title = 'labels.product';
        elseif (Request::segment(2) == 'membership-types'):
            $tab_title = 'labels.membership_type';
        elseif (Request::segment(2) == 'certification'):
            $tab_title = 'labels.certification';
        elseif (Request::segment(2) == 'other-settings'):
            $tab_title = 'labels.other_setting';
        elseif (Request::segment(2) == 'change-password-club-list'):
            $tab_title = 'labels.change_password';
        elseif (Request::segment(2) == 'change-password-club-detail'):
            $tab_title = 'labels.change_password';
        elseif (Request::segment(2) == 'travel-pass'):
            $tab_title = 'labels.travel_pass';
        elseif (Request::segment(2) == 'travel-pass-new-request'):
            $tab_title = 'labels.travel_pass';
        elseif (Request::segment(2) == 'travel-pass-active-list'):
            $tab_title = 'labels.travel_pass';
        elseif (Request::segment(2) == 'travel-pass-finished-list'):
            $tab_title = 'labels.travel_pass';
        elseif (Request::segment(2) == 'visiting-member'):
            $tab_title = 'labels.visiting_member';
        elseif (Request::segment(2) == 'visiting-member-inactive'):
            $tab_title = 'labels.visiting_member';
        elseif (Request::segment(2) == 'message'):
            $tab_title = 'labels.message';
        elseif (Request::segment(2) == 'whatsapp'):
            $tab_title = 'labels.message';
        elseif (Request::segment(2) == 'sms'):
            $tab_title = 'labels.message';
        elseif (Request::segment(2) == 'auto-assign-member'):
            $tab_title = 'labels.coaches';
        elseif (Request::segment(2) == 'coach-lead-setting'):
            $tab_title = 'labels.administrator_lead';
        elseif (Request::segment(2) == 'coach-sequence'):
            $tab_title = 'labels.coach_sequence';
        elseif (Request::segment(2) == 'curves-learning'):
            $tab_title = 'labels.curves_learning';
        elseif (Request::segment(2) == 'cashier'):
            $tab_title = 'labels.cashier';
        elseif (Request::segment(2) == 'bank'):
            $tab_title = 'labels.bank';
        elseif ((Request::segment(1) == 'cf-lite') && (Request::segment(2) == 'accounting')):
            $tab_title = 'labels.accounting';
        elseif (Request::segment(2) == 'guest-membership'):
            $tab_title = 'labels.membership_promo';
        elseif (Request::segment(2) == 'guest-member'):
            $tab_title = 'labels.promo_lead';
        elseif (Request::segment(2) == 'publicity'):
            $tab_title = 'labels.publicity';
        elseif (Request::segment(2) == 'coach-target'):
            $tab_title = 'labels.coach_target';
        elseif (Request::segment(2) == 'digital-product'):
            $tab_title = 'labels.digital_product';
        elseif (Request::segment(2) == 'master'):
            if (Request::segment(3) == 'jubilant'):
                $tab_title = 'labels.jubilant';
            endif;
        elseif (Request::segment(1) == 'cf-acc'):
            if (Request::segment(3) == 'chart-of-account'):
                $tab_title = 'labels.chart_of_account';
            elseif (Request::segment(3) == 'cost-revenue'):
                $tab_title = 'labels.master_cost_revenue';
            elseif (Request::segment(2) == 'general-ledger'):
                $tab_title = 'labels.general_ledger';
            elseif (Request::segment(2) == 'memorial-journal'):
                $tab_title = 'labels.memorial_journal';
            elseif (Request::segment(2) == 'ledger'):
                $tab_title = 'labels.ledger';
            elseif (Request::segment(2) == 'income-statement'):
                $tab_title = 'labels.income_statement';
            elseif (Request::segment(2) == 'trial-balance'):
                $tab_title = 'labels.trial_balance';
            elseif (Request::segment(2) == 'cash-flow'):
                $tab_title = 'labels.cash_flow';
            elseif (Request::segment(2) == 'bank-flow'):
                $tab_title = 'labels.bank_flow';
            elseif (Request::segment(2) == 'purchasing-book'):
                $tab_title = 'labels.purchasing_book';
            elseif (Request::segment(2) == 'transaction'):
                $tab_title = 'labels.manual_cost_revenue';
            elseif (Request::segment(2) == 'financial-statement'):
                if (Request::segment(3) == 'cashflow-statement'):
                    $tab_title = 'labels.cashflow_statement';
                elseif (Request::segment(3) == 'balance'):
                    $tab_title = 'labels.neraca';
                elseif (Request::segment(3) == 'income-statement'):
                    $tab_title = 'labels.income_statement';
                endif;
            elseif (Request::segment(2) == 'accounting'):
                if (Request::segment(3) == 'cost-revenue-center'):
                    $tab_title = 'labels.cost_revenue_center';
                elseif (Request::segment(3) == 'cost-revenue'):
                    $tab_title = 'labels.cost_revenue';
                elseif (Request::segment(3) == 'beginning-balance'):
                    $tab_title = 'labels.beginning_balance';
                endif;
            endif;
        elseif (Request::segment(2) == 'hq'):
            $tab_title = 'labels.hq_credit';
        endif;

        view()->share('tab_title', $tab_title);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
