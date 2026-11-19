<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotAllowedDelete
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
        if(Auth::user()->role_id != 1 && Auth::user()->role_id != 4 && Auth::user()->role_id != 8){
            return redirect('cf-lite/dashboard');
        }
        return $next($request);
    }
}
