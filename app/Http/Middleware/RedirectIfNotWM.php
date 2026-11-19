<?php

namespace App\Http\Middleware;

use Closure;
use Session;

class RedirectIfNotWM
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
        if (Session::has('wm-only')):
            return redirect('cf-wm');
        endif;

        return $next($request);
    }
}
