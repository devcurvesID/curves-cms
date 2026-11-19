<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Session;

class RedirectIfNotAllowedStaffPassword
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
        if ((Auth::user()->role_id <= 4) || (Auth::user()->role_id == 8) || (Auth::id() == $request->attribute)):
            $open = 1;
        endif;

        if ($open == 0):
            Session::flash('flash_message', trans('messages.you_dont_have_access'));
            return redirect('cf-lite/staff/'.Auth::id().'/detail');
        endif;

        return $next($request);
    }
}
