<?php

namespace App\Providers;

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class StaffSessionServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('cf_lite.includes.left-sidebar', function ($view) {
            $view->with('our_staffs', User::where('club_id', '=', Auth::user()->club_id)
            ->where('role_id', '>=', Auth::user()->role_id)
            ->where('role_id', '<>', '7')
            ->orderBy('name', 'asc')
            ->pluck('name', 'id'));
        });

        view()->composer('cf_wm.layout.left-sidebar', function ($view) {
            $view->with('our_staffs', User::where('club_id', '=', Auth::user()->club_id)
                ->where('role_id', '>=', Auth::user()->role_id)
                ->where('role_id', '<>', '7')
                ->orderBy('name', 'asc')
                ->pluck('name', 'id'));
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
