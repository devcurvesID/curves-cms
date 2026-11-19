<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddBulkContactedLeadRequest;
use App\Http\Requests\AddNewAppointmentRequest;
use App\Http\Requests\WMWORequest;
use App\Model\Appointment\GuestAppointmentStatus;
use App\Model\Appointment\GuestNoSaleReason;
use App\Model\Appointment\MemberAppointment;
use App\Model\Appointment\MemberAppointmentNote;
use App\Model\Appointment\MemberAppointmentStatus;
use App\Model\Appointment\RescheduledAppointment;
use App\Model\Guest\Referrer;
use App\Model\Log\LogContactedLead;
use App\Model\MasterData\AppointmentType;
use App\Model\MasterData\MemberSource;
use App\Model\MasterData\NoSaleReason;
use App\Model\Workout\Workout;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Session;
use App\Model\Appointment\GuestAppointmentNote;
use App\Http\Requests\FARequest;
use App\Model\Appointment\GuestAppointment;
use App\Model\Guest\BuddyReferralLead;
//use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('wm-only');
    }

    public function index() {
        session()->forget('date');
        session()->forget('time');
        session()->forget('appointment_type');

        $start = new Carbon('first day of this month');
        $end = new Carbon('last day of this month');

        //Added 08082018
        $club_id = Auth::user()->club_id;

        $fas = GuestAppointment::whereDate('fa_date', '>=', $start)
            //Added 08082018
            ->whereHas('guestappointment_buddyreferrallead', function ($q) use ($club_id) {
                $q->where('club_id', '=', $club_id);
            })
            ->whereDate('fa_date', '>=', $start->subDays(30)->toDateString())
            ->whereDate('fa_date', '<=', $end->addMonth(3)->toDateString())
            ->get();

        $mas = MemberAppointment::whereDate('app_date', '>=', $start)
            //Added 08082018
            ->whereHas('memberappointment_user', function ($q) use ($club_id) {
                $q->where('club_id', '=', $club_id);
            })
            ->whereDate('app_date', '>=', $start->subDays(30)->toDateString())
            ->whereDate('app_date', '<=', $end->addMonth(3)->toDateString())
            ->get();

        $appointment_types = AppointmentType::orderby('id', 'asc')->pluck('type', 'id');

        return view('cf_lite.dashboard', compact('fas', 'mas', 'appointment_types'));
    }

    //Fitness Assessment
    public function fa_choose_lead($date, $time) {
        session(['date' => $date]);
        session(['time' => $time]);

        return view('cf_lite.appointment.fa-choose-lead');
    }

    public function fa_lead_list() {
        session(['appointment_type' => 'FA']);

        $leads = BuddyReferralLead::where('is_br', 'NotBR')
            ->where('status_gm', '=', 'Guest')
            ->whereDoesntHave('buddyreferrallead_guestappointment')
            ->orWhere(function($q) {
                $q->whereHas('buddyreferrallead_guestappointment', function($r) {
                    $r->where('guest_status_after', '=', 'Lead')
                        ->where('fa_date', '<', Carbon::today()->toDateString())
                        ->whereHas('guestappointment_gastatus', function ($s) {
                            $s->where('sa_date', null);
                        });
                });
            })
            ->orderBy('fullname', 'asc');
        $submit_link = 'cf-lite/appointment/submit/fa/';

        $leads_json = $leads->get();
        $leads = $leads->simplePaginate(12);

        $sources = MemberSource::where('source', '<>', 'BR')->orderBy('source', 'asc')->pluck('source', 'id');

        return view('cf_lite.appointment.lead-list', compact('leads', 'submit_link', 'leads_json', 'sources'));
    }
	
    public function fa_lead_list_bulk() {
        session(['appointment_type' => 'FA']);

        $leads = BuddyReferralLead::where('is_br', 'NotBR')
            ->where('status_gm', '=', 'Guest')
            ->whereDoesntHave('buddyreferrallead_guestappointment')
            ->orWhere(function($q) {
                $q->whereHas('buddyreferrallead_guestappointment', function($r) {
                    $r->where('guest_status_after', '=', 'Lead')
                        ->where('fa_date', '<', Carbon::today()->toDateString())
                        ->whereHas('guestappointment_gastatus', function ($s) {
                            $s->where('sa_date', null);
                        });
                });
            })
            ->orderBy('fullname', 'asc');
        $submit_link = 'cf-lite/appointment/submit/fa/';

        $leads_json = $leads->get();
        $leads = $leads->simplePaginate(12);

        $sources = MemberSource::where('source', '<>', 'BR')->orderBy('source', 'asc')->pluck('source', 'id');

        return view('cf_lite.appointment.lead-list-bulk', compact('leads', 'submit_link', 'leads_json', 'sources'));
    }

    public function fa_br_list() {
        session(['appointment_type' => 'FA']);
        session(['br' => 'BR']);

        $leads = BuddyReferralLead::where('is_br', 'BR')
            ->where('status_gm', '=', 'Guest')
            ->whereHas('buddyreferrallead_referrer')
            ->whereDoesntHave('buddyreferrallead_guestappointment')
            ->orWhere(function($q) {
                $q->whereHas('buddyreferrallead_guestappointment', function($r) {
                    $r->where('guest_status_after', '=', 'Lead')
                        ->where('fa_date', '<', Carbon::today()->toDateString())
                        ->whereHas('guestappointment_gastatus', function ($s) {
                            $s->where('sa_date', null);
                        });
                });
            })
            ->orderBy('fullname', 'asc');
        $submit_link = 'cf-lite/appointment/submit/fa/';

        $leads_json = $leads->get();
        $leads = $leads->simplePaginate(12);

        $members = User::where('role_id', '7')->orderBy('name', 'asc')->get();

        return view('cf_lite.appointment.br-list', compact('leads', 'submit_link', 'leads_json', 'members'));
    }

    public function fa_submit_detail($id) {
        $lead = BuddyReferralLead::findOrFail($id);

        return view('cf_lite.appointment.fa-send-detail', compact('lead'));
    }

    public function store_fa(FARequest $request) {
        if ($request->appointment_type == 'FA'):
            $fa = new GuestAppointment;

            $fa->guest_id = $request->lead_id;
            $fa->staff_id = Auth::id();
            $fa->fa_status = 'FA';
            $fa->fa_date = $request->date;
            $fa->fa_hour = $request->time;
            $fa->club_id = Auth::user()->club_id;
            $fa->save();

            if (!empty($request->note)):
                $note = new GuestAppointmentNote;
                $note->note = $request->note;
                $fa->guestappointment_ganotes()->save($note);
            endif;

            session()->forget('date');
            session()->forget('time');
            session()->forget('appointment_type');

            Session::flash('cf_lite.includes.flash-message', trans('messages.fitness_assessment_appointment_added'));

            return redirect('cf-lite/appointment');
        endif;
    }

    //Weigh & Measures
    public function wm_member_list($date, $time) {
        session(['date' => $date]);
        session(['time' => $time]);
        session(['appointment_type' => 'WM']);

        $members = User::where('role_id', '7')->orderBy('name', 'asc');

        $submit_link = 'cf-lite/appointment/submit/wm-wo/';

        $members_json = $members->get();
        $members = $members->simplePaginate(12);

        return view('cf_lite.appointment.wm-wo-list', compact('members', 'submit_link', 'members_json'));
    }

    public function wo_member_list($date, $time) {
        session(['date' => $date]);
        session(['time' => $time]);
        session(['appointment_type' => 'WO']);

        $members = User::where('role_id', '7')->orderBy('name', 'asc');

        $submit_link = 'cf-lite/appointment/submit/wm-wo/';

        $members_json = $members->get();
        $members = $members->simplePaginate(12);

        return view('cf_lite.appointment.wm-wo-list', compact('members', 'submit_link', 'members_json'));
    }

    public function wm_wo_submit_detail($id) {
        $member = User::findOrFail($id);

        $staffs = User::where('role_id', '<>', '7')->orderBy('name', 'asc')->pluck('name', 'id');

        return view('cf_lite.appointment.wm-wo-send-detail', compact('member', 'staffs'));
    }

    public function store_wm_wo(WMWORequest $request) {
        if (!empty($request->appointment_type)):
            if ($request->appointment_type == 'WM'):
                $appointment_type = 1;
                Session::flash('cf_lite.includes.flash-message', trans('messages.weigh_n_measure_appointment_added'));
            elseif ($request->appointment_type == 'WO'):
                $appointment_type = 2;
                Session::flash('cf_lite.includes.flash-message', trans('messages.workout_appointment_added'));
            endif;

            $appointment = new MemberAppointment;
            $appointment->user_id = $request->member_id;
            $appointment->staff_id = $request->staff_id;
            $appointment->ap_type_id = $appointment_type;
            $appointment->app_date = $request->date;
            $appointment->app_hour = $request->time;
            $appointment->save();

            if (!empty($request->confirmed)):
                $ma_status = new MemberAppointmentStatus;
                $ma_status->status_id = '1';
                $ma_status->c_date = Carbon::now();
                $appointment->memberappointment_mastatus()->save($ma_status);
            endif;

            if (!empty($request->note)):
                $ma_note = new MemberAppointmentNote;
                $ma_note->notes = $request->note;
                $appointment->memberappointment_memberappointmentnote()->save($ma_note);
            endif;

            return redirect('cf-lite/appointment');
        endif;
    }

    //Appointment Status
    public function fa_status($id) {
        $appointment = GuestAppointment::findOrFail($id);
        session(['appointment' => 'Guest']);

        $nosalereasons = NoSaleReason::orderBy('nosale_reason', 'asc')->pluck('nosale_reason', 'id');

        return view('cf_lite.appointment.appointment-status', compact('appointment', 'nosalereasons'));
    }

    public function ma_status($id) {
        $appointment = MemberAppointment::findOrFail($id);
        session(['appointment' => 'Member']);

        return view('cf_lite.appointment.appointment-status', compact('appointment'));
    }

    public function update_status($id, Request $request) {
        if (session('appointment') == 'Guest'):
            $appointment = GuestAppointment::findOrFail($id);

            if (!empty($appointment->guestappointment_gastatus)):
                $appointment_status = $appointment->guestappointment_gastatus;
                $appointment_status->status_id = $request->status_id;
                if (($request->status_id == '3') || ($request->status_id == '4')):
                    $appointment_status->s_date = Carbon::now();
                elseif (($request->status_id == '5') || ($request->status_id == '6')):
                    $appointment_status->sa_date = Carbon::now();
                    if ($request->status_id == '6'):
                        $nosale = new GuestNoSaleReason;
                        $nosale->no_sale_reason_id = $request->nosale_reason;
                        $appointment->guestappointment_guestnosalereason()->save($nosale);
                    endif;
                endif;
            else:
                $appointment_status = new GuestAppointmentStatus;
                $appointment_status->status_id = $request->status_id;
                $appointment_status->c_date = Carbon::now();
            endif;
            $appointment->guestappointment_gastatus()->save($appointment_status);
            $redirect = 'cf-lite/appointment/status/fa/'.$id;
        elseif (session('appointment') == 'Member'):
            $appointment = MemberAppointment::findOrFail($id);

            if (!empty($appointment->memberappointment_mastatus)):
                $appointment_status = $appointment->memberappointment_mastatus;
                $appointment_status->status_id = $request->status_id;
                if (($request->status_id == '3') || ($request->status_id == '4')):
                    $appointment_status->s_date = Carbon::now();

                    //Cek jika FWO
                    if ($request->status_id == '3'):
                        $check_wo = Workout::where('user_id', '=', $appointment->user_id)->get()->count();
                        if (empty($check_wo)):
                            $fwo = new Workout;
                            $fwo->user_id = $appointment->user_id;
                            $fwo->workout_date = Carbon::now();
                            $fwo->save();
                        endif;
                    endif;
                endif;
            else:
                $appointment_status = new MemberAppointmentStatus;
                $appointment_status->status_id = $request->status_id;
                $appointment_status->c_date = Carbon::now();
            endif;
            $appointment->memberappointment_mastatus()->save($appointment_status);
            $redirect = 'cf-lite/appointment/status/ma/'.$id;
        endif;
        session()->forget('appointment');
        Session::flash('cf_lite.includes.flash-message', trans('messages.appointment_status_updated'));

        if ($request->status_id == '5'):
            return redirect('cf-lite/pre-member/'.$appointment->guest_id.'/membership');
        else:
            return redirect($redirect);
        endif;
    }

    public function store_fa_reschedule($id, Request $request) {
        $this->validate($request, [
            'reschedule_date' => 'required',
            'reschedule_time' => 'required'
        ]);

        $appointment = GuestAppointment::findOrFail($id);

        //Add new Rescheduled Appointment
        $reschedule = new RescheduledAppointment;
        $reschedule->appointment_type = session('appointment');
        $reschedule->appointment_id = $appointment->id;
        $reschedule->original_schedule_date = $appointment->fa_date;
        $reschedule->original_schedule_time = $appointment->fa_hour;
        $reschedule->new_schedule_date = $request->reschedule_date;
        $reschedule->new_schedule_time = $request->reschedule_time;
        $reschedule->last_status = $appointment->guestappointment_gastatus->status_id;
        $reschedule->rescheduled_by = Auth::id();
        $reschedule->save();


        //Update Appointment
        $appointment->fa_date = $request->reschedule_date;
        $appointment->fa_hour = $request->reschedule_time;
        $appointment->save();


        //Update Appointment Status
        $appointment_status = $appointment->guestappointment_gastatus;

        if (empty($appointment_status)):
            $appointment_status = new GuestAppointmentStatus;
        else:
            if ($appointment_status->status_id == '4'):
                $appointment_status->s_date = null;
            endif;
        endif;

        $appointment_status->status_id = '1';
        $appointment_status->c_date = Carbon::now();
        $appointment->guestappointment_gastatus()->save($appointment_status);

        Session::flash('cf_lite.includes.flash-message', trans('messages.appointment_rescheduled'));

        return redirect('cf-lite/appointment/status/fa/'.$id);
    }

    public function store_ma_reschedule($id, Request $request) {
        $this->validate($request, [
            'reschedule_date' => 'required',
            'reschedule_time' => 'required'
        ]);

        $appointment = MemberAppointment::findOrFail($id);

        //Add new Rescheduled Appointment
        $reschedule = new RescheduledAppointment;
        $reschedule->appointment_type = session('appointment');
        $reschedule->appointment_id = $appointment->id;
        $reschedule->original_schedule_date = $appointment->app_date;
        $reschedule->original_schedule_time = $appointment->app_hour;
        $reschedule->new_schedule_date = $request->reschedule_date;
        $reschedule->new_schedule_time = $request->reschedule_time;
        $reschedule->last_status = $appointment->memberappointment_mastatus->status_id;
        $reschedule->rescheduled_by = Auth::id();
        $reschedule->save();


        //Update Appointment
        $appointment->app_date = $request->reschedule_date;
        $appointment->app_hour = $request->reschedule_time;
        $appointment->save();


        //Update Appointment Status
        $appointment_status = $appointment->memberappointment_mastatus;

        if (empty($appointment_status)):
            $appointment_status = new MemberAppointmentStatus;
        else:
            if ($appointment_status->status_id == '4'):
                $appointment_status->s_date = null;
            endif;
        endif;

        $appointment_status->status_id = '1';
        $appointment_status->c_date = Carbon::now();
        $appointment->memberappointment_mastatus()->save($appointment_status);

        Session::flash('cf_lite.includes.flash-message', trans('messages.appointment_rescheduled'));

        return redirect('cf-lite/appointment/status/ma/'.$id);
    }

    public function book_choose_date() {
        return view('cf_lite.appointment.choose-appointment-book');
    }

    public function bulk_appointment_list($date) {
        $guest_appointments = GuestAppointment::where('guest_status_after', '=', 'Lead')
            ->whereDate('fa_date', '=', $date)
            ->orderBy('fa_hour', 'asc')
            ->paginate(100);

        return view('cf_lite.appointment.appointment-book-list', compact('guest_appointments', 'date'));
    }

    public function store_bulk_contacted_leads($date, AddBulkContactedLeadRequest $request) {
        $confirms = $request->confirm;
        $no_answers = $request->no_answer;
        $contacted = $request->contacted;
        $reschedules = $request->reschedule;
        $reschedule_dates = $request->reschedule_date;
        $reschedule_times = $request->reschedule_time;

        //Cek lead yang sudah dikontak saat ini
        if (!empty($contacted)):
            foreach ($contacted as $key => $value):
                $guest_appointment = GuestAppointment::findOrFail($value);

                //Cek Konfirmasi
                if (!empty($confirms[$key])):
                    //Guest Appointment Status
                    if (empty($guest_appointment->guestappointment_gastatus)):
                        $ga_status = new GuestAppointmentStatus;
                        $ga_status->status_id = '1';
                        $ga_status->c_date = Carbon::now();
                        $guest_appointment->guestappointment_gastatus()->save($ga_status);
                    endif;
                endif;

                //Cek Reschedule
                if (!empty($reschedules[$key]) && !empty($reschedule_dates[$key]) && !empty($reschedule_times[$key])):
                    $rescheduled_appointment = new RescheduledAppointment;
                    $rescheduled_appointment->appointment_type = 'Guest';
                    $rescheduled_appointment->appointment_id = $value;
                    $rescheduled_appointment->original_schedule_date = $guest_appointment->fa_date;
                    $rescheduled_appointment->original_schedule_time = $guest_appointment->fa_hour;
                    $rescheduled_appointment->new_schedule_date = $reschedule_dates[$key];
                    $rescheduled_appointment->new_schedule_time = $reschedule_times[$key];

                    if (empty($guest_appointment->guestappointment_gastatus)):
                        $rescheduled_appointment->last_status = '1';

                        $ga_status = new GuestAppointmentStatus;
                        $ga_status->status_id = '1';
                        $ga_status->c_date = Carbon::now();
                    else:
                        $rescheduled_appointment->last_status = $guest_appointment->guestappointment_gastatus->status_id;

                        $ga_status = $guest_appointment->guestappointment_gastatus;
                        $ga_status->status_id = '1';
                        $ga_status->c_date = Carbon::now();
                        $ga_status->s_date = null;
                        $ga_status->sa_date = null;
                    endif;
                    $guest_appointment->guestappointment_gastatus()->save($ga_status);

                    $rescheduled_appointment->rescheduled_by = Auth::id();
                    $rescheduled_appointment->save();

                    $guest_appointment->fa_date = $reschedule_dates[$key];
                    $guest_appointment->fa_hour = $reschedule_times[$key];
                    $guest_appointment->save();
                endif;

                //Cek No Answer
                $opengate = 0;
                $log_contacted_lead = new LogContactedLead;
                $log_contacted_lead->guest_appointment_id = $value;
                $log_contacted_lead->staff_id = Auth::id();

                if ($no_answers[$key] > 0):
                    $no_answered_calls = LogContactedLead::where('guest_appointment_id', '=', $value)
                        ->where('status_id', '=', '7')
                        ->get();

                    if (count($no_answered_calls) < $no_answers[$key]):
                        $log_contacted_lead->status_id = '7';
                    else:
                        $opengate = 1;
                    endif;
                else:
                    $opengate = 1;
                endif;

                if ($opengate == '1'):
                    if (!empty($guest_appointment->guestappointment_gastatus)):
                        $log_contacted_lead->status_id = $guest_appointment->guestappointment_gastatus->status_id;
                    else:
                        $log_contacted_lead->status_id = '1';
                    endif;
                endif;

                $log_contacted_lead->save();
            endforeach;
        endif;

        return redirect('cf-lite/appointment/book/'.$date);
    }

//    public function attend_choose_date() {
//        return view('cf_lite.appointment.choose-appointment-attend');
//    }

    public function attendance_main(Request $request) {
        //Menampilkan Appointment di kalender
        if (!empty($request->date_chosen)):
            $date = Carbon::parse($request->date_chosen);
        else:
            $date = Carbon::today();
        endif;

        $start = new Carbon('first day of this month');
        $end = new Carbon('last day of this month');

        $fas = GuestAppointment::where('club_id', '=', Auth::user()->club_id)
            ->whereDate('fa_date', '>=', $start->subMonths(1)->toDateString())
            ->whereDate('fa_date', '<=', $end->addMonth(3)->toDateString())
            ->get();

        //Attendance List Today
        $guest_appointments = GuestAppointment::where('club_id', '=', Auth::user()->club_id)
            ->whereDate('fa_date', '=', $date->toDateString())
            ->whereHas('guestappointment_gastatus', function($q) {
                $q->whereNotNull('c_date');
            })
            ->orderBy('fa_hour', 'asc')
            ->paginate(100);

        //Attendance Notification before today
//        $need_completed_list = GuestAppointment::whereDate('fa_date', '<', $date->toDateString())
//            ->whereHas('guestappointment_gastatus', function ($q) {
//                $q->whereNotNull('s_date');
//            })
//            ->orderBy('fa_hour', 'asc')
//            ->get();

        $need_completed_list = GuestAppointment::where('club_id', '=', Auth::user()->club_id)
            ->whereDate('fa_date', '<', Carbon::today()->toDateString())
            ->whereHas('guestappointment_gastatus', function ($q) {
                $q->where('status_id', '=', '3')
                    ->orWhere('status_id', '=', '5')
                    ->orWhere('status_id', '=', '6');
            })
            ->whereHas('guestappointment_buddyreferrallead', function ($r) {
                $r->whereDoesntHave('buddyreferrallead_guestdetail');
            })
            ->get();

        return view('cf_lite.appointment.attendance-book-list-v2', compact('fas', 'guest_appointments', 'need_completed_list', 'date'));
    }

    public function bulk_attendance_list($date) {
        /*$guest_appointments = GuestAppointment::where('guest_status_after', '=', 'Lead')*/
        $guest_appointments = GuestAppointment::whereDate('fa_date', '=', $date)
            ->whereHas('guestappointment_gastatus', function($q) {
                $q->whereNotNull('c_date');
            })
            ->orderBy('fa_hour', 'asc')
            ->get();

        return view('cf_lite.appointment.attendance-book-list', compact('guest_appointments', 'date'));
    }

    public function store_bulk_attendance_leads($date, Request $request) {
        $showups = $request->showup;
        $sales = $request->sale;

        //Cek lead yang show up
        if (!empty($showups)):
            foreach ($showups as $key => $value):
                $guest_appointment = GuestAppointment::findOrFail($value);
				
				$guest = BuddyReferralLead::findOrfail($guest_appointment->guest_id);
                //$refferers = Referrer::where('referral_id','=',$guest->id)->first();
                //if($refferers!=null)
                    app('App\Http\Controllers\PointStaffController')->add_br_point($guest->staff_id,29,$date,$guest);
				
                if (!empty($guest_appointment->guestappointment_gastatus) && ($guest_appointment->guestappointment_gastatus->status_id == '1')):
                    $ga_status = $guest_appointment->guestappointment_gastatus;
                    $ga_status->status_id = '3';
                    $ga_status->s_date = Carbon::now();
                    $guest_appointment->guestappointment_gastatus()->save($ga_status);
                endif;

                //Cek jika sale
                $ga_sale = $guest_appointment->guestappointment_gastatus;

                if (!empty($sales[$key])):
                    $ga_sale->status_id = '5';
                else:
                    $ga_sale->status_id = '6';
                endif;

                $ga_sale->sa_date = Carbon::now();
                $guest_appointment->guestappointment_gastatus()->save($ga_sale);

                //Add Point Staff
                app('App\Http\Controllers\PointStaffController')->staff_point_apo_showup($guest_appointment->staff_id, $guest_appointment->fa_date, 'showup');
            endforeach;
        endif;

        Session::flash('cf_lite.includes.flash-message', trans('messages.attendance_updated'));

        return redirect('cf-lite/appointment/attendance');
    }

    public function create_appointment_ver_2(AddNewAppointmentRequest $request) {
        $real_date = Carbon::createFromFormat('Y-m-d H:i', Carbon::parse($request->appointment_date.' '.$request->appointment_hour)->format('Y-m-d H:i'), session('timezone'))->setTimezone('UTC');
        if ($request->has('btn-lead')):
            if ($request->appointment_type == 'FA'):
                $fa = new GuestAppointment;
                $fa->guest_id = $request->user_id;
                $fa->staff_id = Auth::id();
                $fa->fa_status = 'FA';
                $fa->fa_date = date('Y-m-d', strtotime($real_date));
                $fa->fa_hour = date('H:i', strtotime($real_date));
                $fa->club_id = Auth::user()->club_id;
                $fa->save();

                if (!empty($request->appointment_note)):
                    $appointment_note = $fa->guestappointment_ganotes;
                    $appointment_note->note = $request->appointment_note;
                    $fa->guestappointment_ganotes()->save($appointment_note);
                endif;

                $fa_status = new GuestAppointmentStatus;
                $fa_status->status_id = '1';
                $fa_status->c_date = date('Y-m-d H:i:s', strtotime($real_date));
                $fa->guestappointment_gastatus()->save($fa_status);

                Session::flash('cf_lite.includes.flash-message', trans('messages.fa_has_been_added'));
            endif;
        elseif ($request->has('btn-member')):
            $member_appointment = new MemberAppointment;
            $member_appointment->user_id = $request->user_id;
            $member_appointment->staff_id = Auth::id();
            $member_appointment->ap_type_id = $request->appointment_type;


            $member_appointment->app_date = date('Y-m-d', strtotime($real_date));
            $member_appointment->app_hour = date('H:i', strtotime($real_date));
            $member_appointment->save();

            if (!empty($request->appointment_note)):
                $appointment_note = $member_appointment->memberappointment_memberappointmentnote;
                $appointment_note->notes = $request->appointment_note;
                $member_appointment->memberappointment_memberappointmentnote()->save($appointment_note);
            endif;
            Session::flash('cf_lite.includes.flash-message', trans('messages.appointment_has_been_made'));
        elseif ($request->has('btn-guest-reschedule')):
            $appointment = GuestAppointment::findOrFail($request->appointment_id);

            //Add new Rescheduled Appointment
            $reschedule = new RescheduledAppointment;
            $reschedule->appointment_type = session('appointment');
            $reschedule->appointment_id = $appointment->id;
            $reschedule->original_schedule_date = $appointment->fa_date;
            $reschedule->original_schedule_time = $appointment->fa_hour;
            $reschedule->new_schedule_date = date('Y-m-d', strtotime($real_date));
            $reschedule->new_schedule_time = date('H:i', strtotime($real_date));
            if (!empty($appointment->guestappointment_gastatus)):
                $reschedule->last_status = $appointment->guestappointment_gastatus->status_id;
            else:
                $reschedule->last_status = null;
            endif;
            $reschedule->rescheduled_by = Auth::id();
            $reschedule->save();


            //Update Appointment
            $appointment->fa_date = date('Y-m-d', strtotime($real_date));
            $appointment->fa_hour = date('H:i', strtotime($real_date));
            $appointment->save();


            //Update Appointment Status
            $appointment_status = $appointment->guestappointment_gastatus;

            if (empty($appointment_status)):
                $appointment_status = new GuestAppointmentStatus;
            else:
                if ($appointment_status->status_id == '4'):
                    $appointment_status->s_date = null;
                endif;
            endif;

            $appointment_status->status_id = '1';
            $appointment_status->c_date = Carbon::now();
            $appointment->guestappointment_gastatus()->save($appointment_status);

            //Update Appointment Notes
            if (!empty($request->appointment_note)):
                if (empty($appointment->guestappointment_ganotes)):
                    $appointment_note = new GuestAppointmentNote;
                else:
                    $appointment_note = $appointment->guestappointment_ganotes;
                endif;
                $appointment_note->note = $request->appointment_note;
                $appointment->guestappointment_ganotes()->save($appointment_note);
            endif;

            Session::flash('cf_lite.includes.flash-message', trans('messages.appointment_has_been_rescheduled'));
        elseif ($request->has('btn-member-reschedule')):
            $appointment = MemberAppointment::findOrFail($request->appointment_id);

            //Add new Rescheduled Appointment
            $reschedule = new RescheduledAppointment;
            $reschedule->appointment_type = 'Guest';
            $reschedule->appointment_id = $appointment->id;
            $reschedule->original_schedule_date = $appointment->app_date;
            $reschedule->original_schedule_time = $appointment->app_hour;
            $reschedule->new_schedule_date = date('Y-m-d', strtotime($real_date));
            $reschedule->new_schedule_time = date('H:i', strtotime($real_date));
            if (!empty($appointment->memberappointment_mastatus)):
                $reschedule->last_status = $appointment->memberappointment_mastatus->status_id;
            else:
                $reschedule->last_status = null;
            endif;
            $reschedule->rescheduled_by = Auth::id();
            $reschedule->save();


            //Update Appointment
            $appointment->app_date = date('Y-m-d', strtotime($real_date));
            $appointment->app_hour = date('H:i', strtotime($real_date));
            $appointment->save();


            //Update Appointment Status
            $appointment_status = $appointment->memberappointment_mastatus;

            if (empty($appointment_status)):
                $appointment_status = new MemberAppointmentStatus;
            else:
                if ($appointment_status->status_id == '4'):
                    $appointment_status->s_date = null;
                endif;
            endif;

            $appointment_status->status_id = '1';
            $appointment_status->c_date = Carbon::now();
            $appointment->memberappointment_mastatus()->save($appointment_status);

            //Update Appointment Notes
            if (!empty($request->appointment_note)):
                if (empty($appointment->memberappointment_memberappointmentnote)):
                    $appointment_note = new MemberAppointmentNote;
                else:
                    $appointment_note = $appointment->memberappointment_memberappointmentnote;
                endif;
                $appointment_note->notes = $request->appointment_note;
                $appointment->memberappointment_memberappointmentnote()->save($appointment_note);
            endif;

            Session::flash('cf_lite.includes.flash-message', trans('messages.appointment_has_been_rescheduled'));
        endif;

        return redirect('cf-lite/appointment');
    }

    public function reschedule_view($type, $id) {
        if ($type == 'fa'):
            $appointment = GuestAppointment::findOrFail($id);
            session(['appointment' => 'Guest']);
        elseif ($type == 'ma'):
            $appointment = MemberAppointment::findOrFail($id);
            session(['appointment' => 'Member']);
        endif;

        return view('cf_lite.appointment.reschedule-set', compact('appointment'));
    }

    //Undo Appointment
    public function undo_show_sale($id){
        $ga_status = GuestAppointmentStatus::findOrFail($id);
        $ga_status->status_id = 1;
        $ga_status->s_date = null;
        $ga_status->sa_date = null;
        $ga_status->save();

        //Add Point Staff
        app('App\Http\Controllers\PointStaffController')->staff_point_apo_showup($ga_status->gastatus_guestappointment->staff_id, $ga_status->gastatus_guestappointment->fa_date, 'undo');


        $guest = BuddyReferralLead::findOrFail($ga_status->gastatus_guestappointment->guest_id);
        //$refferers = Referrer::where('referral_id','=',$guest->id)->first();
        //if($refferers!=null)
            app('App\Http\Controllers\PointStaffController')->min_br_point($guest->staff_id,29,$ga_status->gastatus_guestappointment->fa_date,$guest);

        return redirect('cf-lite/appointment/attendance');
    }
}
