<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotAllowedStaff
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
        $open = 0;
        foreach (Auth::user()->user_accessmenu as $access):
            if ($access->id == 2):
                $open = 1;
            endif;
        endforeach;

        if ($open == 0):
            return redirect('cf-lite/staff/'.Auth::user()->club_id);
        endif;

        return $next($request);
    }
}
