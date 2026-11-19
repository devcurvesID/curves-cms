<?php

namespace App\Providers;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::tokensCan([
            \App\Http\Middleware\AuthorizeEzypayWebhook::SCOPE => 'Forward EZYPAY payment webhooks from mobile-curves',
        ]);

        view()->composer('*', function($view)
        {
            if (Auth::check()) {

                $wib_zone = ['1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','26','27','28'];

                $wita_zone = ['25'];
                $wit_zone = [];

                if(in_array(Auth::user()->club_id, $wib_zone)){
                    config(['app.timezone' => 'Asia/Jakarta']);
                }else if(in_array(Auth::user()->club_id, $wita_zone)){
                    config(['app.timezone' => 'Asia/Makassar']);
                }else if(!empty($wit_zone) && in_array(Auth::user()->club_id, $wit_zone)){
                    config(['app.timezone' => 'Asia/Jayapura']);
                }
                //dd(config('app.timezone'));
                //dd( Carbon::now());
            }
        });

       //Passport::routes();
    }
}
