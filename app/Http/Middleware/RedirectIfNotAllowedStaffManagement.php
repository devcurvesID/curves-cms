<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Session;

class RedirectIfNotAllowedStaffManagement
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
            if ($access->id == 7):
                $open = 1;
            endif;
        endforeach;

        if ($open == 0):
            Session::flash('flash_message', trans('messages.you_dont_have_access'));
            return redirect('cf-lite/staff/'.Auth::id().'/detail');
        endif;

        return $next($request);
    }
}
