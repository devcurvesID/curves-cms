<?php

namespace App\Http\Controllers;

use Session;
use App\Model\Appointment\GuestAppointment;
use App\Model\Appointment\GuestAppointmentStatus;
use App\Model\Guest\BuddyReferralLead;
use App\Model\Member\MemberPersonal;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalkInController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('wm-only');
    }

    public function store_walk_in(Request $request) {
        $this->validate($request, [
            'fa_date' => 'required|date|before:tomorrow',
            'name' => 'required|string',
            'phone' => 'required|numeric|digits_between:10,15',
            'source' => 'required',
            'staff' => 'sometimes',
        ]);

        //Check Double Member?
        $checker = 0;
        $check_user = User::where('name', 'ILIKE', '%'.$request->name.'%')
            ->whereHas('user_memberstatus', function ($q) {
                $q->where('status', '<>', 'Inactive')
                    ->where('status', '<>', 'Stopped');
            })->get();
        if (count($check_user) > 0):
            $checker++;
        endif;

        $check_phone = MemberPersonal::where('phone', '=', $request->phone)->orWhere('cellphone', '=', $request->phone)->get();
        if (count($check_phone) > 0):
            $checker++;
        endif;
        //End of Check Double Member?

        if ($checker < 2):
            //Buddy Referral Lead
            $lead = new BuddyReferralLead;
            $lead->fullname = $request->name;
            $lead->phone = $request->phone;
            $lead->source_id = $request->source;

            if ($request->source == '1'):
                $lead->is_br = 'BR';
            else:
                $lead->is_br = 'NotBR';
            endif;

            if (!empty($request->staff)):
                $lead->staff_id = $request->staff;
            else:
                if (!empty(session('staff_session'))):
                    $lead->staff_id = session('staff_session');
                else:
                    $lead->staff_id = Auth::id();
                endif;
            endif;

            $lead->club_id = Auth::user()->club_id;
            $lead->status_gm = 'Guest';
            $lead->save();

            //Guest Appointment
            $guest_appt = new GuestAppointment;
            $guest_appt->guest_id = $lead->id;
            $guest_appt->staff_id = $lead->staff_id;
            $guest_appt->fa_status = 'NonFA';

            $datetime = Carbon::createFromFormat('Y-m-d H:i', Carbon::parse($request->fa_date.' 09:01')->format('Y-m-d H:i'), session('timezone'))->setTimezone('UTC');

            $guest_appt->fa_date = $datetime;
            $guest_appt->fa_hour = date('H:i', strtotime($datetime));
            $guest_appt->guest_status_after = 'Lead';
            $guest_appt->club_id = $lead->club_id;
            $guest_appt->save();

            //Guest Appointment Status
            $guest_appt_status = new GuestAppointmentStatus;
            $guest_appt_status->status_id = '5';
            $guest_appt_status->c_date = $datetime;
            $guest_appt_status->s_date = $datetime;
            $guest_appt_status->sa_date = $datetime;
            $guest_appt->guestappointment_gastatus()->save($guest_appt_status);

            //Staff Point Added 22012019
            app('App\Http\Controllers\PointStaffController')->staff_point_apo($lead->staff_id, $request->fa_date);
            app('App\Http\Controllers\PointStaffController')->staff_point_apo_showup($lead->staff_id, $request->fa_date, 'showup');

            //Staff Point BR Added 23012019
            app('App\Http\Controllers\PointStaffController')->add_br_point($lead->staff_id, 28, $request->fa_date, $lead);

            if ($request->input('modal-attr') == 'payment'):
                return redirect('cf-lite/pre-member/'.$lead->id.'/membership');
            elseif ($request->input('modal-attr') == 'corporate'):
                return redirect('cf-lite/corporate/corporation-options');
            endif;
        else:
            Session::flash('flash_message', trans('messages.member_exist'));
            return redirect('cf-lite/new-member');
        endif;
    }

    public function ajax_walk_in(Request $request) {
        $this->validate($request, [
            'fa_date' => 'required|date|before:tomorrow',
            'name' => 'required|string',
            'phone' => 'required|numeric',
            'source' => 'required',
            'status' => 'required'
        ]);

        //Check Double Member?
        $checker = 0;
        $check_user = User::where('name', 'ILIKE', '%'.$request->name.'%')
            ->whereHas('user_memberstatus', function ($q) {
                $q->where('status', '<>', 'Inactive')
                    ->where('status', '<>', 'Stopped');
            })->get();
        if (count($check_user) > 0):
            $checker++;
        endif;

        $check_phone = MemberPersonal::where('phone', '=', $request->phone)->orWhere('cellphone', '=', $request->phone)->get();
        if (count($check_phone) > 0):
            $checker++;
        endif;
        //End of Check Double Member?

        if ($checker < 2):
            //Buddy Referral Lead
            $lead = new BuddyReferralLead;
            $lead->fullname = $request->name;
            $lead->phone = $request->phone;
            $lead->source_id = $request->source;

            if ($request->source == '1'):
                $lead->is_br = 'BR';
            else:
                $lead->is_br = 'NotBR';
            endif;

            if (!empty(session('staff_session'))):
                $lead->staff_id = session('staff_session');
            else:
                $lead->staff_id = Auth::id();
            endif;

            $lead->club_id = Auth::user()->club_id;
            $lead->status_gm = 'Guest';
            $lead->save();

            //Guest Appointment
            $guest_appt = new GuestAppointment;
            $guest_appt->guest_id = $lead->id;
            $guest_appt->staff_id = $lead->staff_id;
            $guest_appt->fa_status = 'NonFA';

            $datetime = Carbon::createFromFormat('Y-m-d H:i', Carbon::parse($request->fa_date.' 09:01')->format('Y-m-d H:i'), session('timezone'))->setTimezone('UTC');

            $guest_appt->fa_date = $datetime;
            $guest_appt->fa_hour = date('H:i', strtotime($datetime));
            $guest_appt->guest_status_after = 'Lead';
            $guest_appt->club_id = $lead->club_id;
            $guest_appt->save();

            //Guest Appointment Status
            $guest_appt_status = new GuestAppointmentStatus;
            $guest_appt_status->status_id = $request->status;
            $guest_appt_status->c_date = $datetime;
            $guest_appt_status->s_date = $datetime;
            $guest_appt_status->sa_date = $datetime;
            $guest_appt->guestappointment_gastatus()->save($guest_appt_status);

            return response()->json(array('guest' => $lead, 'exist' => 'not'));
        else:
            return response()->json(array('exist' => 'exist'));
        endif;
    }
}
