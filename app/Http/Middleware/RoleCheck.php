<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Session;

class RoleCheck
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
        if (!empty(Auth::user())):
            if (Auth::user()->role_id == '7'):
                Auth::logout();

                Session::flash('flash_message', trans('messages.data_not_found'));
                return redirect('login');
            endif;
        endif;

        return $next($request);
    }
}
