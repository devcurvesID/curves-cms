<?php

namespace App\Providers;

use App\Model\Staff\StaffAttendance;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class StaffAttendanceServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('cf_lite.layouts.hq-dashboard-template', function ($view) {
            $view->with('staff_attendance_list', StaffAttendance::whereDate('sign_in', '=', Carbon::today()->toDateString())->orderBy('sign_in', 'asc')->get());
        });
        view()->composer('cf_lite.layouts.staff-dashboard-template', function ($view) {
            $view->with('staff_attendance_list', StaffAttendance::whereDate('sign_in', '=', Carbon::today()->toDateString())->orderBy('sign_in', 'asc')->get());
        });

        view()->composer('cf_lite.layouts.staff-template', function ($view) {
            $view->with('staff_attendance_list', StaffAttendance::whereDate('sign_in', '=', Carbon::today()->toDateString())->orderBy('sign_in', 'asc')->get());
        });

        view()->composer('cf_wm.layout.wm-template', function ($view) {
            $view->with('staff_attendance_list', StaffAttendance::whereDate('sign_in', '=', Carbon::today()->toDateString())->orderBy('sign_in', 'asc')->get());
        });
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
