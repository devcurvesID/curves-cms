<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotAllowedJubilant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::user()->role_id == '4' || Auth::user()->role_id == '5' || Auth::user()->role_id == '6') {
            return redirect('cf-lite/dashboard');
        }

        if (Auth::user()->user_club->club_jubilant->type == 'Franchise') {
            return redirect('cf-lite/dashboard');
        }

        return $next($request);
    }
}
