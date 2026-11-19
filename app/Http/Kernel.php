<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\Language::class,
            \App\Http\Middleware\RoleCheck::class,
            \App\Http\Middleware\EnsureMobileCurvesEnabled::class,
            \App\Http\Middleware\RequireEzypayInstallments::class,
        ],

        'api' => [
            'throttle:60,1',
            'bindings',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'coach_assign'=>\App\Http\Middleware\RedirectifNotAllowedCoachAssign::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'master-product' => \App\Http\Middleware\RedirectIfNotAllowedMasterProduct::class,
        'staff' => \App\Http\Middleware\RedirectIfNotAllowedStaff::class,
        'tax-type' => \App\Http\Middleware\RedirectIfNotAllowedTaxType::class,
        'cf-wm' => \App\Http\Middleware\RedirectIfAuthenticatedWM::class,
        'club-password' => \App\Http\Middleware\RedirectIfNotAllowedClubPassword::class,
        'wm-only' => \App\Http\Middleware\RedirectIfNotWM::class,
        'member-status-setting' => \App\Http\Middleware\RedirectForMemberStatusSetting::class,
        'staff-management' => \App\Http\Middleware\RedirectIfNotAllowedStaffManagement::class,
        'staff-password' => \App\Http\Middleware\RedirectIfNotAllowedStaffPassword::class,
        'developer-only' => \App\Http\Middleware\RedirectIfNotDeveloper::class,
        'bank' => \App\Http\Middleware\RedirectIfNotAllowedBank::class,
        'acc-auth' => \App\Http\Middleware\RedirectIfAuthenticatedAccounting::class,
        'hq' => \App\Http\Middleware\RedirectIfNotHQ::class,
        'jubilant' => \App\Http\Middleware\RedirectIfNotAllowedJubilant::class,
        'log-delete' => \App\Http\Middleware\RedirectIfNotAllowedDelete::class,
    ];
}
