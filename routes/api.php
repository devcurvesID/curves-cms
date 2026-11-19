<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', 'API\LoginController@login');

Route::post('webhooks/ezypay', 'API\WebhookEzypayController@update_member_payment')
    ->withoutMiddleware('api');

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('user-detail', 'API\UserController@user_detail');

    //Workout
    Route::post('workout', 'API\UserController@workout');
    Route::post('workout/detail/{date}', 'API\UserController@workout_detail');

    //WM
    Route::post('wm', 'API\UserController@wm');
    Route::post('wm/detail/{id}', 'API\UserController@wm_detail');

    //User's Profile
    Route::post('profile', 'API\UserController@profile');
    Route::post('qrcode', 'API\UserController@qrcode');
    Route::post('update-phone', 'API\UserController@updatePhone');
    Route::post('photo', 'API\UserController@updatePhoto');

    //Message
    Route::post('member-message', 'API\UserController@message');

    //Payment
    Route::post('debt', 'API\UserController@debt');
    Route::post('payment', 'API\UserController@payment');
});

//Testing Flutter API
// Route::get('member-data', 'API\FlutterController@member_data');
// Route::post('store-new-member', 'API\FlutterController@store_member');

//Coach Application
Route::group(['middleware' => 'auth:api'], function () {
    Route::get('coach-profile', 'API\CoachAppController@coach_profile');
    Route::get('member-data', 'API\CoachAppController@member_data');
    Route::get('member-detail/{id}', 'API\CoachAppController@member_detail');
    Route::get('member-workout/{id}','API\CoachAppController@member_wo_main');
    Route::get('member-workout-chart/{id}','API\CoachAppController@member_wo_chart');
    Route::get('member-workout/{id}/detail','API\CoachAppController@member_wo_detail');
    Route::get('member-detail/{id}/wm', 'API\CoachAppController@member_wm_list');
    Route::get('wm-detail/{wm_id}', 'API\CoachAppController@wm_detail');
    Route::get('wm-detail/{wm_id}/{title}', 'API\CoachAppController@wm_detail_chart');
    Route::get('keytag','API\CoachAppController@keytag');
    Route::get('thirty-master/{id}/profile','API\CoachAppController@member_thirty_profile_main');
    Route::post('thirty-master/{id}/profile','API\CoachAppController@member_add_thirty');
    Route::get('mini-member-data', 'API\CoachAppController@mini_member_data');
    Route::post('check', 'API\CoachAppController@check');
    Route::get('coach-performance', 'API\CoachAppController@coach_performance');
    Route::get('coach-performance/{category}/{time}', 'API\CoachAppController@coach_performance_detail');
    Route::get('guest-list','API\CoachAppController@guest_list');
    Route::post('missing-in-action', 'API\CoachAppController@missing_in_action');
    Route::post('missing-in-action/{id}', 'API\CoachAppController@store_missing_in_action');
    Route::get('missing-in-action/{id}', 'API\CoachAppController@missing_in_action_history');
    Route::get('top-weight-losers','API\CoachAppController@get_top_weight_losers_v4');
    Route::get('top-weight-losers-overall','API\CoachAppController@get_top_weight_losers_overall_v2');
    Route::get('top-weight-losers-by-club','API\CoachAppController@get_top_weight_losers_by_club_v2');
    Route::get('top-weight-losers-by-club-overall','API\CoachAppController@get_top_weight_losers_by_club_overall_v2');
    Route::get('top-measurement-losers','API\CoachAppController@get_top_measurement_losers_v4');
    Route::get('top-measurement-losers-overall','API\CoachAppController@get_top_measurement_losers_overall_v2');
    Route::get('top-measurement-losers-by-club','API\CoachAppController@get_top_measurement_losers_by_club_v2');
    Route::get('top-measurement-losers-by-club-overall','API\CoachAppController@get_top_measurement_losers_by_club_overall_v2');
    Route::get('top-body-fat-losers','API\CoachAppController@get_top_body_fat_losers_v4');
    Route::get('top-body-fat-losers-overall','API\CoachAppController@get_top_body_fat_losers_overall_v2');
    Route::get('top-body-fat-losers-by-club','API\CoachAppController@get_top_body_fat_losers_by_club_v2');
    Route::get('top-body-fat-losers-by-club-overall','API\CoachAppController@get_top_body_fat_losers_by_club_overall_v2');
    Route::get('club-today-event', 'API\CoachAppController@club_today_event');
    Route::get('club-today-reconfirmation', 'API\CoachAppController@club_today_reconfirmation');
    Route::get('club-today-appointment', 'API\CoachAppController@club_today_appointment');
    Route::post('brl-list','API\CoachAppController@buddy_referral_lead');
    Route::post('store-new-appo','API\CoachAppController@store_new_appo');
    Route::post('set-appo-status','API\CoachAppController@set_appo_status');
    Route::get('workout-appointment/{member_id}', 'API\CoachAppController@workout_appointment');
    Route::post('workout-appointment/{member_id}', 'API\CoachAppController@update_workout_appointment');
    Route::get('coach-rank', 'API\CoachAppController@coach_rank');
    Route::post('add-new-wm/{member_id}', 'API\CoachAppController@store_new_wm');
    Route::post('call-log-apo/{apo_id}', 'API\CoachAppController@store_call_log_apo');
    Route::post('call-log-new-lead/{guest_id}', 'API\CoachAppController@store_call_log_new_lead');
    Route::get('source-list', 'API\CoachAppController@get_source_list');
    Route::post('add-new-lead', 'API\CoachAppController@add_new_lead');
    Route::post('guest-monitoring/{guest_id}', 'API\CoachAppController@guest_monitoring');

    Route::get('cl-category-list','API\CoachAppController@cl_category_list_index');
    Route::post('cl-header-list','API\CoachAppController@cl_category_list_main_index');
    Route::post('cl-content','API\CoachAppController@cl_material_content_index');
    Route::post('cl-support','API\CoachAppController@cl_material_support_index');

    Route::get('best-coach-club','API\CoachAppController@best_coach_club_v3');
    Route::get('best-coach-overall','API\CoachAppController@best_coach_overall_v2');
    Route::get('best-coach-performance/{id}', 'API\CoachAppController@best_coach_performance_v2');
    Route::get('best-coach-performance-detail/{id}', 'API\CoachAppController@best_coach_performance_detail_v2');

    Route::post('time-availability','API\CoachAppController@buddy_referral_time_availability_v2');

    Route::get('x-factor/{member_id}', 'API\CoachAppController@xfactor');
    Route::post('x-factor/{member_id}', 'API\CoachAppController@update_xfactor');
    Route::post('member-payment/{member_id}','API\CoachAppController@get_member_payment');

    Route::get('ble-get-member', 'API\CoachAppController@ble_get_member');

    Route::get('staff-point-acquisition/{staff_id}','API\CoachAppController@staff_point_acquisition');

    Route::get('get-gm-call-histories/{id}','API\CoachAppController@get_call_histories');

    Route::post('update-gm','API\CoachAppController@update_guest_name_and_phone');

    Route::get('challenge-report/{id}', 'API\CoachAppController@get_challenge_report');
    Route::post('challenge-report', 'API\CoachAppController@store_or_update_challenge_report');

    Route::get('my-target/{id}', 'API\CoachAppController@my_target');


    // Daily Report Staff
    Route::get('staff-daily-report', 'API\CoachAppController@staff_daily_report');

    Route::get('staff-daily-report-data', 'API\CoachAppController@get_data_staff_daily_report');
    Route::get('staff-daily-report-data-sales-detail', 'API\CoachAppController@get_data_staff_daily_report_sales_detail');
});

//Member Application
Route::group(['middleware' => 'auth:api'], function () {
    Route::get('member-profile','API\MemberAppController@member_profile');
    Route::get('member-detail-member','API\MemberAppController@member_detail');
    Route::get('member-workout-member','API\MemberAppController@member_wo_main');
    Route::get('member-detail-member/wm','API\MemberAppController@member_wm_list');
    Route::get('club-now', 'API\MemberAppController@club_now');
    Route::get('show-reward', 'API\MemberAppController@show_reward');
    Route::post('get-member-birthday','API\MemberAppController@get_member_birthday');

	Route::get('survey-list', 'API\MemberAppController@survey_list');
    Route::get('get-survey', 'API\MemberAppController@get_survey');
    Route::post('store-survey/{workout_id}', 'API\MemberAppController@store_survey');
    Route::post('skip-survey/{workout_id}', 'API\MemberAppController@skip_survey');
    Route::get('get-survey-counter','API\MemberAppController@get_survey_counter');

    Route::post('add-daily-hydration','API\MemberAppController@add_daily_hydration');
    Route::post('update-hydration-value','API\MemberAppController@update_hydration_value');
    Route::get('get-current-hydration','API\MemberAppController@get_current_hydration');

    Route::get('wo-detail', 'API\MemberAppController@get_wo_weekly');
});

Route::get('registration-page', 'API\MemberAppController@registration_permission');
Route::post('register/{id}', 'API\MemberAppController@register');

Route::get('get-publicities','API\MemberAppController@get_publicities');
Route::get('get-publicities/{id}','API\MemberAppController@get_publicity');
Route::get('get-club-list','API\MemberAppController@get_club_list');
Route::post('register-new-lead','API\MemberAppController@register_new_lead');
Route::get('get-member-by-name', 'API\MemberAppController@get_member_by_name');


//PORTAL ACCESS
Route::get('staff-daily-sales/{date}/{clubID}/{uid}','API\FinancialReportPortalController@daily_sales');


Route::post('user', 'API\UserController@getUsers');
Route::get('club-member/{club_id}/{offset}/{limit}', 'API\UserController@member_club');
Route::get('club', 'API\UserController@data_clubs');
Route::get('role', 'API\UserController@data_roles');
Route::get('user-member', 'API\UserController@data_usermember');


Route::get('weigh-measure', 'API\SyncDataToMongoDBController@data_weighmeasuresbyuserid');
Route::get('user-workout', 'API\SyncDataToMongoDBController@data_workoutbyuserid');
Route::get('membership-type', 'API\SyncDataToMongoDBController@data_memberships');
Route::get('member-status', 'API\SyncDataToMongoDBController@data_memberstatus');

Route::get('member-payment', 'API\SyncDataToMongoDBController@data_memberpayment');
Route::get('bank-club', 'API\SyncDataToMongoDBController@data_bankclub');
Route::get('payment-method', 'API\SyncDataToMongoDBController@data_paymentmethod');
Route::get('user-nonmember', 'API\SyncDataToMongoDBController@data_usernonmember');
Route::get('user-mobilecurves', 'API\SyncDataToMongoDBController@data_usermobilecurves');
Route::get('challenge', 'API\SyncDataToMongoDBController@data_challenges');
Route::get('publicity', 'API\SyncDataToMongoDBController@data_publicities');
Route::get('coach-member', 'API\SyncDataToMongoDBController@data_coachmember');
Route::post('store-weigh-measure', 'API\SyncDataToMongoDBController@store_weigh_measure');

Route::get('community-partners', 'API\SyncDataToMongoDBController@data_communitypartners');
Route::get('member-hold', 'API\SyncDataToMongoDBController@data_memberhold');
Route::get('nonmemberstatus/{club_id}', 'API\SyncDataToMongoDBController@data_listnonmemberstatus');
Route::get('memberhold-by-clubid/{club_id}', 'API\SyncDataToMongoDBController@data_listmemberholdbyclubid');
Route::get('member-hasnt-workout/{club_id}', 'API\SyncDataToMongoDBController@data_memberhasntworkoutyet');
Route::get('staff-point-acquisitions/{user_id}', 'API\SyncDataToMongoDBController@data_lspa');
Route::get('buddy-referral/{staff_id}/{created_at}', 'API\SyncDataToMongoDBController@data_buddyReferralLead');
