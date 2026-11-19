<?php

namespace App\Http\Controllers;

use App\Model\Club\ClubInfo;
use Illuminate\Support\Facades\Auth;
use Session;
use App\Model\Staff\StaffAttendance;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ManualAttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('wm-only');
    }

    public function staff_login(Request $request) {
        $this->validate($request, [
            'password' => 'required',
        ]);

        $the_status = 0;

        if (!empty($request->email)):
            $check = User::where('email', '=', $request->email)->first();

            if ($check && Hash::check($request->password, $check->password)):
                $the_status = 1;
            endif;
        else:
            if (!empty($request->key_tag_id)):
                $keytag = $request->key_tag_id;
                $check = User::whereHas('user_memberpersonal', function ($q) use ($keytag) {
                        $q->where('key_tag_id', '=', $keytag);
                    })->first();

                if ($check && Hash::check($request->password, $check->password)):
                    $the_status = 1;
                endif;
            endif;
        endif;

        if ($the_status == 1):
            if (!empty($check)):
                $attendance = new StaffAttendance;
                $attendance->user_id = $check->id;
                $attendance->sign_in = Carbon::now();
                $attendance->login_ip_address = $request->ip();
                $attendance->club_id = Auth::user()->club_id;
                $attendance->save();

                Session::flash('flash_message', trans('messages.thanks_for_sign_in'));
            endif;
        else:
            Session::flash('flash_message', trans('messages.sign_in_failed'));
        endif;

        return redirect('cf-lite/manual-login/choose');
    }

    public function staff_login_list(Request $request) {
        $club_id = Auth::user()->club_id;
        if (!empty($request->clubs)):
            $club_id = $request->clubs;
        endif;

        $club = ClubInfo::findOrFail($club_id);
        $clubs = ClubInfo::orderBy('club_name', 'asc')->pluck('club_name', 'id');

        $staffs = User::where('role_id', '<>', '7')
            ->whereHas('user_staffattendance', function ($q) use ($club_id){
                $q->whereDate('sign_in', '=', Carbon::today()->toDateString())->where('club_id', '=', $club_id);
            })->paginate(12);

        return view('cf_lite.manual-login.staff-login-list', compact('staffs', 'club', 'clubs'));
    }

    public function staff_logout(Request $request) {
        $the_status = 0;
        $check = User::findOrFail($request->staff_so_id);
        if ($check && Hash::check($request->password, $check->password)):
            $the_status = 1;
        endif;

        $user = StaffAttendance::where('user_id', '=', $check->id)->where('sign_out', null)->orderBy('sign_in', 'desc')->first();
        $attendance = StaffAttendance::findOrFail($user->id);
        if ($the_status == 1):
            $attendance->sign_out = Carbon::now();
            $attendance->save();
            Session::flash('flash_message', trans('messages.sign_out_successful'));
        elseif ($the_status == 0):
            Session::flash('flash_message', trans('messages.wrong_password'));
        endif;

        return redirect('cf-lite/manual-login/sign-out?clubs='.$attendance->staffattendance_user->club_id);
    }
}
