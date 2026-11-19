<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

class RedirectIfAuthenticatedAccounting
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
        if (Session::get('acc') && Session::get('account')):
            return $next($request);
        else:
            return redirect('cf-acc/login');
        endif;
    }
}
