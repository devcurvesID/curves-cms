<?php

namespace App\Providers;

use App\Model\Workout\Workout;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class MemberAttendanceServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('cf_lite.includes.left-sidebar', function ($view) {
            $view->with('member_attendance_list', Workout::select('user_id')
                ->where('club_id', '=', Auth::user()->club_id)
                ->whereDate('workout_date', '=', Carbon::today()->toDateString())
                ->whereHas('workout_user', function ($q) {
                    $q->where('club_id', '=', Auth::user()->club_id)
                        //Added 03042018
                        ->where('role_id', '=', '7');
                })
                ->with('workout_user.user_appointment', function ($q) {
                    $toDate = Carbon::now()->addDays(4)->toDateString();
                    $yesterdays = Carbon::now()->subDays(4)->toDateString();
                    $q->whereBetween('app_date', [$yesterdays, $toDate]);
                    //$q->first();
                })
                ->whereHas('pending_payment')
                ->orderBy('workout_date', 'desc')
                ->get());
        });

        //Added 08082018
        view()->composer('cf_lite.includes.left-sidebar', function ($view) {
            $view->with('other_attendance_list', Workout::select('user_id')
                ->where('club_id', '=', Auth::user()->club_id)
                ->whereDate('workout_date', '=', Carbon::today()->toDateString())
                ->whereHas('workout_user', function ($q) {
                    $q->where('club_id', '<>', Auth::user()->club_id)
                        ->where('role_id', '=', '7');
                })
                ->orderBy('workout_date', 'desc')
                ->get());
        });

        view()->composer('cf_lite.includes.left-sidebar', function ($view) {
            $view->with('latest_member_attendance', Workout::whereDate('workout_date', '=', Carbon::today()->toDateString())
                ->where('club_id', '=', Auth::user()->club_id)
                ->orderBy('workout_date', 'desc')
                ->first());
        });

        view()->composer('cf_wm.layout.left-sidebar', function ($view) {
            $view->with('member_attendance_list', Workout::select('user_id')->whereDate('workout_date', '=', Carbon::today()->toDateString())->orderBy('workout_date', 'desc')->get());
        });

        //Total Member
        view()->composer('cf_lite.includes.left-sidebar', function ($view) {
            $view->with('total_member', User::where('club_id', '=', Auth::user()->club_id)
//                ->where('role_id', '<>', '1')
                ->where('role_id', '=', '7')
                ->whereHas('user_memberstatus', function($q) {
//                    $q->where('status', '<>', 'Inactive')
//                        ->where('status', '<>', 'Stopped');
                    $q->where('status', '=', 'Active')
                        ->orWhere('status', '=', 'Hold');
                })->get()->count());
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
