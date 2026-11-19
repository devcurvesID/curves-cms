<?php

namespace App\Providers;

use App\Model\Club\ClubInfo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class ManagerClubServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('cf_lite.includes.left-sidebar', function ($view) {
            //dd(Auth::user()->role_id);
            if ((Auth::user()->role_id <= 4) || (Auth::user()->role_id == 8)):
                //$view->with('club_access', ClubInfo::orderBy('club_name', 'asc')->pluck('club_name', 'id'));
                $view->with('club_access', ClubInfo::where('status', 'Active')->orderBy('club_name', 'asc')->pluck('club_name', 'id'));
            elseif (Auth::user()->role_id == 10):
                $accountingClubs = ['1', '2', '3', '4', '6'];
                //$view->with('club_access', ClubInfo::whereIn('id', $accountingClubs)->orderBy('club_name', 'asc')->pluck('club_name', 'id'));
                $view->with('club_access', ClubInfo::where('status', 'Active')->whereIn('id', $accountingClubs)->orderBy('club_name', 'asc')->pluck('club_name', 'id'));
                //$view->with('club_access', Auth::user()->manager_club);
            endif;
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
