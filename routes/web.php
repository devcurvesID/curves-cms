<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
   return redirect('cf-lite/dashboard');
   //return view('welcome');
});

/*Route::get('login-curves',function (){
    return view('auth.login_curves');
});*/

// CF LITE
Route::group(['middleware' => ['web']], function () {
    Auth::routes();
    Route::get('/home', 'HomeController@index')->name('home');

    //Staff Home
    Route::get('cf-lite/dashboard', 'CFMainController@index');

    Route::post('cf-lite/set-staff-session', 'AjaxController@set_staff_session');
    //End of Staff Home

    //1. Appointment
    Route::get('cf-lite/appointment', 'AppointmentController@index');
    Route::get('cf-lite/appointment/book', 'AppointmentController@book_choose_date');
    Route::get('cf-lite/appointment/book/{date}', 'AppointmentController@bulk_appointment_list');
    Route::post('cf-lite/appointment/book/{date}', 'AppointmentController@store_bulk_contacted_leads');
    Route::get('cf-lite/appointment/attendance', 'AppointmentController@attendance_main');
//    Route::get('cf-lite/appointment/attendance/{date}', 'AppointmentController@bulk_attendance_list');
    Route::post('cf-lite/appointment/attendance/{date}', 'AppointmentController@store_bulk_attendance_leads');
    Route::post('cf-lite/appointment', 'AppointmentController@create_appointment_ver_2');

    //Fitness Assessment
    Route::get('cf-lite/appointment/fa/{date}/{time}', 'AppointmentController@fa_choose_lead');
    Route::get('cf-lite/appointment/fa/lead', 'AppointmentController@fa_lead_list');
    Route::get('cf-lite/appointment/bulk/fa/lead', 'AppointmentController@fa_lead_list_bulk');
    Route::post('cf-lite/lead/add', 'LeadController@store_lead');
    Route::get('cf-lite/appointment/submit/fa/{id}', 'AppointmentController@fa_submit_detail');
    Route::post('cf-lite/appointment/submit/fa', 'AppointmentController@store_fa');

    Route::get('cf-lite/appointment/fa/br', 'AppointmentController@fa_br_list');
    Route::post('cf-lite/br/add', 'LeadController@store_br');

    //Weigh & Measures
    Route::get('cf-lite/appointment/wm/{date}/{time}', 'AppointmentController@wm_member_list');
    Route::get('cf-lite/appointment/wo/{date}/{time}', 'AppointmentController@wo_member_list');
    Route::get('cf-lite/appointment/submit/wm-wo/{id}', 'AppointmentController@wm_wo_submit_detail');
    Route::post('cf-lite/appointment/submit/wm-wo', 'AppointmentController@store_wm_wo');

    //Change Status
    Route::get('cf-lite/appointment/status/fa/{fa}', 'AppointmentController@fa_status');
    Route::get('cf-lite/appointment/status/ma/{ma}', 'AppointmentController@ma_status');
    Route::post('cf-lite/appointment/{id}/status', 'AppointmentController@update_status');
    Route::patch('cf-lite/appointment/status/fa/{fa}', 'AppointmentController@store_fa_reschedule');
    Route::patch('cf-lite/appointment/status/ma/{ma}', 'AppointmentController@store_ma_reschedule');

    //Undo Appointment
    Route::get('cf-lite/appointment/undo/{id}','AppointmentController@undo_show_sale');

    //End of Appointment

    //2. Pre-Member
    Route::get('cf-lite/pre-member', 'PreMemberController@index');
    Route::get('cf-lite/pre-member/{id}', 'PreMemberController@premember_detail');
    Route::get('cf-lite/pre-member/{id}/membership', 'PreMemberController@premember_choose_membership');
    //End of Pre-Member

    //3. Payment
    Route::get('cf-lite/payment', 'PaymentController@index');
    Route::get('cf-lite/payment/membership/{id}', 'PaymentController@payment_fa_detail');
    Route::post('cf-lite/payment/membership/{id}', 'PaymentController@store_first_payment');
    Route::post('cf-lite/get-tax-config-detail', 'PaymentController@ajax_get_tax_config_detail');
    Route::get('cf-lite/payment/debt/{id}', 'PaymentController@payment_debt_detail');
    Route::post('cf-lite/payment/debt/{id}', 'PaymentController@store_payment_debt');
    Route::get('cf-lite/payment/installment', 'PaymentController@installment_member_list');
    Route::get('cf-lite/payment/installment/{id}', 'PaymentController@installment_member_detail');
    Route::get('cf-lite/payment/new-installment-setting/{id}', 'PaymentController@new_installment_setting');
    Route::post('cf-lite/payment/new-installment-setting/{id}', 'PaymentController@store_installment_setting');
    Route::get('cf-lite/payment/paid-list/{id}', 'PaymentController@paid_list');

    Route::get('cf-lite/payment/bill-list/{id}', 'PaymentController@bill_list');
    Route::patch('cf-lite/payment/bill-list/{id}', 'PaymentController@accept_installment_payment');
    Route::post('cf-lite/payment/bill-list/{id}/ezypay-customer', 'PaymentController@prepareInstallmentEzypayCustomer')
        ->middleware('auth')->name('payment.bill-list.ezypay-customer');
    Route::post('cf-lite/payment/bill-list/{id}/ezypay-card', 'PaymentController@storeBillEzypayCard')
        ->middleware('auth')->name('payment.bill-list.ezypay-card');
    Route::get('cf-lite/payment/bill-list-test/{id}', 'PaymentController@bill_list_test');
    Route::patch('cf-lite/payment/bill-list-test/{id}', 'PaymentController@accept_installment_payment_test');

    Route::get('cf-lite/payment/invoice/{id}/member', 'InvoiceController@member_payment_invoice');
    Route::patch('cf-lite/payment/invoice/{id}/member', 'InvoiceController@edit_member_payment_invoice');
    Route::get('cf-lite/payment/invoice/{id}/detail', 'PaymentController@bill_detail');
    Route::patch('cf-lite/payment/invoice/{id}/detail', 'PaymentController@update_bill_detail');
    Route::post('cf-lite/payment/invoice/{id}/member', 'InvoiceController@member_payment_installment_invoice');
    //End of Payment

    //4. Leads
	Route::get('cf-lite/lead', 'LeadController@lead_listv2');
    Route::patch('cf-lite/lead/edit-ga/{id}','LeadController@edit_lead_ga');
    Route::post('cf-lite/lead/add-ga/{id}','LeadController@add_lead_ga');
    Route::delete('cf-lite/lead/delete/{id}','LeadController@delete_lead');
    Route::patch('cf-lite/lead','LeadController@update_lead_data');
    Route::get('cf-lite/lead/choose', 'LeadController@choose');
    Route::get('cf-lite/lead/add/{type}/member/{id}', 'LeadController@bulk_add_leads_from_member');
    //Route::get('cf-lite/lead/add/{type}', 'LeadController@bulk_add_leads');
    //Route::post('cf-lite/lead/add', 'LeadController@store_bulk_leads');
	Route::get('cf-lite/lead/add/{type}', 'LeadController@bulk_add_leads_v2');
    Route::post('cf-lite/lead/add', 'LeadController@store_bulk_leads_v2');
    Route::get('cf-lite/lead/{id}/complete-fa-data', 'LeadController@complete_fa_data');
    Route::patch('cf-lite/lead/{id}/complete-fa-data', 'LeadController@lead_update');
	Route::patch('cf-lite/lead/set-fa','LeadController@set_fa');

    Route::post('cf-lite/get-lead-detail', 'AjaxController@get_lead_detail');
	Route::post('cf-lite/get-fa-detail','AjaxController@get_fa_detail');

    Route::get('cf-lite/lead/br-referrer','LeadController@br_referrer_list');
    Route::patch('cf-lite/lead/br-referrer/{id}','LeadController@store_lead_referrer');

        //import leads
    Route::get('cf-lite/lead/import-leads', 'LeadController@import_view');
    Route::post('cf-lite/lead/import-leads', 'LeadController@import_leads');
    //End of Leads

    //5. Weigh And Measure
    Route::get('cf-lite/weigh-measure', 'WeighAndMeasureController@wm_member_list');
    Route::get('cf-lite/weigh-measure/all-club', 'WeighAndMeasureController@wm_member_list_all_club');
    Route::get('cf-lite/weigh-measure/{id}/add', 'WeighAndMeasureController@add_new_wm');
    Route::post('cf-lite/weigh-measure/{id}/add', 'WeighAndMeasureController@store_new_wm_data');
    Route::get('cf-lite/weigh-measure/{id}/history', 'WeighAndMeasureController@wm_detail_list');
    Route::get('cf-lite/weigh-measure/{id}/detail', 'WeighAndMeasureController@wm_detail');
    Route::patch('cf-lite/weigh-measure/{id}/detail', 'WeighAndMeasureController@update_wm_data');
    Route::get('cf-lite/weigh-measure/{id}/printout', 'WeighAndMeasureController@wm_printout');
    Route::get('cf-lite/weigh-measure/{id}/add/goal', 'WeighAndMeasureController@add_new_wm_goal');
    Route::post('cf-lite/weigh-measure/{id}/add/goal', 'WeighAndMeasureController@store_new_wm_goal_data');
    Route::get('cf-lite/weigh-measure/{id}/history/goal', 'WeighAndMeasureController@wm_goal_detail_list');
    Route::get('cf-lite/weigh-measure/{id}/detail/goal', 'WeighAndMeasureController@wm_goal_detail');
    Route::get('cf-lite/weigh-measure/{id}/edit/goal', 'WeighAndMeasureController@edit_wm_goal');
    Route::patch('cf-lite/weigh-measure/{id}/edit/goal', 'WeighAndMeasureController@update_wm_goal_data');
    Route::delete('cf-lite/weigh-measure/{id}/history','WeighAndMeasureController@delete_wm');
    Route::get('cf-lite/weigh-measure/{member_id}/schedule', 'WeighAndMeasureController@wm_schedule');
    Route::post('cf-lite/weigh-measure/{member_id}/schedule', 'WeighAndMeasureController@store_wm_schedule');
    Route::patch('cf-lite/weigh-measure/{member_id}/schedule', 'WeighAndMeasureController@update_wm_schedule');
    //End of Weigh and Measure

    //6. New Member
    Route::get('cf-lite/new-member/search', 'MemberController@new_member_date');
    Route::get('cf-lite/new-member', 'MemberController@new_member');

    //6a. Walk-in
    Route::post('cf-lite/new-member', 'WalkInController@store_walk_in');

    Route::post('cf-lite/add-member-walk-in', 'WalkInController@ajax_walk_in');
    //End of Walk-in
    //End of New Member

    //7. Data Member
    Route::get('cf-lite/member/list/search', 'MemberController@member_list_search');
    Route::get('cf-lite/member/list', 'MemberController@member_list');
    Route::get('cf-lite/member/{id}/edit', 'MemberController@member_edit');
    Route::patch('cf-lite/member/{id}/edit', 'MemberController@member_update');
    Route::get('cf-lite/member/{id}/album', 'MemberController@member_album');
    Route::post('cf-lite/member/{id}/album', 'MemberController@add_member_photo');
    Route::delete('cf-lite/member/{id}/album', 'MemberController@delete_member_photo');

    Route::get('cf-lite/member/{id}/transfer-member','MemberController@transfer_member_detail');
    Route::patch('cf-lite/member/transfer-member/{id}','MemberController@transfer_member');

    Route::post('cf-lite/member/{id}/converting-member', 'MemberController@convert_member_into_staff');
    Route::post('cf-lite/qr-broadcast-ajax', 'MemberController@qr_broadcast_ajax');

    //7a. Member Status Setting
    Route::get('cf-lite/member/status-setting', 'MemberController@member_status_setting_list');
    Route::patch('cf-lite/member/status-setting', 'MemberController@bulk_update_member_status_setting');

    Route::post('cf-lite/member/add-duration_to_club', 'MemberController@add_membership_duration_to_clubs');
    //End of Member Status Setting
    //End of Data Member

    //8. Manual Login
    Route::get('cf-lite/manual-login/choose', function () {
        return view('cf_lite.manual-login.choose-manual-login');
    });
    Route::post('cf-lite/manual-login/staff', 'ManualAttendanceController@staff_login');
    Route::get('cf-lite/manual-login/sign-out', 'ManualAttendanceController@staff_login_list');
    Route::post('cf-lite/manual-login/sign-out', 'ManualAttendanceController@staff_logout');
    Route::get('cf-lite/manual-login', 'WorkoutController@show_result_login_member');

    Route::get('cf-lite/manual-login/{id}','WorkoutController@past_wo');
    Route::post('cf-lite/manual-login/store_past_wo','WorkoutController@store_past_wo')->name('cf-lite.manual-login.store-past-wo');
    //End of Manual Login

    //9. Application Setting
    Route::get('cf-lite/settings', 'SettingController@setting_main');

    //9a. Club
    Route::get('cf-lite/club', 'ClubController@club_list');
    Route::get('cf-lite/club/add', 'ClubController@add_new_club');
    Route::post('cf-lite/club/add', 'ClubController@store_new_club');
    Route::get('cf-lite/club/{id}', 'ClubController@club_detail');
    Route::patch('cf-lite/club/{id}', 'ClubController@update_club_detail');
    Route::get('cf-lite/club/{id}/tax-config', 'ClubController@club_tax_config');
    Route::post('cf-lite/club/{id}/tax-config', 'ClubController@store_club_tax_config');
    Route::patch('cf-lite/club/{id}/tax-config', 'ClubController@update_club_tax_config');
    //for accurate branch
    Route::get('cf-lite/club/branches/{id}', 'ClubController@accurate_branches');

    //End of Club

    //9b. Staff
    Route::get('cf-lite/staff', 'StaffController@club_list');
    Route::get('cf-lite/staff/{club_id}', 'StaffController@staff_list');
    Route::get('cf-lite/staff/{club_id}/add', 'StaffController@add_new_staff');
    Route::post('cf-lite/staff/{club_id}/add', 'StaffController@store_new_staff');
    Route::get('cf-lite/staff/{id}/detail', 'StaffController@staff_detail');
    Route::patch('cf-lite/staff/{id}/detail', 'StaffController@staff_update');
    Route::delete('cf-lite/staff/{id}/detail', 'StaffController@set_staff_inactive');
    Route::get('cf-lite/staff/{id}/inactive-staff-list', 'StaffController@inactive_staff_list');
    Route::post('cf-lite/staff/{staffinactive_id}/activate-permission-member', 'StaffController@activate_permission_member');
    Route::post('cf-lite/staff/reactivate-resigned-member', 'StaffController@reactivate_resigned_member');
    Route::get('cf-lite/staff/{id}/manage-password', 'StaffController@manage_password');
    Route::patch('cf-lite/staff/{id}/manage-password', 'StaffController@update_password');
    Route::post('cf-lite/staff/{id}/reset-password','StaffController@reset_password');
    Route::get('cf-lite/staff/{id}/manage-note', 'StaffController@manage_note');
    Route::post('cf-lite/staff/{id}/manage-note', 'StaffController@store_staff_note');
    Route::delete('cf-lite/staff/{note_id}/manage-note', 'StaffController@delete_staff_note');
    Route::post('cf-lite/staff/{id}/manager-changing-club', 'StaffController@manager_class_changing_club');
    Route::get('cf-lite/staff/{id}/manager-club-setting', 'StaffController@manager_club_setting');
    Route::post('cf-lite/staff/{id}/manager-club-setting', 'StaffController@store_manager_club_setting');
    //End of Staff

    //9c. Product Setting
    Route::get('cf-lite/product-setting', 'ProductController@product_setting_page');
    Route::get('cf-lite/master-product/search', 'ProductController@product_search');
    Route::get('cf-lite/master-product', 'ProductController@product_list');
    Route::post('cf-lite/master-product', 'ProductController@store_new_product');
    Route::get('cf-lite/master-product/{id}/edit', 'ProductController@edit_product');
    Route::patch('cf-lite/master-product/{id}/edit', 'ProductController@update_product');
    Route::patch('cf-lite/master-product/{id}/edit/discount', 'ProductController@update_product_discount_global');
    Route::get('cf-lite/master-product/add-products', 'ProductController@add_bulk_product');
    Route::post('cf-lite/master-product/add-products', 'ProductController@store_bulk_product');
    Route::get('cf-lite/master-product/category/{id}/search', 'ProductController@product_search_by_category');
    Route::get('cf-lite/master-product/category/{id}', 'ProductController@product_list_by_category');
    Route::get('cf-lite/product-inventory/club', 'ProductController@product_inventory_choose_club');
    Route::get('cf-lite/product-inventory/{club_id}', 'ProductController@product_inventory');
    Route::get('cf-lite/product-inventory/{club_id}/stock/{product_id}/history', 'ProductController@product_inventory_history');
    Route::get('cf-lite/product-inventory/{club_id}/edit', 'ProductController@edit_inventory');
    Route::patch('cf-lite/product-inventory/{club_id}/edit', 'ProductController@update_bulk_product_inventory');
    Route::get('cf-lite/product-inventory/edit/{inventory_id}', 'ProductController@inventory_detail');
    Route::patch('cf-lite/product-inventory/edit/{inventory_id}', 'ProductController@update_product_inventory_detail');
    Route::patch('cf-lite/product-inventory/edit/{inventory_id}/discount', 'ProductController@update_product_discount');

    Route::get('cf-lite/product-inventory-correction/club', 'ProductController@product_inventory_correction_choose_club');
    Route::get('cf-lite/product-inventory-correction/{club_id}', 'ProductController@edit_inventory_correction');
    Route::patch('cf-lite/product-inventory-correction/{club_id}/edit', 'ProductController@update_bulk_product_inventory_correction');

    Route::get('cf-lite/master-product/add-promo', 'ProductController@add_promo_product');
    Route::post('cf-lite/master-product/add-promo', 'ProductController@store_promo_product');
    Route::post('cf-lite/update-promo-product', 'ProductController@update_promo_product_ajax');

    //End of Product Setting

    //9d. Voucher
    Route::get('cf-lite/voucher', 'VoucherController@voucher_type_list');
    Route::post('cf-lite/voucher', 'VoucherController@store_voucher_type');
    Route::get('cf-lite/voucher/list/{id}/filter', 'VoucherController@voucher_list_filter');
    Route::get('cf-lite/voucher/list/{id}', 'VoucherController@voucher_list');
    Route::patch('cf-lite/voucher/list/{id}', 'VoucherController@update_voucher');
    Route::get('cf-lite/voucher/add', 'VoucherController@voucher_bulk_add');
    Route::post('cf-lite/voucher/add', 'VoucherController@store_voucher');
    //End of Voucher

    //9e. Tax Type Setting
    Route::get('cf-lite/tax-type', 'TaxController@tax_type_list');
    Route::post('cf-lite/tax-type', 'TaxController@store_tax_type');
    Route::patch('cf-lite/tax-type/{id}', 'TaxController@update_tax_type_status');
    //End of Tax Type Setting

    //9f. Access Menu Setting
    Route::get('cf-lite/access-menu/club', 'AccessMenuController@access_menu_club_list');
    Route::get('cf-lite/access-menu/club/{club_id}', 'AccessMenuController@access_menu_staff_list');
    Route::get('cf-lite/access-menu/staff/{staff_id}', 'AccessMenuController@access_menu_config');
    Route::patch('cf-lite/access-menu/staff/{staff_id}', 'AccessMenuController@update_staff_access_setting');
    //End of Access Menu Setting

    //9g. Predefined Message
    Route::get('cf-lite/predefined-message', 'PredefinedMessageController@predefined_message_list');
    Route::post('cf-lite/predefined-message', 'PredefinedMessageController@store_predefined_message');
    Route::patch('cf-lite/predefined-message', 'PredefinedMessageController@update_predefined_message');
    Route::delete('cf-lite/predefined-message/{id}', 'PredefinedMessageController@delete_predefined_message');

    Route::post('cf-lite/get-predefined-message-detail', 'AjaxController@get_predefined_message_detail');
    //End of Predefined Message

    //9h. Thirty Days
    /*Route::get('cf-lite/thirty-days', 'ThirtyDays@thirty_days_list');*/
    //End of Thirty Days
    //End of Application Setting

    //10. REPORT
    Route::get('cf-lite/laporan', 'ReportController@index');
    Route::get('cf-lite/laporan/ulang-tahun', 'ReportController@birthday');
    Route::get('cf-lite/laporan/ulang-tahun-staff', 'ReportController@staff_birthday');
    Route::get('cf-lite/laporan/wo', 'ReportController@list_wo');
    Route::get('cf-lite/laporan/wo-filtered', 'ReportController@list_wo_filter');
    Route::get('cf-lite/laporan/wo-member-detail/{id}', 'ReportController@list_wo_filter_member');
    Route::get('cf-lite/laporan/appointment', 'ReportController@list_appointment');
    Route::get('cf-lite/laporan/member', 'ReportController@list_member');
    Route::get('cf-lite/laporan/member/{status}', 'ReportController@list_member_filter');
    Route::get('cf-lite/laporan/penjualan-staff', 'ReportController@list_staff_selling');
    Route::get('cf-lite/laporan/penjualan-staff/{staff}/{date1}/{date2}', 'ReportController@list_staff_selling_filter');

    Route::get('cf-lite/laporan/staff-fa-success','ReportController@list_fa_succes_staff');
    Route::get('cf-lite/laporan/staff-fa-success-filter','ReportController@staff_fa_date_filter');

    Route::get('cf-lite/laporan/wm-difference', 'ReportController@list_comparison_wm');
    Route::get('cf-lite/laporan/challenge', 'ReportController@choose_challenge');
    Route::get('cf-lite/laporan/challenge/{challenge}/{date1}/{date2}', 'ReportController@result_challenge');
    Route::get('cf-lite/laporan/reward', 'ReportController@list_reward');
    Route::get('cf-lite/laporan/reward/{id}/{date1}/{date2}', 'ReportController@list_reward_filter');
    Route::get('cf-lite/laporan/cancellation', 'ReportController@list_cancellation');
    Route::get('cf-lite/laporan/cancellation/{id}/{date1}/{date2}', 'ReportController@list_cancellation_filter');
    Route::get('cf-lite/laporan/member-hold/filter', 'ReportController@member_hold_filter');
    Route::get('cf-lite/laporan/member-hold', 'ReportController@member_hold_list');
    Route::get('cf-lite/laporan/absent-member/filter', 'ReportController@absent_member_filter');
    Route::get('cf-lite/laporan/absent-member', 'ReportController@absent_report_list');
    Route::get('cf-lite/laporan/traffic-control', 'ReportController@traffic_control_main');
    Route::get('cf-lite/laporan/source-report', 'ReportController@fa_source_main');
    Route::get('cf-lite/laporan/source-report/no-sale-reason', 'ReportController@fa_source_nosale_reason');

    Route::get('cf-lite/laporan/wo-time','ReportController@wo_time_list');
    Route::get('cf-lite/laporan/wo-time-checker','ReportController@wo_time_list_check');
    Route::get('cf-lite/laporan/wo-time-filter','ReportController@wo_time_list_filter');
    Route::get('cf-lite/laporan/membership-type','ReportController@membership_type_list_v2');
    Route::get('cf-lite/laporan/membership-type/{id}','ReportController@membership_type_each_list');
    Route::get('cf-lite/laporan/membership-type-filter','ReportController@membership_type_list_filter_v2');

    Route::get('cf-lite/laporan/wo-flag','ReportController@wo_flag_list');
    Route::get('cf-lite/laporan/wo-count-avg','ReportController@wo_count_average_list');

    Route::get('cf-lite/laporan/tshirt-size','ReportController@tshirt_size_list');
    Route::get('cf-lite/laporan/tshirt-size-detail/{id}','ReportController@tshirt_size_detail');

    Route::get('cf-lite/laporan/top-weight-loser', 'ReportController@top_weight_loser');

    Route::get('cf-lite/laporan/product-report','ReportController@product_report_list');

    Route::get('cf-lite/laporan/not-fwo-yet', 'ReportController@member_not_fwo');

    Route::get('cf-lite/laporan/wo-member-report','ReportController@wo_member_report');
    Route::get('cf-lite/laporan/wo-member-report-filter','ReportController@wo_member_report_filter');

    Route::get('cf-lite/laporan/source-cancel-report', 'ReportController@fa_source_cancel');

    Route::get('cf-lite/laporan/locker-sales-report','ReportController@locker_sale_report');

    Route::get('cf-lite/laporan/member-data-report','ReportController@member_report_to_excel');

    Route::get('cf-lite/laporan/staff-attendance-report','ReportController@staff_attendance_report');

    //11. Cancelling Membership
    Route::get('cf-lite/cancelling-membership', function () {
        return view('cf_lite.cancelling-membership.cancelling-membership-main-v2');
    });
    Route::get('cf-lite/cancelling-membership/{id}/detail', 'CancellingMembershipController@cancellation_detail');
    Route::post('cf-lite/cancelling-membership/{id}/detail', 'CancellingMembershipController@create_refund');
    Route::get('cf-lite/cancelling-membership/request', 'CancellingMembershipController@cancellation_request');
    Route::get('cf-lite/cancelling-membership/request/{member_id}', 'CancellingMembershipController@cancellation_request_detail');
    Route::post('cf-lite/cancelling-membership/request/{member_id}', 'CancellingMembershipController@create_cancellation_submission');
    Route::get('cf-lite/cancelling-membership/list', 'CancellingMembershipController@cancellation_list');
    Route::get('cf-lite/continue-membership/{member_id}/reactivate', 'ContinueMembershipController@reactivate');

    //11a. Inactivated Member
    Route::get('cf-lite/cancelling-membership/inactive-member-list', 'CancellingMembershipController@inactive_member_list');
    Route::post('cf-lite/cancelling-membership/inactive-member-list', 'CancellingMembershipController@inactivate_member');

    //12. Refund
    Route::get('cf-lite/refund', 'RefundController@refund_list');
//    Route::get('cf-lite/refund/{id}/detail', 'RefundController@refund_detail');
//    Route::post('cf-lite/refund/{id}/detail', 'RefundController@refund_process');
    Route::get('cf-lite/refund/{id}/continue-membership', 'RefundController@continue_membership_refund_detail');
    Route::get('cf-lite/refund/{id}/cancellation-detail', 'RefundController@refund_cancellation_detail');
    Route::post('cf-lite/refund/{id}/cancellation-detail', 'RefundController@refund_cancellation_process');

    //13. Pembayaran & Penjualan Produk
    //Route::get('cf-lite/product', 'ProductController@index');
    Route::get('cf-lite/product/type', 'ProductController@type_index');
    Route::get('cf-lite/product/type/create', 'ProductController@type_create');
    Route::post('cf-lite/product/type', 'ProductController@type_store');
    Route::patch('cf-lite/product/type/{type}', 'ProductController@type_update');
    Route::delete('cf-lite/product/type/{type}', 'ProductController@type_destroy');
    //End of Pembayaran & Penjualan Produk

    //14. Product
    Route::get('cf-lite/product/f', 'ProductController@sales_product_list_filtered');
    Route::get('cf-lite/product', 'ProductController@sales_product_list');
    Route::post('cf-lite/product/{id}/add-cart', 'ProductController@add_to_cart');
    Route::get('cf-lite/product/cart', 'ProductController@cart_list');
    Route::patch('cf-lite/product/cart', 'ProductController@update_cart_qty');
    Route::delete('cf-lite/product/cart', 'ProductController@remove_from_cart');
    Route::get('cf-lite/product/cart/payment', 'ProductController@cart_payment');
    Route::post('cf-lite/product/cart/payment', 'ProductController@store_product_payment');
    Route::post('cf-lite/verify-voucher-code-product', 'AjaxController@ajax_check_voucher_product');
    Route::get('cf-lite/product/cart/payment/{product_payment_id}/detail', 'ProductController@product_payment_detail');
    //End of Product


    //15. Member Hold
    Route::get('cf-lite/member-hold', 'MemberHoldController@member_hold_main');
    Route::get('cf-lite/member-hold/request/add', 'MemberHoldController@add_new_request');
    Route::post('cf-lite/member-hold/request/add', 'MemberHoldController@store_new_member_hold_request');
    Route::get('cf-lite/member-hold/request/list', 'MemberHoldController@hold_request_list');
    Route::patch('cf-lite/member-hold/request/list', 'MemberHoldController@activate_member_hold');
    Route::patch('cf-lite/member-hold/request/{id}/list', 'MemberHoldController@cancel_member_hold_request');
    Route::get('cf-lite/member-hold/request/{id}/edit', 'MemberHoldController@hold_request_detail');
    Route::patch('cf-lite/member-hold/request/{id}/edit', 'MemberHoldController@update_member_hold_request');
    Route::get('cf-lite/member-hold/on-hold/list', 'MemberHoldController@member_hold_list');
    Route::patch('cf-lite/member-hold/on-hold/{id}/list', 'MemberHoldController@finish_member_on_hold');
    Route::get('cf-lite/member-hold/finished/list', 'MemberHoldController@member_hold_finished_list');
    Route::patch('cf-lite/member-hold/finished/list', 'MemberHoldController@member_hold_finished');

    Route::post('cf-lite/get-member-data', 'AjaxController@get_member_detail');
    //End of Member Hold

    //16. Member Message Note & Whatsapp
    Route::get('cf-lite/message', function () {
        return view('cf_lite.message.message-main');
    });
    Route::get('cf-lite/member-message-note/filter', 'MemberController@member_message_note_filter');
    Route::get('cf-lite/member-message-note', 'MemberController@member_message_note_main');
    Route::patch('cf-lite/member-message-note', 'MemberController@update_member_message_note');

    Route::post('cf-lite/get-member-message-note-detail', 'AjaxController@get_member_message_note_detail');
    //End of Member Message Note

    //17. Functional Roles
    Route::get('cf-lite/roles/functional-roles-list','FunctionalRolesController@functional_roles_list');
    Route::delete('cf-lite/roles/delete_functional_role/{id}','FunctionalRolesController@delete_functional_role');

    Route::post('cf-lite/roles/functional-roles-list','FunctionalRolesController@store_functional_role');
    Route::patch('cf-lite/roles/functional-roles-list/{id}','FunctionalRolesController@update_functional_role');
    //End of Functional Roles

    //18. Attributes Setting Menu
    Route::get('cf-lite/attributes/attribute-setting-choice','MemberAttributesController@attribute_setting_choice');

    Route::get('cf-lite/attributes/member-attributes-list','MemberAttributesController@member_attributes_list');
    Route::delete('cf-lite/attributes/delete_member_attributes/{id}','MemberAttributesController@delete_member_attributes');
    Route::post('cf-lite/attributes/member_attributes-list','MemberAttributesController@store_member_attributes');
    Route::patch('cf-lite/attributes/member_attributes-list/{id}','MemberAttributesController@update_member_attributes');

    Route::get('cf-lite/attributes/weight-measures-list','WeightMeasuresController@weight_measures_list');
    Route::delete('cf-lite/attributes/delete_weight-measures/{id}','WeightMeasuresController@delete_weight_measures');
    Route::post('cf-lite/attributes/weight-measures-list','WeightMeasuresController@store_weight_measures');
    Route::patch('cf-lite/attributes/weight-measures-list/{id}','WeightMeasuresController@update_weight_measures');
    //End of Attributes Setting Menu

    //19. Challenges Setting Menu
    Route::get('cf-lite/challenges/challenges-list','ChallengesController@challenges_list');
    Route::get('cf-lite/challenges/challenges-list-filter','ChallengesController@challenges_list_filter');
    Route::patch('cf-lite/challenges/challenges-list','ChallengesController@store_challenges');

    Route::post('cf-lite/challenges/challenges-list-ajax', 'AjaxController@get_challenges_detail');

    //End of Challenges Setting Menu

	//20. Document
    Route::get('cf-lite/document', function () {
        return view('cf_lite.document.document-main');
    });

    //20a. Staff Report
    Route::get('cf-lite/staff-report', 'StaffReportController@staff_report_choose_member');
    Route::get('cf-lite/staff-report/{id}', 'StaffReportController@staff_report_member_detail');
    Route::post('cf-lite/staff-report/{id}', 'StaffReportController@update_staff_report');
    Route::patch('cf-lite/staff-report/{id}', 'StaffReportController@store_thirty_days');
    Route::patch('cf-lite/staff-report/{id}/thirty-days', 'StaffReportController@update_first_thirty_days');

    Route::post('cf-lite/get-staff-report-detail', 'AjaxController@get_staff_report_detail');
    Route::post('cf-lite/get-thirty-days-report-detail', 'AjaxController@get_thirty_days_report_detail');
    //End of Staff Report

    //20b. Invoice
    Route::get('cf-lite/invoice-list','InvoiceController@product_invoice_list');
    Route::get('cf-lite/invoice-list-filtered','InvoiceController@invoice_list_filter');
    Route::get('cf-lite/invoice-detail-list/{id}','InvoiceController@invoice_detail_list');
    Route::get('cf-lite/refund-product-list/{id}','ProductController@refund_product_list');

    Route::get('cf-lite/locker-invoice-list', 'InvoiceController@locker_invoice_list');
    Route::get('cf-lite/locker-invoice-detail-list/{id}', 'InvoiceController@locker_invoice_detail_list');

    Route::patch('cf-lite/refund-product-list/{id}','ProductController@refund_product');
    Route::get('cf-lite/refund-product-list/{id}/detail', 'ProductController@product_refund_detail');

    Route::get('cf-lite/invoice-list-refund','InvoiceController@product_invoice_refund_inventory_list');
    Route::get('cf-lite/invoice-list-refund-filtered','InvoiceController@invoice_refund_inventory_list_filter');
    Route::get('cf-lite/invoice-detail-refund-list/{id}','InvoiceController@invoice_refund_inventory_detail_list');
    Route::post('cf-lite/invoice-detail-refund-list/{id}','ProductController@product_refund_inventory');

    Route::post('cf-lite/invoice-list/detail-ajax', 'InvoiceController@product_invoice_detail_ajax');
    Route::post('cf-lite/invoice-list/store-detail-ajax', 'InvoiceController@store_product_invoice_detail_ajax');
    Route::post('cf-lite/locker-invoice-list/detail-ajax', 'InvoiceController@locker_invoice_detail_ajax');
    Route::patch('cf-lite/invoice-list', 'InvoiceController@update_invoice');
    Route::patch('cf-lite/locker-invoice-list','InvoiceController@update_locker_invoice');
    //End of Invoice

    //20c. Club Assesment
    Route::get('cf-lite/club-assessment', function () {
        return view('cf_lite.document.club-assesment.main');
    });
    Route::get('cf-lite/club-assessment/report', 'CAController@generate_report');
    Route::patch('cf-lite/club-assessment/report/{date}', 'CAController@set_management');
    //End of Club Assesment

    //20d. PJ Sheet
    Route::get('cf-lite/pj', function () {
        return view('cf_lite.document.pj.main');
    });
    Route::get('cf-lite/pj/report', 'PJController@generate_report');
    //End of PJ Sheet

    //End of Document

    //21. Transfer Staff
    Route::get('cf-lite/transfer-staff/choice-mutation', 'StaffController@choose_mutation'); //Edited by Ricky
    Route::get('cf-lite/transfer-staff/staff-rotation/club', 'StaffController@staff_rotation'); //Edited by Ricky
    Route::get('cf-lite/transfer-staff/transfer-staff-list','StaffController@transfer_staff_club_list');
    Route::get('cf-lite/transfer-staff/{club_id}', 'StaffController@staff_transfer_staff_list');
    Route::get('cf-lite/transfer-staff/{id}/detail', 'StaffController@transfer_staff_detail');
    Route::post('cf-lite/transfer-staff/rotation-ajax-from', 'AjaxController@get_staff_name_from'); //Edited by Ricky
    Route::post('cf-lite/transfer-staff/rotation-ajax-to', 'AjaxController@get_staff_name_to'); //Edited by Ricky

    Route::post('cf-lite/transfer-staff/staff-rotation/club', 'StaffController@store_rotation');
    Route::patch('cf-lite/transfer-staff/{id}/detail','StaffController@transfer_staff');

    Route::post('cf-lite/transfer-staff/get-staff-with-member-ajax','AjaxController@get_staff_with_membercoach');
    //End of Transfer Staff

    //22. Global Event
    Route::get('cf-lite/event/global-event-club-list','GlobalEventController@global_event_club_list');

    Route::get('cf-lite/event/global-event-list/{id}','GlobalEventController@global_events_list')->name('cf-lite.event.global-event-list');
    Route::patch('cf-lite/event/global-event-list','GlobalEventController@store_global_event');
    Route::delete('cf-lite/event/delete_global_event/{id}','GlobalEventController@delete_global_event');

    Route::post('cf-lite/event/global-event-list-ajax', 'AjaxController@get_globalevent_detail');
    //End of Global Event

    //23. Corporation
    Route::get('cf-lite/corporate/corporation-options','CorporationController@corporation_options');

    Route::get('cf-lite/corporate/corporation-list','CorporationController@corporation_list');
    Route::post('cf-lite/corporate/corporation-options','CorporationController@store_corporation');
    Route::post('cf-lite/corporate/corporation-list','CorporationController@store_bulk_member_corporation');

    Route::post('cf-lite/corporate/corporate-group-ajax','CorporationController@corporate_group_ajax');

    //Master Corporate
    Route::get('cf-lite/corporate/corporate-mst-list','CorporationController@mst_corporate_list');
    Route::get('cf-lite/corporate/corporate-mst-list-filter','CorporationController@corporation_list_filter');
    Route::patch('cf-lite/corporate/corporate-mst-list','CorporationController@store_new_corporation');
    Route::get('cf-lite/corporate/corporate-mst-detail/{id}','CorporationController@mst_corporate_detail');
    Route::delete('cf-lite/corporate/corporate-mst-detail','CorporationController@delete_member_corporation');

    Route::post('cf-lite/corporate/corporate-group-list-ajax','CorporationController@corporate_group_list_ajax');
    //End of Master Corporate

    //End of Corporation

    //24. Global Message
    Route::get('cf-lite/member/global-message-list','GlobalMessageController@global_message_list')->name('cf-lite.member.global-message-list');;
    Route::patch('cf-lite/member/global-message-list','GlobalMessageController@store_global_message');
    Route::delete('cf-lite/member/delete-global-message/{id}','GlobalMessageController@delete_global_message');

    Route::post('cf-lite/member/global-message-list-ajax', 'AjaxController@get_globalmessage_detail');
    //End of Global Message

    //25. Membership types
    Route::get('cf-lite/membership-types','MembershipTypeController@membership_type_list');
    Route::get('cf-lite/member/member-type-list-filter','MembershipTypeController@membership_type_list_filter');
    Route::patch('cf-lite/membership-types','MembershipTypeController@store_membership_type');

    Route::post('cf-lite/membership-types/membership-type-list-ajax', 'AjaxController@get_membership_type_detail');
    //End of Membership Types

    //26. Locker
    Route::get('cf-lite/locker/f', 'LockerController@locker_sale_list_filtered');
    Route::get('cf-lite/locker/choose-packet/{id}', 'LockerController@sales_locker_list');
    Route::get('cf-lite/locker/cart', 'LockerController@locker_cart');
    Route::get('cf-lite/locker/cart/payment', 'LockerController@cart_payment');
    Route::post('cf-lite/locker/cart/payment', 'LockerController@store_locker_payment');
    Route::get('cf-lite/locker/cart/payment/{locker_payment_id}/detail', 'LockerController@locker_payment_detail');

    Route::get('cf-lite/locker/locker-list','LockerController@locker_list');
    Route::get('cf-lite/locker/locker-list-filter','LockerController@locker_list_filter');
    Route::patch('cf-lite/locker/locker-list','LockerController@store_locker');
    Route::delete('cf-lite/locker/locker-list/{id}','LockerController@delete_locker');

    Route::post('cf-lite/lockers/locker-list-ajax', 'AjaxController@get_locker_detail');

    //Locker Info
    Route::get('cf-lite/locker/locker-setting-choice','LockerController@choose_locker_menu');

    Route::get('cf-lite/locker/club-locker-list','LockerController@club_locker_list');
    Route::get('cf-lite/locker/locker-info-list/{id}','LockerController@locker_info_list');
    Route::post('cf-lite/locker/locker-info-list/{id}','LockerController@store_lockers');

    Route::get('cf-lite/locker/locker-sale-list/{id}','LockerController@choose_locker_sale');
    Route::post('cf-lite/locker/locker-sale-list/{id}','LockerController@key_returned');

    Route::get('cf-lite/locker/locker-transfer', 'LockerController@locker_transfer_index');
    Route::post('cf-lite/locker/locker-transfer', 'LockerController@locker_transfer');
    Route::post('cf-lite/locker/member-transfer-ajax', 'LockerController@locker_transfer_ajax');
    //End of Locker Info

    //Locker Rent Management
    Route::get('cf-lite/locker/locker-rent-management','LockerController@locker_rent_list');
    Route::patch('cf-lite/locker/locker-rent-management','LockerController@update_memberlocker');
    Route::post('cf-lite/locker/memberlocker-list-ajax','AjaxController@get_memberlocker_detail');
    //End of Locker Rent Management
    //End of Locker

    //27a. Community Partner
    Route::get('cf-lite/community-partner/choose-cp-menu','CommunityPartnerController@choose_cp_menu');

    Route::get('cf-lite/community-partner/community-partner-list','CommunityPartnerController@cp_list');
    Route::get('cf-lite/community-partner/community-partner-list-filter','CommunityPartnerController@cp_list_filter');
    Route::patch('cf-lite/community-partner/community-partner-list','CommunityPartnerController@store_cp');

    Route::post('cf-lite/community-partner/cp-list-ajax', 'AjaxController@get_cp_detail');
    //End of Community Partner

    //27b. Community Partner Message
    Route::get('cf-lite/community-partner/community-partner-message-list','CommunityPartnerController@cpm_list');
    Route::patch('cf-lite/community-partner/community-partner-message-list','CommunityPartnerController@store_cpm');
    Route::delete('cf-lite/community-partner/community-partner-message-list/{id}','CommunityPartnerController@delete_cpm');

    Route::post('cf-lite/community-partner/cp-message-list-ajax', 'AjaxController@get_cp_message_detail');
    //End of Community Partner Message

	//28. Continue Membership
    Route::get('cf-lite/continue-membership', 'ContinueMembershipController@main_menu');
    Route::get('cf-lite/continue-membership/request', 'ContinueMembershipController@add_request');
    Route::get('cf-lite/continue-membership/{id}/membership', 'ContinueMembershipController@choose_membership');
    Route::get('cf-lite/payment/continue-membership/{membership_id}', 'ContinueMembershipController@payment_detail');
    Route::post('cf-lite/payment/continue-membership/{membership_id}', 'PaymentController@store_continue_payment');
    Route::get('cf-lite/continue-membership/activation', 'ContinueMembershipController@activation_list');
    Route::post('cf-lite/continue-membership/activation', 'ContinueMembershipController@activate_continue_membership');
    Route::get('cf-lite/continue-membership/cancel', 'ContinueMembershipController@pending_list');
    Route::get('cf-lite/continue-membership/cancel/{id}', 'ContinueMembershipController@cancel_membership_request_detail');
    Route::post('cf-lite/continue-membership/cancel/{id}', 'ContinueMembershipController@create_refund');
    //End of Continue Membership

    //29. Debt
//    Route::get('cf-lite/debt-list','PaymentController@debt_list');
//    Route::get('cf-lite/debt-list-filter','PaymentController@debt_list_filter');

    Route::get('cf-lite/debt-list','PaymentController@debt_list_v2');
    //End of Debt

    //30. Member Monitoring
    Route::get('cf-lite/member/member-monitoring-option','MemberMonitoringController@choose_mm_menu');

    // Workout Monitoring
    Route::get('cf-lite/member/workout-monitoring-list','MemberMonitoringController@workout_monitoring_list');
    Route::post('cf-lite/member/workout-monitoring-list','MemberMonitoringController@store_member_monitoring');
    Route::get('cf-lite/member/workout-monitoring-list-filter','MemberMonitoringController@workout_monitoring_list_filter');
    //End of Workout Monitoring

    //Workout Club Monitoring
    Route::get('cf-lite/member/workout-club-monitoring-list','MemberMonitoringController@workout_club_monitoring_list');
    //End of Workout Club Monitoring

    //Birthday Monitoring
    Route::get('cf-lite/member/birthday-monitoring-list','MemberMonitoringController@birthday_monitoring_list');
    Route::post('cf-lite/member/birthday-monitoring-list','MemberMonitoringController@store_member_monitoring_birthday');
    Route::get('cf-lite/member/birthday-monitoring-list-filter','MemberMonitoringController@birthday_monitoring_list_filter');

    //Lead Members Monitoring
    Route::get('cf-lite/member/monitoring-lead-member-list', 'MemberMonitoringController@monitoring_lead_member');
    Route::post('cf-lite/member/monitoring-lead-member', 'MemberMonitoringController@store_bulk_lead_member');

    //End of Birthday Monitoring
    //End of Member Monitoring

	//31. Membership Changing
    Route::get('cf-lite/membership-changing/request', 'MembershipChangingController@add_request');
    Route::get('cf-lite/membership-changing/{id}/membership', 'MembershipChangingController@choose_membership');
    Route::get('cf-lite/payment/membership-changing/{membership_id}', 'MembershipChangingController@payment_detail');
    Route::post('cf-lite/payment/membership-changing/{membership_id}', 'PaymentController@store_changing_payment');
    //End of Membership Changing

    //31a. Membership Changing Downgrade
    Route::get('cf-lite/membership-changing/{id}/membership/downgrade', 'MembershipChangingController@choose_membership_downgrade');
    Route::get('cf-lite/payment/membership-changing/{membership_id}/downgrade', 'MembershipChangingController@payment_detail_downgrade');
    Route::post('cf-lite/payment/membership-changing/{membership_id}/downgrade', 'PaymentController@store_changing_payment_downgrade');
    //End of Membership Changing Downgrade

    //32. Financial Management
    Route::get('cf-lite/financial-management', function () {
        return view('cf_lite.financial-management.financial-management-main');
    });

    Route::get('cf-lite/financial-management/ezypay-transactions', 'EzypayTransactionController@index')
        ->middleware('auth')->name('ezypay.transactions.index');
    Route::get('cf-lite/financial-management/ezypay-transactions/{transactionId}', 'EzypayTransactionController@show')
        ->middleware('auth')->whereUuid('transactionId')->name('ezypay.transactions.show');

    Route::get('cf-lite/financial-management/member-deposit', 'FinancialManagementController@member_deposit_main');
    Route::get('cf-lite/financial-management/member-deposit/{deposit_id}', 'FinancialManagementController@member_deposit_pay');
    Route::patch('cf-lite/financial-management/member-deposit/{deposit_id}', 'FinancialManagementController@process_member_deposit_payment');
    Route::get('cf-lite/financial-management/member/{member_id}', 'FinancialManagementController@member_deposit_detail');
    Route::post('cf-lite/financial-management/member/{member_id}', 'FinancialManagementController@store_member_deposit_correction');
    Route::get('cf-lite/financial-management/member-deposit/{deposit_id}/balance-correction', 'FinancialManagementController@balance_correction');
    Route::patch('cf-lite/financial-management/member-deposit/{deposit_id}/balance-correction', 'FinancialManagementController@store_balance_correction');

    Route::get('cf-lite/financial-management/daily-sales-report', 'FinancialManagementReportController@daily_sales');
    Route::get('cf-lite/financial-management/daily-sales-report-test', 'FinancialManagementReportController@daily_sales_test');
    Route::get('cf-lite/financial-management/daily-sales-report/export','FinancialManagementReportController@export_daily_sales');

    //PREP FOR ACCURATE
    Route::get('cf-lite/financial-management/monthly-sales-report/{month}/{year}/{action?}/{club?}', 'FinancialManagementReportController@monthly_sales');

    Route::get('cf-lite/financial-management/product-sales-report', 'ReportController@product_sales_report');

    Route::get('cf-lite/financial-management/daily-rewards-report', 'FinancialManagementReportController@daily_rewards');

    //Print Daily Sales Report added 25/03/2019
    Route::get('cf-lite/financial-management/daily-sales-report/print','FinancialManagementReportController@print_daily_sales');
    //End of Financial Management

    //33. Whatsapp
    Route::get('cf-lite/whatsapp/sendtest', 'WhatsappController@sendtest'); //TEST SEND
    Route::get('cf-lite/whatsapp', 'WhatsappController@whatsapp_main');
    Route::post('cf-lite/whatsapp', 'WhatsappController@send_message');

    Route::post('cf-lite/get-whatsapp-target', 'WhatsappController@whatsapp_ajax');

    Route::get('cf-lite/whatsapp/no-sale', 'WhatsappController@nosale_main');
    Route::post('cf-lite/whatsapp/no-sale', 'WhatsappController@send_nosale_blast');
    Route::post('cf-lite/get-whatsapp-club', 'WhatsappController@nosale_ajax');

    //34. Product Request
    Route::get('cf-lite/product-request/product-request-main','ProductController@product_request_main');
    Route::get('cf-lite/product-request','ProductController@product_request_club_list');
    Route::patch('cf-lite/product-request/{id}','ProductController@product_request_cancel');
    Route::get('cf-lite/product-request-detail/{id}','ProductController@product_request_detail');
    Route::get('cf-lite/product-request-item-list','ProductController@product_request_item_list');
    Route::post('cf-lite/product-request-item-list/{id}','ProductController@add_to_request_list');
    Route::get('cf-lite/product-request/cart', 'ProductController@request_list');
    Route::post('cf-lite/product-request/cart', 'ProductController@store_product_request');
    Route::patch('cf-lite/product-request/cart', 'ProductController@update_request_qty');
    Route::delete('cf-lite/product-request/cart', 'ProductController@remove_from_request');

    Route::get('cf-lite/product-request/requests','ProductController@product_request_all_club_list');
    Route::get('cf-lite/product-shipment-detail/{id}','ProductController@product_shipment_detail');
    Route::post('cf-lite/product-shipment-detail/{id}','ProductController@accept_shipment');

    Route::get('cf-lite/product-request/accepted-requests','ProductController@product_request_accepted_list');
    Route::patch('cf-lite/product-request/accepted-requests/{id}','ProductController@product_shipment_cancel');
    Route::get('cf-lite/product-shipment-accepted-detail/{id}','ProductController@product_request_send_list');
    Route::patch('cf-lite/product-shipment-accepted-detail/{id}','ProductController@update_bulk_product_shipment_detail');

    Route::get('cf-lite/product-shipment-arrival','ProductController@product_shipment_arrival_list');
    Route::get('cf-lite/product-shipment-arrival-detail/{id}','ProductController@product_shipment_arrival_detail_list');
    Route::patch('cf-lite/product-shipment-arrival-detail/{id}','ProductController@update_bulk_product_shipment_arrival_detail');

    // New Product Request
    Route::get('cf-lite/product-request/{club_id}/stock','ProductController@club_product_stock');
    Route::post('cf-lite/product-request/transfer', 'ProductController@transfer_product_stock');
    //End of Product Request

    //35. Certification
    Route::get('cf-lite/certification/certification-list','CertificationController@certification_list');
    Route::patch('cf-lite/certification/certification-list','CertificationController@store_certification');
    Route::delete('cf-lite/certification/certification-list/{id}','CertificationController@delete_certification');
    Route::post('cf-lite/certification/certification-list-ajax', 'AjaxController@get_certification_detail');

    Route::get('cf-lite/certification/staff-certification-detail/{id}','CertificationController@staff_certification');
    Route::patch('cf-lite/certification/staff-certification-detail/{id}','CertificationController@store_staff_certification');
    Route::delete('cf-lite/certification/staff-certification-detail/{id}','CertificationController@delete_staff_certification');
    //End of Certification

    //36. Other Setting
    Route::get('cf-lite/other-settings', 'SettingController@other_setting_main');
    Route::get('cf-lite/change-password-club-list','ClubController@change_password_club_list');
    Route::get('cf-lite/change-password-club-detail/{id}','ClubController@change_club_password');
    Route::post('cf-lite/change-password-club-detail/{id}','ClubController@store_club_password');
    Route::patch('cf-lite/change-password-club-detail/{id}','ClubController@update_club_password');

    //36a. Auto-Assign Member
    Route::get('cf-lite/auto-assign-member', 'SettingController@auto_assign_home');
    Route::post('cf-lite/auto-assign-member', 'SettingController@generate_auto_assign_member');
    Route::get('cf-lite/auto-assign-member/{staff_id}', 'SettingController@edit_auto_assign');
    Route::post('cf-lite/auto-assign-member/{staff_id}', 'SettingController@update_auto_assign');

    //36b. Coach Sequence Setting
    Route::get('cf-lite/coach-sequence', 'SettingController@coach_sequence_home');
    Route::post('cf-lite/coach-sequence', 'SettingController@update_coach_sequence');
    //End of Other Setting

    //37. Travel Pass
    Route::get('cf-lite/travel-pass','TravelPassController@travel_pass_main');
    Route::get('cf-lite/travel-pass-new-request','TravelPassController@travel_pass_new_request');
    Route::post('cf-lite/travel-pass-new-request','TravelPassController@store_new_travel_pass');
    Route::get('cf-lite/travel-pass-active-list','TravelPassController@travel_pass_active_list');
    Route::patch('cf-lite/travel-pass-active-list','TravelPassController@update_travel_pass');
    Route::delete('cf-lite/travel-pass-active-list/{id}','TravelPassController@delete_travel_pass');
    Route::post('cf-lite/travel-pass-detail-ajax', 'AjaxController@get_travel_pass_detail');
    Route::get('cf-lite/travel-pass-finished-list','TravelPassController@travel_pass_finished_list');
    //End of Travel Pass

    //38. Visiting Member
    Route::get('cf-lite/visiting-member','TravelPassController@visiting_member_list');
    Route::post('cf-lite/visiting-member-ajax','AjaxController@get_visiting_member_detail');
    Route::patch('cf-lite/visiting-member','TravelPassController@store_new_visiting_member');
    Route::get('cf-lite/visiting-member-filter','TravelPassController@visiting_member_filter');

    Route::get('cf-lite/visiting-member/{id}','TravelPassController@past_wo_vm');
    Route::post('cf-lite/visiting-member/{id}','TravelPassController@store_past_wo_vm');

    Route::get('cf-lite/visiting-member-inactive','TravelPassController@visiting_member_inactive_list');
    Route::get('cf-lite/visiting-member-inactive-filter','TravelPassController@visiting_member_inactive_filter');
    //End of Visiting Member

    //39. SMS
    Route::get('cf-lite/sms', 'SMSController@sms_main');
    Route::post('cf-lite/sms', 'SMSController@sms_message');

    Route::post('cf-lite/get-sms-target', 'SMSController@sms_ajax');

    //40. Language
    Route::get('lang/{lang}', ['as'=>'lang.switch', 'uses'=>'LanguageController@switchLang']);

    //41 Curves Learning
    Route::get('cf-lite/curves-learning/choose-menus', 'CurvesLearningController@choose_menus');

		//cl-categories
    Route::get('cf-lite/curves-learning/category', 'CurvesLearningController@list_category');
    Route::post('cf-lite/curves-learning/category', 'CurvesLearningController@store_category');
    Route::patch('cf-lite/curves-learning/category', 'CurvesLearningController@edit_category');
    Route::delete('cf-lite/curves-learning/category/{id}', 'CurvesLearningController@delete_category');
    Route::post('cf-lite/curves-learning/category/ajax', 'AjaxController@get_ajax_category');

		//cl-material-headers
	Route::get('cf-lite/curves-learning/material-header','CurvesLearningController@view_material_header');
    Route::post('cf-lite/curves-learning/material-header','CurvesLearningController@add_material_header');
    Route::patch('cf-lite/curves-learning/material-header','CurvesLearningController@edit_material_header');
    Route::delete('cf-lite/curves-learning/material-header/{id}','CurvesLearningController@delete_material_header');
    Route::post('cf-lite/curves-learning/material-header/ajax','AjaxController@get_ajax_material_header');

		//material-content
    Route::get('cf-lite/curves-learning/material-content/{id}','CurvesLearningController@view_material_content');
    Route::post('cf-lite/curves-learning/material-content/{id}','CurvesLearningController@add_material_content');
    Route::get('cf-lite/curves-learning/material-content-detail/{id}','CurvesLearningController@edit_material_content');
    Route::patch('cf-lite/curves-learning/material-content-detail/{id}','CurvesLearningController@edit_material_content_detail');
    Route::patch('cf-lite/curves-learning/material-question/{contentid}','CurvesLearningController@edit_material_content_question');
    Route::delete('cf-lite/curves-learning/material-question/{contentid}','CurvesLearningController@delete_material_content_question');
    Route::patch('cf-lite/curves-learning/material-file/{contentid}','CurvesLearningController@upload_material_content_file');
    Route::delete('cf-lite/curves-learning/material-file/{contentid}','CurvesLearningController@delete_material_content_file');
	Route::delete('cf-lite/curves-learning/material-content/{id}','CurvesLearningController@delete_material_content');

        //approval-content
    Route::get('cf-lite/curves-learning/approval', 'CurvesLearningController@list_approval');
    Route::patch('cf-lite/curves-learning/approval' ,'CurvesLearningController@update_bulk_content_approved');

	//42. Cashier
    Route::get('cf-lite/cashier/invoices', 'EzypayInvoiceController@index')->name('ezypay.invoices.index');
    Route::get('cf-lite/cashier/invoices/{member}', 'EzypayInvoiceController@show')->whereNumber('member')->name('ezypay.invoices.show');
    Route::get('cf-lite/cashier/membership', 'PaymentController@cashier_membership');
    Route::post('cf-lite/cashier/membership', 'PaymentController@store_cashier_membership');
    Route::post('cf-lite/cashier/membership/get-user/ajax','PaymentController@getUserAjax');
    Route::post('cf-lite/cashier/membership/ezypay-customer', 'PaymentController@saveEzypayCustomer');
    Route::get('cf-lite/cashier/membership/ezypay-payment-methods', 'PaymentController@getEzypayPaymentMethods');
    Route::post('cf-lite/cashier/membership/ezypay-payment-methods', 'PaymentController@storeEzypayPaymentMethod');
    Route::post('cf-lite/cashier/membership/ezypay-plan', 'PaymentController@ensureEzypayMembershipPlan');
    Route::get('cf-lite/cashier/membership/ezypay-installments/{purchase}', 'PaymentController@showEzypayInstallments')->name('ezypay.installments');
    Route::get('cf-lite/cashier/membership/ezypay-installments/{purchase}/payment-methods', 'PaymentController@getEzypayInstallmentMethods')->name('ezypay.installments.methods');
    Route::post('cf-lite/cashier/membership/ezypay-installments/{purchase}/payment-methods', 'PaymentController@storeEzypayInstallmentCard')->name('ezypay.installments.card');
    Route::post('cf-lite/cashier/membership/ezypay-installments/{purchase}', 'PaymentController@storeEzypayInstallments')->name('ezypay.installments.store');
    Route::get('cf-lite/cashier/membership/ezypay-checkout-options', 'PaymentController@getEzypayCheckoutOptions');
    Route::post('cf-lite/cashier/membership/get-clubs/ajax', 'PaymentController@getClubAjax');
    Route::get('cf-lite/cashier/membership-dummy', 'PaymentController@cashier_membership_dummy'); // Dummy
    Route::post('cf-lite/cashier/membership-dummy', 'PaymentController@store_cashier_membership_dummy'); // Dummy
    Route::get('cf-lite/cashier/locker','LockerController@new_locker_sales');
    Route::get('cf-lite/cashier/locker-test','LockerController@new_locker_sales_test');
    Route::post('cf-lite/cashier/locker','LockerController@store_locker_payment_cashier');
    Route::post('cf-lite/cashier/get-locker-list-ajax','LockerController@get_locker_list');
    Route::get('cf-lite/cashier/product', 'ProductController@product_cashier');
    Route::get('cf-lite/cashier/product-test', 'ProductController@product_cashier_test');
    Route::post('cf-lite/cashier/product', 'ProductController@store_product_cashier');
    Route::get('cf-lite/cashier/renewal','PaymentController@cashier_renewal');
    Route::post('cf-lite/cashier/renewal','PaymentController@store_renewal_cashier');
    Route::get('cf-lite/cashier/promo','PaymentController@cashier_promo');
    Route::post('cf-lite/cashier/promo','PaymentController@store_promo_cashier');
    Route::get('cf-lite/cashier/promo/convert','PaymentController@cashier_promo_convert');
        // Cashier updated February 5th, 2020
    Route::get('cf-lite/cashier/membership/{brl_id}/pay','PaymentController@cashier_membership_brl');

    //PDF Generation Test
    Route::get('cf-lite/pdfgen', 'PrintESCPOSController@generate_pdf'); // Dummy
    Route::get('cf-lite/downloadpdf/{filename}', 'PrintESCPOSController@download_pdf'); // Download generated pdf
    Route::get('cf-lite/sendpdf/{filename}/{email}/{receiptId}/{lead}/{module}', 'PrintESCPOSController@send_pdf'); // Send generated pdf

    //43. Printing
    Route::get('PrintESCPOSMembershipCashierDummy', 'PrintESCPOSController@cashierMembershipDummy'); // Dummy
    Route::get('home/printESCPOS', 'HomeController@printESCPOS');
    Route::get('PrintESCPOSProductCashier', 'PrintESCPOSController@cashierProduct');
    Route::get('PrintESCPOSMembershipCashier', 'PrintESCPOSController@cashierMembership');
    Route::get('PrintESCPOSMembershipCashierWithEzypay', 'PrintESCPOSController@cashierMembershipWithEzypay');
    Route::post('PrintESCPOSMembershipCashierWithEzypay', 'PrintESCPOSController@cashierMembershipWithEzypay');
    Route::get('PrintESCPOSRenewalCashier', 'PrintESCPOSController@cashierRenewal');
    Route::post('PrintESCPOSRenewalCashierWithEzypay', 'PrintESCPOSController@cashierRenewalWithEzypay')->middleware('auth')->name('ezypay.renewal.pay');
    Route::post('cf-lite/cashier/renewal/ezypay-options', 'PaymentController@renewalEzypayOptions')->middleware('auth')->name('ezypay.renewal.options');
    Route::get('cf-lite/cashier/renewal/ezypay-contact/{id}', 'PaymentController@renewalEzypayContact')->middleware('auth')->name('ezypay.renewal.contact');
    Route::get('PrintESCPOSLockerCashier', 'PrintESCPOSController@cashierLocker');
    Route::get('PrintESCPOSPromoCashier', 'PrintESCPOSController@promoMembership');

    Route::get('PrintESCPOSPromoConvertCashier', 'PrintESCPOSController@promoConvertMembership');
    Route::get('PrintESCPOSInstallmentCashier', 'PrintESCPOSController@cashierInstallment');
    Route::get('PrintESCPOSFirstPaymentCashier', 'PrintESCPOSController@firstPaymentInstallment');
    Route::get('PrintESCPOSGuestMembershipCashier', 'PrintESCPOSController@guestMembershipPayment');
    Route::get('PrintESCPOSReprintProduct', 'PrintESCPOSController@reprintProduct');
    Route::get('PrintESCPOSReprintLocker', 'PrintESCPOSController@reprintLocker');
    Route::any('WebClientPrintController', 'WebClientPrintController@processRequest');

    //44. Bank
    Route::get('cf-lite/bank', 'BankController@bank');
    Route::post('cf-lite/bank', 'BankController@store_new_bank');
    Route::delete('cf-lite/bank/{id}', 'BankController@delete_bank');
    Route::patch('cf-lite/bank', 'BankController@update_bank');
    Route::post('cf-lite/bank-ajax', 'BankController@edit_bank_ajax');

    //45. WMRank
    Route::get('cf-lite/wm-rank-gnrtr','WeighAndMeasureController@generate_wmrank_seeder');

    //46. Coach Lead Setting
    Route::get('cf-lite/coach-lead-setting', 'SettingController@coach_lead_home');
    Route::get('cf-lite/coach-lead-setting/{staff_id}', 'SettingController@edit_coach_lead');
    Route::post('cf-lite/coach-lead-setting/{staff_id}', 'SettingController@update_coach_lead');

    //46a. Coach Lead Sequence Setting
    Route::get('cf-lite/coach-lead-sequence', 'SettingController@coach_lead_sequence_home');
    Route::post('cf-lite/coach-lead-sequence', 'SettingController@update_coach_lead_sequence');

    //47. Publicity
    Route::get('cf-lite/publicity', 'PublicityController@index');
    Route::get('cf-lite/publicity/new-publicity', 'PublicityController@new_publicity');
    Route::post('cf-lite/publicity/new-publicity', 'PublicityController@store_publicity');
    Route::get('cf-lite/publicity/{id}', 'PublicityController@edit_publicity');
    Route::patch('cf-lite/publicity/{id}', 'PublicityController@update_publicity');
    Route::delete('cf-lite/publicity/{id}', 'PublicityController@delete_publicity');
    Route::post('cf-lite/ajax-publicity-status', 'PublicityController@ajax_status');

    //48. Mst Coach Target
    Route::get('cf-lite/coach-target', 'CoachTargetController@get_target_category');
    Route::get('cf-lite/coach-target/{id}/detail', 'CoachTargetController@target_category_detail');
    Route::patch('cf-lite/coach-target/{id}/detail', 'CoachTargetController@update_point_target');
    Route::post('cf-lite/coach-target/edit-point-target-ajax', 'CoachTargetController@edit_point_target_ajax');
    Route::post('cf-lite/coach-target/edit-status-target-category-ajax', 'CoachTargetController@edit_status_target_category_ajax');
    Route::post('cf-lite/coach-target/edit-status-target-category-detail-ajax', 'CoachTargetController@edit_status_target_category_detail_ajax');

    //49. Setting Coach Target Per Coach
    Route::get('cf-lite/coach-target/setting-coach-target/{id}', 'CoachTargetController@setting_coach_target');
    Route::post('cf-lite/coach-target/setting-coach-target/edit-status-ajax', 'CoachTargetController@edit_status_coach_target');

    Route::get('cf-lite/laporan/member-source-report', 'ReportController@member_source_report');

    //50. Curves HQ
    Route::get('cf-lite/hq/credit-index', 'HQController@credit_index');
    Route::get('cf-lite/hq/add-credit', 'HQController@create_credit');
    Route::post('cf-lite/hq/add-credit', 'HQController@store_credit');
    Route::get('cf-lite/hq/credit-detail/{id}', 'HQController@credit_detail');
    Route::post('cf-lite/hq/credit-detail/{id}/payment', 'HQController@store_credit_payment');
    Route::post('cf-lite/hq/credit-detail/payment', 'HQController@update_credit_payment');
    Route::patch('cf-lite/hq/credit-detail/{id}', 'HQController@update_credit');
    Route::get('cf-lite/hq/credit-payment-ajax', 'HQController@get_credit_payment');

    //51. Master Jubilant
    Route::get('cf-lite/master/jubilant', 'JubilantController@index');
    Route::post('cf-lite/master/jubilant', 'JubilantController@store_jubilant');
    Route::patch('cf-lite/master/jubilant', 'JubilantController@update_jubilant');
    Route::delete('cf-lite/master/jubilant/{id}', 'JubilantController@delete_jubilant');
    Route::post('cf-lite/master/jubilant/edit-jubilant-ajax', 'JubilantController@edit_jubilant_ajax');

    //52. Master COA Default
    Route::get('cf-lite/master/coa-default', 'MasterCoaDefaultController@index');
    Route::post('cf-lite/master/coa-default', 'MasterCoaDefaultController@store_coa_default');
    Route::patch('cf-lite/master/coa-default', 'MasterCoaDefaultController@update_coa_default');
    Route::post('cf-lite/master/coa-default/edit-coa-default-ajax', 'MasterCoaDefaultController@edit_ajax');

    //53. Consignment Order
    Route::get('cf-lite/consignment-order', 'ConsignmentOrderController@index');
    Route::get('cf-lite/consignment-order/create', 'ConsignmentOrderController@create_order');
    Route::post('cf-lite/consignment-order/create', 'ConsignmentOrderController@store_order');
    Route::get('cf-lite/consignment-order/detail/{id}', 'ConsignmentOrderController@order_detail');

    Route::get('cf-lite/consignment-order/detail/{id}/do', 'ConsignmentOrderController@delivery_order');
	Route::patch('cf-lite/consignment-order/detail/{id}', 'ConsignmentOrderController@update_consignment_order_detail');
    Route::post('cf-lite/delivery-order-ajax', 'ConsignmentOrderController@delivery_order_ajax');

    Route::get('cf-lite/consignment-order/bill', 'ConsignmentOrderController@bill_index');
    Route::get('cf-lite/consignment-order/bill/generate', 'ConsignmentOrderController@pre_generate_bill');
    Route::post('cf-lite/consignment-order/bill/generate', 'ConsignmentOrderController@generate_bill');
    Route::get('cf-lite/consignment-order/bill/detail/{id}', 'ConsignmentOrderController@bill_detail');
    Route::post('cf-lite/consignment-order/bill/detail/{id}', 'ConsignmentOrderController@store_bill_payment');
    Route::post('cf-lite/consignment-order/bill/detail', 'ConsignmentOrderController@update_bill_payment');
    Route::get('cf-lite/consignment-order/bill/product-payment-ajax', 'ConsignmentOrderController@product_payment_ajax');

    //54. Product Reception
    Route::get('cf-lite/product-consignment-acceptance', 'ProductController@product_consignment_acceptance');
    Route::get('cf-lite/product-consignment-acceptance/detail/{id}', 'ProductController@product_consignment_acceptance_detail');
    Route::post('cf-lite/product-consignment-acceptance/detail/{id}', 'ProductController@store_product_consignment_acceptance');

    //56. Staff Daily Report
    Route::get('cf-lite/report/staff-daily-report', 'StaffController@staff_daily_report');
    Route::post('cf-lite/report/staff-daily-report/import', 'StaffController@import_coach_report');

    //58. Digital Product
    Route::get('cf-lite/digital-product', 'DigitalProductController@digital_product_list');
    Route::get('cf-lite/digital-product-detail', 'DigitalProductController@digital_product_detail');
    Route::post('cf-lite/digital-product-store', 'DigitalProductController@store_digital_product');
    Route::patch('cf-lite/digital-product-update', 'DigitalProductController@update_digital_product');
    Route::delete('cf-lite/digital-product-delete', 'DigitalProductController@delete_digital_product');

    //Route Setting Accounting Account
    Route::get('cf-lite/setting/accounting-account', 'SettingController@setting_accounting_account');
    Route::get('cf-lite/setting/accounting-account/{id}/setting-list-jubilant', 'JubilantController@setting_acconting_account_jubilant');
    Route::post('cf-lite/setting/accounting-account/{id}/setting-list-jubilant', 'JubilantController@store_accounting_account_setting');

    Route::get('cf-lite/member-scan-image', 'ClubController@member_scan_image');
    Route::post('cf-lite/member-scan-image/{id}', 'ClubController@store_member_scan_image');

    // Product Digital
    Route::get('cf-lite/digital-product/inventory', 'DigitalProductController@digital_product_inventory_list');
    Route::post('cf-lite/digital-product/inventory', 'DigitalProductController@update_digital_product_inventory');
    Route::post('cf-lite/digital-product/get-detail-ajax', 'DigitalProductController@digital_product_inventory_detail');

    // DELETE Function
    Route::get('cf-lite/delete-function', 'DeleteController@get_view');
    Route::post('cf-lite/delete-function', 'DeleteController@delete');
    Route::post('cf-lite/delete-function/get-detail-ajax/{id}', 'DeleteController@get_detail_ajax');

});
// END OF CF LITE

// CF WM
Route::group(['middleware' => ['web']], function () {
    Route::get('cf-wm/login', 'WMLiteController@login');
    Route::post('cf-wm/login', 'WMLiteController@login_authentication');
    Route::post('cf-wm/logout', 'WMLiteController@logout');
    Route::get('cf-wm/', 'WMLiteController@wm_main');
    Route::get('cf-wm/wm-member-scan', 'WMLiteController@wm_member_scan');
    Route::post('cf-wm', 'WMLiteController@wm_main');
    Route::get('cf-wm/{id}/add', 'WMLiteController@add_wm');
    Route::post('cf-wm/{id}/add', 'WMLiteController@store_add_wm');
    Route::get('cf-wm/{wm_id}/printout', 'WMLiteController@wm_printout');
});

// END OF CF WM


// CF BACK
Route::group(['middleware' => ['web']], function () {
    Auth::routes();
    //1. STAFF
    Route::get('cf-back/staff', 'StaffController@index');
    Route::post('cf-back/staff', 'StaffController@store');
    Route::get('cf-back/staff/create', 'StaffController@create');
    Route::get('cf-back/staff/{id}/detail', 'StaffController@show');
    Route::delete('cf-back/staff/{id}', 'StaffController@destroy');

    //2. ROLE
    Route::get('cf-back/role', 'RolesController@index');
    Route::get('cf-back/role/create', 'RolesController@create');
    Route::post('cf-back/role', 'RolesController@store');
    Route::get('cf-back/role/{role}/edit', 'RolesController@edit');
    Route::patch('cf-back/role/{role}', 'RolesController@update');
    Route::delete('cf-back/role/{role}', 'RolesController@destroy');

    //3. CLUBS
    Route::get('cf-back/clubinfo', 'ClubController@index');
//    Route::get('cf-back/clubinfo/create', 'ClubController@create');
//    Route::post('cf-back/clubinfo', 'ClubController@store');
//    Route::get('cf-back/clubinfo/{clubinfo}/edit', 'ClubController@edit');
//    Route::patch('cf-back/clubinfo/{clubinfo}', 'ClubController@update');
    Route::delete('cf-back/clubinfo/{clubinfo}', 'ClubController@destroy');

    //4. CLUBS SERVICE FEE
    Route::get('cf-back/club/service-fee', 'ClubController@csf_index');
//    Route::get('cf-back/club/service-fee/create', 'ClubController@csf_create');
//    Route::post('cf-back/club/service-fee', 'ClubController@csf_store');
//    Route::get('cf-back/club/service-fee/edit/{edit}', 'ClubController@csf_edit');
//    Route::patch('cf-back/club/service-fee/edit/{edit}', 'ClubController@csf_update');
    Route::delete('cf-back/club/service-fee/edit/{edit}', 'ClubController@csf_destroy');

    //5. Lead
    Route::get('cf-back/lead', 'BuddyReferralLeadController@index');
    Route::get('cf-back/lead/date', 'BuddyReferralLeadController@index_filter');
    Route::get('cf-back/lead/date/{date}', 'BuddyReferralLeadController@guest_appointment_list');
    Route::get('cf-back/lead/date/{date}/{id}', 'BuddyReferralLeadController@leads_to_guest');
    Route::post('cf-back/lead/date/{date}/{id}', 'BuddyReferralLeadController@store_guest_detail');
//    Route::get('cf-back/leads/{leads}/create/first-assessment', 'BuddyReferralLeadController@create_fa');
//    Route::get('cf-back/leads/{leads}/create', 'BuddyReferralLeadController@create');
//    Route::post('cf-back/leads/{leads}', 'BuddyReferralLeadController@store');
//    Route::get('cf-back/leads/{leads}', 'BuddyReferralLeadController@show');
//    Route::post('cf-back/leads', 'BuddyReferralLeadController@store_lead_from_admin');

    //6. Member Attribute
    Route::get('cf-back/member-attribute', 'MemberController@index_attribute');
    Route::post('cf-back/member-attribute', 'MemberController@store_attribute');
    Route::patch('cf-back/member-attribute/{id}', 'MemberController@update_attribute');
    Route::delete('cf-back/member-attribute/{id}', 'MemberController@destroy_attribute');

    //7. LOCATION
    Route::get('cf-back/country', 'LocationController@indexCountry');
    Route::post('cf-back/country/createCountry', 'LocationController@storeCountry');
    Route::get('cf-back/country/{country}', 'LocationController@editCountry');
    Route::post('cf-back/country/updateCountry', 'LocationController@updateCountry');
    Route::delete('cf-back/country/{country}', 'LocationController@destroyCountry');

    Route::get('cf-back/province', 'LocationController@indexProvince');
    Route::post('cf-back/province/createProvince', 'LocationController@storeProvince');
    Route::get('cf-back/province/{province}', 'LocationController@editProvince');
    Route::post('cf-back/province/updateProvince', 'LocationController@updateProvince');
    Route::delete('cf-back/province/{province}', 'LocationController@destroyProvince');

    Route::get('cf-back/city', 'LocationController@indexCity');
    Route::post('cf-back/city/createCity', 'LocationController@storeCity');
    Route::get('cf-back/city/{city}', 'LocationController@editCity');
    Route::post('cf-back/city/updateCity', 'LocationController@updateCity');
    Route::delete('cf-back/city/{city}', 'LocationController@destroyCity');

    //8. TAX
    Route::get('cf-back/tax', 'TaxController@indexTax');
    Route::post('cf-back/tax/createTaxConfig', 'TaxController@storeTaxConfig');
    Route::post('cf-back/tax/createTaxType', 'TaxController@storeTax');
    Route::get('cf-back/tax/{tax}/edit', 'TaxController@editTax');
    Route::patch('cf-back/tax/{tax}/edit', 'TaxController@updateTax');
    Route::delete('cf-back/tax/{tax}', 'TaxController@destroyTax');

    // AJAX URL
    Route::post('getprovince', 'AjaxController@getprovince');
    Route::post('getcities', 'AjaxController@getcities');
    Route::post('getmemberorleads', 'AjaxController@getMemberOrLeads');
    Route::post('getmemberpersonal', 'AjaxController@getMemberPersonal');
    Route::post('getappointmentdata', 'AjaxController@getAppointmentData');
});
// END OF CF BACK

// CF Member View
Route::get('club-login', function () {
    return view('auth.club-login');
});
Route::post('club-login', 'ViewController@check_club_login');
Route::get('member-scan/{id}', 'ViewController@index');
Route::post('member-scan/{id}', 'ViewController@member_login');
Route::get('member-details/{member}', 'ViewController@show');
Route::get('member-details/create-fwo/{member}', 'ViewController@show_member_not_fwo');
Route::post('wm-notif-ajax', 'ViewController@wm_today_check');
//Route::get('wm-notif-ajax_test/{id}', 'ViewController@wm_today_check_test');
Route::get('due-payments', 'ViewController@due_payments');
// END CF Member View

//ACCURATE
Route::get('oauth-callback', 'ViewController@oauth');
Route::get('oauth-aol', 'ViewController@oauth_aol');
Route::get('opendb/{id}/{token}', 'ViewController@open_db');
Route::get('accurate/{club}/{keyword}/{itemType}', 'ViewController@accurate_item')->where('keyword', '.*');
Route::get('accurate-items/{club}/{itemType}/{page}/{max}', 'ViewController@accurate_items');
Route::get('accurate-membertype/{club}/{membertype}', 'ViewController@accurate_membership');

Route::group(['middleware' => ['web']], function () {
    //For Developer Only
    Route::get('dev/import-excel', 'ExcelDevController@main');
    Route::post('dev/import-excel', 'ExcelDevController@read_excel');
    Route::get('dev/import-staff-code', 'ExcelDevController@staff_code');
    Route::post('dev/import-staff-code', 'ExcelDevController@import_staff_code');
    Route::get('dev/generate-member-flag', 'MemberController@auto_generate_member_flag');

    //Additional Bonus
    Route::get('dev/additional-bonus', 'BonusDevController@main');
    Route::post('dev/additional-bonus', 'BonusDevController@execute');

    // Bonus Duration
    Route::get('dev/bonus-duration', 'BonusDevController@bonus_duration');
    Route::post('dev/bonus-duration', 'BonusDevController@store_bonus_duration');

    // Member report
    Route::get('dev/export-member-data', 'ExcelDevController@member_report_to_excel');

    // Delete User
    Route::get('dev/delete-user','DevController@delete_user_index');
    Route::post('dev/delete-user','DevController@get_user_delete_data');
    Route::delete('dev/delete-user','DevController@delete_user');

    // Delete Locker
    Route::get('dev/delete-locker','DevController@delete_locker_index');
    Route::post('dev/get-user-locker','DevController@get_user_locker');
    Route::post('dev/delete-locker','DevController@get_locker_delete_data');
    Route::delete('dev/delete-locker','DevController@delete_locker');

    // Delete Product
    Route::get('dev/delete-product', 'DevController@delete_product_index');
    Route::post('dev/get-user-product', 'DevController@get_user_product');
    Route::post('dev/delete-product', 'DevController@get_product_delete_data');
    Route::delete('dev/delete-product', 'DevController@delete_product');

    // Delete Member Payment
    Route::get('dev/delete-member-payment', 'DevController@delete_member_payment_index');
    Route::post('dev/get-member-payment-data', 'DevController@get_member_payment_data');
    Route::post('dev/get-member-payment-detail', 'DevController@get_member_payment_detail');
    Route::delete('dev/delete-member-payment', 'DevController@delete_member_payment');

    Route::get('dev/ble', function () {
        $members = \App\User::pluck('name', 'id');
        return view('developer.ble', compact('members'));
    });

//    Route::get('dev/ble-part', function () {
//        $members = \App\User::whereHas('user_memberstatus', function ($q) {
//            $q->where('status', '=', 'Active')
//                ->orWhere('status', '=', 'Hold');
//        })
//        ->orderBy('name', 'asc')
//        ->get();
//        return view('developer.ble-part', compact('members'));
//    });

    Route::get('dev/ble-part', 'BLEController@get_member');
});

//documentation
//Route::get('docs/main-page','DocsController');
Route::get('docs/traffic-control-report','DocsController@traffic_control_report');
Route::get('docs/fa-source-report','DocsController@fa_source_report');
Route::get('docs/membership-type-report','DocsController@membership_type_report');
Route::get('docs/birthday-report','DocsController@birthday_report');
Route::get('docs/member-status-report','DocsController@member_status_report');
Route::get('docs/shirt-size-report','DocsController@shirt_size_report');
Route::get('docs/product-report','DocsController@product_report');
Route::get('docs/staff-selling-report','DocsController@staff_selling_report');
Route::get('docs/fa-staff-report','DocsController@fa_staff_report');
Route::get('docs/wm-difference-report','DocsController@wm_difference_report');
Route::get('docs/top-losers-report','DocsController@top_losers_report');
Route::get('docs/wo-report','DocsController@wo_report');
Route::get('docs/challenge-report','DocsController@challenge_report');
Route::get('docs/reward-report','DocsController@reward_report');
Route::get('docs/member-cancellation-report','DocsController@member_cancellation_report');
Route::get('docs/appointment-report','DocsController@appointment_report');
Route::get('docs/member-hold-report','DocsController@member_hold_report');
Route::get('docs/absent-member-report','DocsController@absent_member_report');
Route::get('docs/not-wo-member-report','DocsController@not_wo_member_report');


Route::get('docs/club','DocsController@club');
Route::get('docs/staff','DocsController@staff');
Route::get('docs/product-management','DocsController@product_management');
Route::get('docs/voucher','DocsController@voucher');
Route::get('docs/tax-type','DocsController@tax_type');
Route::get('docs/privilege-access','DocsController@privilege_access');
Route::get('docs/functional-role','DocsController@functional_role');
Route::get('docs/attribute','DocsController@attribute');
Route::get('docs/standard-message','DocsController@standard_message');
Route::get('docs/challenge','DocsController@challenge');
Route::get('docs/mutate-staff','DocsController@mutate_staff');
Route::get('docs/global-activity','DocsController@global_activity');
Route::get('docs/global-message','DocsController@global_message');
Route::get('docs/membership-type','DocsController@membership_type');
Route::get('docs/locker-setting','DocsController@locker_setting');
Route::get('docs/company','DocsController@company');
Route::get('docs/certification','DocsController@certification');

Route::get('docs/sms','DocsController@sms');
Route::get('docs/wa','DocsController@wa');
Route::get('docs/message-for-member','DocsController@message_for_member');

Route::get('docs/login-manual','DocsController@login_manual');

Route::get('docs/travel-pass','DocsController@travel_pass');

Route::get('docs/staff-report','DocsController@staff_report');
Route::get('docs/club-assessment','DocsController@club_assessment');
Route::get('docs/projection-sheet','DocsController@projection_sheet');
Route::get('docs/invoice-product','DocsController@invoice_product');

Route::get('docs/product-request','DocsController@product_request');
Route::get('docs/community-partner','DocsController@community_partner');
Route::get('docs/locker-rental','DocsController@locker_rental');

Route::get('docs/workout-member-monitoring','DocsController@workout_member_monitoring');
Route::get('docs/birthday-member-monitoring','DocsController@birthday_member_monitoring');

Route::get('docs/product','DocsController@product');

Route::get('docs/financial-management','DocsController@financial_management');
Route::get('docs/refund','DocsController@refund');

Route::get('docs/member-hold','DocsController@member_hold');
Route::get('docs/other-setting','DocsController@other_setting');

Route::get('docs/create-new-member','DocsController@create_new_member');
Route::get('docs/weight-and-measurement','DocsController@weight_and_measurement');
Route::get('docs/payment','DocsController@payment');
Route::get('docs/change-club','DocsController@change_club');
Route::get('docs/extend-membership','DocsController@extend_membership');
Route::get('docs/membership-cancellation','DocsController@membership_cancellation');
Route::get('docs/login','DocsController@login');
Route::get('docs/password-recovery','DocsController@password_recovery');

Route::get('docs','DocsController@product');
Route::get('docs/main-page','DocsController@product');

//CF ACCOUNTING
Route::group(['middleware' => ['web']], function () {
    //User's Account
    Route::get('cf-acc', function () {
        return redirect('cf-acc/login');
    });

    Route::get('cf-acc/login', 'AccountingAccountController@login');
    Route::post('cf-acc/login', 'AccountingAccountController@acc_login_authentication');
    Route::patch('cf-acc/login', 'AccountingAccountController@registration_login_check');
    Route::get('cf-acc/registration', 'AccountingAccountController@registration');
    Route::post('cf-acc/registration', 'AccountingAccountController@store_registration');
    Route::post('cf-acc/logout', 'AccountingAccountController@acc_logout');
    Route::post('cf-acc/registration-logout', 'AccountingAccountController@registration_logout');

    //Main Accounting
    Route::get('cf-acc/dashboard', function () {
        return view('cf_acc.main.dashboard');
    });

	//COA
    Route::get('cf-acc/accounting/chart-of-account', 'ChartOfAccountController@chart_of_account');
    Route::post('cf-acc/accounting/chart-of-account', 'ChartOfAccountController@store_chart_of_account');
    Route::delete('cf-acc/accounting/chart-of-account/{id}', 'ChartOfAccountController@delete_chart_of_account');
    Route::patch('cf-acc/accounting/chart-of-account', 'ChartOfAccountController@update_chart_of_account');
    Route::post('cf-acc/accounting/chart-of-account-ajax', 'ChartOfAccountController@edit_chart_of_account_ajax');
    Route::get('cf-acc/accounting/chart-of-account-generate', 'ChartOfAccountController@generate_coa_index');
    Route::post('cf-acc/accounting/chart-of-account-generate', 'ChartOfAccountController@generate_coa');

    //Cost Revenue
    Route::get('cf-acc/accounting/cost-revenue', 'AccountingMasterController@cost_revenue');
    Route::post('cf-acc/accounting/cost-revenue', 'AccountingMasterController@store_cost_revenue');
    Route::delete('cf-acc/accounting/cost-revenue/{id}', 'AccountingMasterController@delete_cost_revenue');
    Route::patch('cf-acc/accounting/cost-revenue', 'AccountingMasterController@update_cost_revenue');
    Route::post('cf-acc/accounting/cost-revenue-ajax', 'AccountingMasterController@edit_cost_revenue_ajax');

	//Manual Transaction
    Route::get('cf-acc/transaction', 'AccountingManualTransactionController@manual_transactionV2');
    Route::post('cf-acc/transaction', 'AccountingManualTransactionController@store_manual_transactionV2');
    Route::post('cf-acc/via-transaction-ajax', 'AccountingManualTransactionController@via_transaction_ajax');

    //Ledger
    Route::get('cf-acc/ledger', 'AccountingJournalController@ledger_v2');
    Route::get('cf-acc/general-ledger','AccountingJournalController@general_ledger_v2');
    Route::get('cf-acc/memorial-journal','AccountingJournalController@memorial_journal_v2');
    Route::get('cf-acc/cash-flow', 'AccountingJournalController@cash_flow_v2');
    Route::get('cf-acc/bank-flow','AccountingJournalController@bank_flow_v2');
    Route::post('cf-acc/bank-flow-ajax', 'AccountingJournalController@bank_flow_get_bank_ajax');

    //Trial Balance
    Route::get('cf-acc/trial-balance', 'AccountingJournalController@trial_balance');

    //Income Statement
    Route::get('cf-acc/income-statement', 'AccountingJournalController@income_statement');

    //Purchasing Book
    Route::get('cf-acc/purchasing-book','PurchasingBookController@purchasing_bookV2');
    Route::post('cf-acc/purchasing-book','PurchasingBookController@store_purchasing_bookV2');
    Route::post('cf-acc/pay-purchasing-book','PurchasingBookController@pay_purchasing_bookV2');
    Route::get('cf-acc/purchasing-book/edit/{id}', 'PurchasingBookController@edit_purchasing_book');
    Route::post('cf-acc/purchasing-book/edit/{id}', 'PurchasingBookController@update_purchasing_book');
    Route::patch('cf-acc/purchasing-book/edit', 'PurchasingBookController@update_purchasing_book_payment');

    //Edit General Ledger Added August 28th 2019
    Route::patch('cf-acc/general-ledger','AccountingJournalController@edit_gl');
    Route::post('cf-acc/general-ledger-edit-ajax','AccountingJournalController@edit_gl_ajax');

    //Cost Revenue Center Added 29082019
    Route::get('cf-acc/accounting/cost-revenue-center', 'CostRevenueCenterController@index');
    Route::post('cf-acc/accounting/cost-revenue-center', 'CostRevenueCenterController@update_cost_revenue');
    Route::post('cf-acc/accounting/cost-revenue-center/ajax', 'CostRevenueCenterController@get_cost_revenue');

    //Beginning Balance
    Route::get('cf-acc/accounting/beginning-balance', 'AccountingManualTransactionController@beginning_balance');
    Route::post('cf-acc/accounting/beginning-balance', 'AccountingManualTransactionController@store_beginning_balance');
    Route::post('cf-acc/accounting/import-beginning-balance', 'AccountingManualTransactionController@import_beginning_balance');

    //LAPORAN KEUANGAN
    //Cashflow Statement
    Route::get('cf-acc/financial-statement/cashflow-statement', 'AccountingJournalController@cashflow_statement');
    //Balance
    Route::get('cf-acc/financial-statement/balance', 'AccountingJournalController@balance');
    //Income Statement
    Route::get('cf-acc/financial-statement/income-statement', 'AccountingJournalController@income_statement_v2');

    // Temporary Journal
    Route::get('cf-acc/temp-journal', 'AccountingTemporaryJournalController@index');
    Route::post('cf-acc/temp-journal/store', 'AccountingTemporaryJournalController@store_temp_journal');

    Route::get('cf-acc/amortization-report', 'AccountingAmortizationController@amortization_report');

    Route::get('cf-acc/generate-amortization', 'AccountingAmortizationController@generate_amortization_page');
    Route::post('cf-acc/generate-amortization', 'AccountingAmortizationController@generate_amortization');
});

//CF GUEST MEMBERSHIP
Route::group(['middleware' => ['web']], function (){
    Route::get('cf-lite/guest-membership', 'GuestMembershipController@guest_membership');
    Route::post('cf-lite/guest-membership', 'GuestMembershipController@store_guest_membership');
    Route::delete('cf-lite/guest-membership/{id}', 'GuestMembershipController@delete_guest_membership');
    Route::patch('cf-lite/guest-membership', 'GuestMembershipController@update_guest_membership');
    Route::post('cf-lite/guest-membership-ajax', 'GuestMembershipController@edit_guest_membership_ajax');

    Route::get('cf-lite/guest-member/list', 'GuestMembershipController@guest_member_list');
    Route::patch('cf-lite/guest-member/list', 'GuestMembershipController@update_guest_member_list');

    Route::get('cf-lite/guest-member/wo-search','GuestMembershipController@guest_member_search');
    Route::get('cf-lite/guest-member/{id}/past-wo','GuestMembershipController@past_gm_wo');
    Route::post('cf-lite/guest-member/{id}/past-wo','GuestMembershipController@store_guest_member_workout');

	Route::get('cf-wm/guest-membership/', 'WMLiteController@new_guest_membership');
    Route::post('cf-wm/guest-membership/{id}', 'WMLiteController@store_guest_add_wm');
    Route::get('cf-wm/guest-membership/{wm_id}/printout', 'WMLiteController@wm_guest_printout');

    //Guest Weight and Measure
    Route::get('cf-lite/guest-member/guest-weight-measure', 'GuestMembershipController@wm_guest_list');
    Route::get('cf-lite/guest-member/guest-weight-measure/{id}/history', 'GuestMembershipController@wm_guest_history');
    Route::get('cf-lite/guest-member/guest-weight-measure/{id}/detail', 'GuestMembershipController@wm_guest_detail');
    Route::patch('cf-lite/guest-member/guest-weight-measure/{id}/detail', 'GuestMembershipController@update_wm_guest_data');
    Route::delete('cf-lite/guest-member/guest-weight-measure/{id}/history','GuestMembershipController@delete_gwm');
    Route::get('cf-lite/guest-member/guest-weight-measure/{id}/printout', 'GuestMembershipController@wm_guest_printout');
    Route::get('cf-lite/guest-membership/receipt/{id}', 'GuestMembershipController@receipt');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
