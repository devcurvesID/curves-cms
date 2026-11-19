<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotHQ
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
        if(Auth::user()->role_id > 4 && Auth::user()->role_id != 8){
            return redirect('cf-lite/dashboard');
        }

        if (!Auth::user()->user_club->hq):
            return redirect('cf-lite/dashboard');
        endif;

        return $next($request);
    }
}
