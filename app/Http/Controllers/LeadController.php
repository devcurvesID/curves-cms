<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddBulkLeadsAppointmentRequest;
use App\Http\Requests\GuestDetailRequest;
use App\Model\Appointment\GuestAppointment;
use App\Model\Appointment\GuestAppointmentStatus;
use App\Model\Appointment\GuestNoSaleReason;
use App\Model\Appointment\MemberAppointment;
use App\Model\Guest\GuestDetail;
use App\Model\Guest\GuestFat;
use App\Model\Log\LogContactedLead;
use App\Model\Log\LogContactedNewLead;
use App\Model\MasterData\MemberAttribute;
use App\Model\MasterData\NoSaleReason;
use App\Model\Staff\StaffCode;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Session;
use App\Http\Requests\AddBulkLeadRequest;
use App\Http\Requests\BRLRequest;
use App\Model\Guest\BuddyReferralLead;
use App\Model\Guest\Referrer;
use App\Model\MasterData\MemberSource;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('wm-only');
    }

    public function store_lead(BRLRequest $request) {
        $lead = new BuddyReferralLead;

        $lead->fullname = $request->fullname;
        $lead->phone = $request->phone;
        $lead->source_id = $request->source_id;
        $lead->staff_id = Auth::id();
        $lead->save();

        if ($request->has('add-lead')):
            return redirect('cf-lite/appointment/submit/fa/'.$lead->id);
        endif;
    }

    public function store_br(BRLRequest $request) {
        if(!empty(session('br')) && (session('br') == 'BR')):
            $lead = new BuddyReferralLead;

            $lead->fullname = $request->fullname;
            $lead->phone = $request->phone;
            $lead->is_br = 'BR';
            $lead->source_id = '1';
            $lead->staff_id = Auth::id();
            $lead->save();

            $referral = new Referrer;
            $referral->user_id = $request->member_id;
            $referral->status = 'Pending';
            $lead->buddyreferrallead_referrer()->save($referral);

            if ($request->has('add-br')):
                return redirect('cf-lite/appointment/submit/fa/'.$lead->id);
            endif;
        endif;
    }

    public function choose() {
        /*return view('cf_lite.leads.choose-lead-type');*/
        return redirect('cf-lite/lead');
    }

//    public function bulk_add_leads($type) {
//        if ($type == 'regular' || $type == 'br'):
//            session(['lead_type' => $type]);
//
//            if ($type == 'regular'):
//                $sources = MemberSource::where('source', '<>', strtoupper('BR'))->orderBy('source', 'asc')->pluck('source', 'id');
//            elseif ($type == 'br'):
//                $sources = User::where('role_id', '=', '7')->orderBy('name', 'asc')->pluck('name', 'id');
//            endif;
//
//            return view('cf_lite.leads.bulk-add-leads', compact('sources'));
//       else:
//            session()->forget('lead_type');
//            Session::flash('cf_lite.includes.flash-message', trans('messages.please_choose_between_br_not_br'));
//
//            return redirect('cf-lite/lead');
//        endif;
//    }

    public function bulk_add_leads_from_member($type, $id) {
        if ($type == 'br'):
            session(['lead_type' => $type]);

            $sources = User::where('id', '=', $id)->pluck('name', 'id');

            return view('cf_lite.leads.bulk-add-leads', compact('sources'));
        else:
            session()->forget('lead_type');
            Session::flash('flash_message', trans('messages.please_choose_between_br_not_br'));

            return redirect('cf-lite/lead');
        endif;
    }

//    public function store_bulk_leads(AddBulkLeadRequest $request) {
//        $fullnames = $request->fullname;
//        $phones = $request->phone;
//
//        foreach ($fullnames as $key => $value):
//            if (!empty($value)):
//                $lead = new BuddyReferralLead;
//                $lead->fullname = $value;
//                $lead->phone = $phones[$key];
//
//                if (session('lead_type') == 'br'):
//                    $lead->is_br = 'BR';
//                elseif (session('lead_type') == 'regular'):
//                    $lead->source_id = $request->sources;
//                endif;
//
//                $lead->staff_id = Auth::id();
//                $lead->club_id = Auth::user()->club_id;
//                $lead->save();
//
//                if (session('lead_type') == 'br'):
//                    $referrer = new Referrer;
//                    $referrer->user_id = $request->sources;
//                    $referrer->status = 'Pending';
//                    $lead->buddyreferrallead_referrer()->save($referrer);
//                endif;
//            endif;
//        endforeach;
//
//        Session::flash('cf_lite.includes.flash-message', trans('messages.leads_has_been_successfully_added'));
//
//        return redirect('cf-lite/lead');
//    }

	public function bulk_add_leads_v2($type) {
        if ($type == 'regular' || $type == 'br'):
            session(['lead_type' => $type]);

            if ($type == 'regular'):
                $sources = MemberSource::where('source', '<>', strtoupper('BR'))->orderBy('source', 'asc')->pluck('source', 'id');
                $staff_name = User::where('club_id', '=', Auth::user()->club_id)
                    ->where(function ($q){
                        $q->where('role_id', '=', 5)->orWhere('role_id', '=', 6)->get();
                    })->pluck('name', 'id');
            elseif ($type == 'br'):
                $sources = User::where('role_id', '<>', '1')->orderBy('name', 'asc')->pluck('name', 'id');
                $staff_name = User::where('club_id', '=', Auth::user()->club_id)
                    ->where(function ($q){
                        $q->where('role_id', '=', 5)->orWhere('role_id', '=', 6)->get();
                    })->pluck('name', 'id');
            endif;

            $coach = User::selectRaw('users.name, count(buddy_referral_leads.status_gm) as status')
                ->where('users.role_id', '=', 6)->where('buddy_referral_leads.status_gm', '=', 'Guest');

            $coach = $coach->join('buddy_referral_leads', function($join){
               $join->on('users.id', '=', 'buddy_referral_leads.staff_id');
            });

            $coach->groupBy('users.name');
            $coach->orderBy('status', 'DESC');
            $coach = $coach->get();

            return view('cf_lite.leads.bulk-add-leads-v2', compact('sources', 'staff_name', 'coach'));
        else:
            session()->forget('lead_type');
            Session::flash('flash_message', trans('messages.please_choose_between_br_not_br'));

            return redirect('cf-lite/lead');
        endif;
    }
	
	public function store_bulk_leads_v2(AddBulkLeadRequest $request) {
        $fullnames = $request->fullname;
        $phones = $request->phone;
        $fa_date = $request->fa_date;
        $fa_hour = $request->fa_hour;
        if(session('lead_type') == 'regular'){
            $source_detail = $request->source_detail;
        }

        foreach ($fullnames as $key => $value):
            if (!empty($value)):
                $cek = BuddyReferralLead::where('fullname', 'ILIKE', '%'.$value.'%')
                    ->where('phone', '=', $phones[$key])->first();
                if (empty($cek)):
                    $lead = new BuddyReferralLead;
                    $lead->fullname = $value;
                    $lead->phone = $phones[$key];

                    if (session('lead_type') == 'br'):
                        $lead->source_id = '1';
                        $lead->is_br = 'BR';
                        $userbr=User::findOrFail($request->sources);
                        if(!empty($userbr)){
                            $lead->source_detail = $userbr->user_memberpersonal->key_tag_id;
                        }
                        if(empty($fa_date[$key]))
                            app('App\Http\Controllers\PointStaffController')->add_br_point($request->staff[$key],28,Carbon::today(),$lead);
                        else
                            app('App\Http\Controllers\PointStaffController')->add_br_point($request->staff[$key],28,$fa_date[$key],$lead);
                    elseif (session('lead_type') == 'regular'):
                        $lead->source_id = $request->sources;
                        $lead->source_detail = $source_detail[$key];
                    endif;

                    $lead->staff_id = $request->staff[$key];
                    $lead->club_id = Auth::user()->club_id;
                    $lead->save();
                    if (!empty($fa_date[$key]) && !empty($fa_hour[$key])):
                        //Create Appointment
                        $guest_appointment = new GuestAppointment;
                        $guest_appointment->guest_id = $lead->id;
                        $guest_appointment->staff_id = $lead->staff_id;
                        $guest_appointment->fa_status = 'FA';
                        $guest_appointment->fa_date = $fa_date[$key];
                        $guest_appointment->fa_hour = $fa_hour[$key];
                        $guest_appointment->guest_status_after = 'Lead';
                        $guest_appointment->club_id = Auth::user()->club_id;
                        $guest_appointment->save();

                        //Set Appointment Status = Confirmed
                        $ga_status = new GuestAppointmentStatus;
                        $ga_status->status_id = '1';
                        $ga_status->c_date = Carbon::now();
                        $guest_appointment->guestappointment_gastatus()->save($ga_status);

                        //Set log_contacted_leads => Answered
                        $log_contact = new LogContactedLead;
                        $log_contact->guest_appointment_id = $guest_appointment->id;
                        $log_contact->staff_id = $lead->staff_id;
                        $log_contact->status_id = '1';
                        $log_contact->save();

                        //Add Point Staff
                        app('App\Http\Controllers\PointStaffController')->staff_point_apo($request->staff[$key], $fa_date[$key]);
                    endif;

                    if (session('lead_type') == 'br'):
                        $referrer = new Referrer;
                        $referrer->user_id = $request->sources;
                        $referrer->status = 'Pending';
                        $lead->buddyreferrallead_referrer()->save($referrer);
                    endif;
                endif;
            endif;
        endforeach;

        Session::flash('flash-message', trans('messages.leads_has_been_successfully_added'));

        return redirect('cf-lite/lead');
    }

    public function lead_list() {
//        $leads = BuddyReferralLead::where('club_id', '=', Auth::user()->club_id)->where('status_gm', '=', 'Guest')->paginate(500);
        $leads = BuddyReferralLead::where('club_id', '=', Auth::user()->club_id)
            ->where('status_gm', '=', 'Guest')
            ->where(function ($z) {
                $z->where(function ($q) {
                        $q->whereDoesntHave('buddyreferrallead_guestappointment');
                    })
                    ->orWhere(function ($r) {
                        $r->whereHas('buddyreferrallead_guestappointment', function ($s) {
                            $s->whereHas('guestappointment_gastatus', function ($t) {
                                $t->where('status_id', '<>', '5');
                            });
                        });
                    });
            })
            ->paginate(500);

        $start = new Carbon('first day of this month');
        $end = new Carbon('last day of this month');

        $fas = GuestAppointment::whereDate('fa_date', '>=', $start->subMonths(1)->toDateString())
            ->where('club_id', '=', Auth::user()->club_id)
            ->whereDate('fa_date', '<=', $end->addMonth(3)->toDateString())
            ->get();

        $mas = MemberAppointment::whereDate('app_date', '>=', $start->subMonths(1)->toDateString())
            ->whereDate('app_date', '<=', $end->addMonth(3)->toDateString())
            ->whereHas('memberappointment_user', function ($q) {
                $q->where('club_id', '=', Auth::user()->club_id);
            })
            ->get();

        $sources = MemberSource::orderBy('source', 'asc')->pluck('source', 'id');

        return view('cf_lite.leads.lead-list', compact('leads', 'fas', 'mas', 'sources'));
    }

    public function lead_listv2(Request $request) {
//        $leads = BuddyReferralLead::where('club_id', '=', Auth::user()->club_id)->where('status_gm', '=', 'Guest')->paginate(500);
        if (!empty($request->date_chosen)):
            $date = Carbon::parse($request->date_chosen);
            $request->filter_group=null;
        else:
            $date = null;
        endif;

//        if($request->filter_group==null)
//            $request->filter_group='not_fa';

        $leads = BuddyReferralLead::where('club_id', '=', Auth::user()->club_id)
            ->where('status_gm', '=', 'Guest');

        $staffs = User::where('club_id','=', Auth::user()->club_id)
            ->where('role_id','=',5)
            ->orWhere('role_id','=',6)
            ->pluck('name','id');
        if($request->filter_group=='not_fa')
        {
            $leads->where(function ($z) {
                $z->where(function ($q) {$q->whereDoesntHave('buddyreferrallead_guestappointment');})
                    ->orWhere(function ($r) {
                        $r->whereHas('buddyreferrallead_guestappointment', function ($s) {
                            $s->whereHas('guestappointment_gastatus', function ($t) {
                                $t->where('status_id', '=', '1')->orWhere('status_id', '=', '2')->orWhere('status_id', '=', '4');
                            });
                        });
                    });
            });
        }
        elseif ($request->filter_group=='fa')
        {
            $leads->where(function ($z) {
                $z->whereHas('buddyreferrallead_guestappointment', function ($s) {
                            $s->whereHas('guestappointment_gastatus', function ($t) {
                                $t->where('status_id', '=', '3')->orWhere('status_id', '=', '6');
                            });
                        });
                    });
        }
        elseif($request->filter_group=='all')
        {
            $leads->where(function ($z) {
                $z->where(function ($q) {$q->whereDoesntHave('buddyreferrallead_guestappointment');})
                    ->orWhere(function ($r) {
                        $r->whereHas('buddyreferrallead_guestappointment', function ($s) {
                            $s->whereHas('guestappointment_gastatus', function ($t) {
                                $t->where('status_id', '!=', '5');
                            });
                        });
                    });
            });
        }

        if($date!=null)
        {
            $leads->where(function ($z) use($date) {
                $z->whereHas('buddyreferrallead_guestappointment',function ($q) use($date) {
                    $q->whereDate('fa_date','=',$date);
                });
            });
        }

        $leads = $leads->get();

        $start = new Carbon('first day of this month');
        $end = new Carbon('last day of this month');

        $fas = GuestAppointment::whereDate('fa_date', '>=', $start->subMonths(1)->toDateString())
            ->where('club_id', '=', Auth::user()->club_id)
            ->whereDate('fa_date', '<=', $end->addMonth(3)->toDateString())
            ->get();

        $mas = MemberAppointment::whereDate('app_date', '>=', $start->subMonths(1)->toDateString())
            ->whereDate('app_date', '<=', $end->addMonth(3)->toDateString())
            ->whereHas('memberappointment_user', function ($q) {
                $q->where('club_id', '=', Auth::user()->club_id);
            })
            ->get();

        $sources = MemberSource::orderBy('source', 'asc')->pluck('source', 'id');

        return view('cf_lite.leads.lead-listv2', compact('leads', 'fas', 'mas', 'sources','staffs','date'));
    }

    public function edit_lead_ga($id, Request $request)
    {
        $this->validate($request,[
            'staff_in_charge'=>'int|required',
            'fa_date'=>'required',
            'fa_hour'=>'required'
        ]);
        $ga=GuestAppointment::where('guest_id','=',$id)->orderBy('fa_date','desc')->first();
        $gas=GuestAppointmentStatus::where('ga_id','=',$ga->id)->first();

        if ($gas->s_date != null){
            $ga = new GuestAppointment;
            $ga->guest_id = $id;
            $ga->staff_id = $request->staff_in_charge;
            $ga->fa_status = 'FA';
            $ga->fa_date = $request->fa_date;
            $ga->fa_hour = $request->fa_hour;
            $ga->guest_status_after = 'Lead';
            $ga->club_id = Auth::user()->club_id;
            $ga->save();

            $gas = new GuestAppointmentStatus;
            $gas->ga_id = $ga->id;
            $gas->c_date = Carbon::now();
            $gas->status_id = '1';
            $ga->guestappointment_gastatus()->save($gas);
        } else {
            $ga->staff_id = $request->staff_in_charge;
            $ga->fa_date = $request->fa_date;
            $ga->fa_hour = $request->fa_hour;
            $ga->save();
            
            $gas->c_date = Carbon::now();
            $ga->guestappointment_gastatus()->save($gas);
        }

        return redirect('cf-lite/lead');

    }

    public function add_lead_ga($id, Request $request)
    {
        $this->validate($request,[
            'staff_in_charge'=>'int|required',
            'fa_date'=>'required',
            'fa_hour'=>'required'
        ]);
        $ga = new GuestAppointment;
        $ga->guest_id = $id;
        $ga->staff_id = $request->staff_in_charge;
        $ga->fa_status = 'FA';
        $ga->fa_date = $request->fa_date;
        $ga->fa_hour = $request->fa_hour;
        $ga->guest_status_after = 'Lead';
        $ga->club_id = Auth::user()->club_id;

        $ga->save();

        $gas = new GuestAppointmentStatus;
        $gas->ga_id = $ga->id;
        $gas->c_date = Carbon::now();
        $gas->status_id = '1';
        $ga->guestappointment_gastatus()->save($gas);

        //Add point staff
        app('App\Http\Controllers\PointStaffController')->staff_point_apo($request->staff_in_charge, $request->fa_date);

        return redirect('cf-lite/lead');
    }

    public function delete_lead($id)
    {
        $lead=BuddyReferralLead::findOrFail($id);
        $ga = GuestAppointment::where('guest_id','=',$lead->id)->first();
        $lcls = null;
        $ga_status = null;
        if($ga!=null)
        {
            $ga_status = GuestAppointmentStatus::where('ga_id','=',$ga->id)->first();
            $lcls = LogContactedLead::where('guest_appointment_id','=',$ga->id)->get();
        }
        $lcnls = LogContactedNewLead::where('guest_id','=',$lead->id)->get();

        if($lcnls!=null)
        {
            foreach ($lcnls as $lcnl)
            {
                $lcnl->delete();
            }
        }
        if($lcls != null)
        {
            foreach ($lcls as $lcl)
            {
                $lcl->delete();
            }
        }
        if($ga_status!=null)
            $ga_status->delete();
        if($ga!=null)
            $ga->delete();
        $lead->delete();

        return redirect('cf-lite/lead');
    }
	
	public function set_fa(Request $request)
    {
        $ga_status = GuestAppointmentStatus::where('ga_id','=', $request->fa_id_cal)->first();

        if($request->result_fa=='not_show')
        {
            $ga_status->s_date = $ga_status->gastatus_guestappointment->fa_date;
            $ga_status->sa_date = null;
            $ga_status->status_id = 4;
        }
        else
        {
            $ga_status->s_date = $ga_status->gastatus_guestappointment->fa_date;
            $ga_status->sa_date = $ga_status->gastatus_guestappointment->fa_date;
            if ($request->result_fa == 'not_sold')
            {
                $ga_status->status_id = 6;
            }
            else
            {
                $ga_status->status_id = 5;
            }
        }
        $ga_status->save();

        return redirect('cf-lite/lead');
    }

    public function bulk_add_leads_appointment(AddBulkLeadsAppointmentRequest $request) {
        if (count($request->fa_contact) > 0):
            $fa_dates = $request->fa_date;
            $fa_hours = $request->fa_hour;
            $no_answers = $request->no_answer;

            foreach ($request->fa_contact as $key => $value):
                /*if (!empty($fa_dates[$key] && $fa_hours[$key])):
                    //Create Appointment
                    $guest_appointment = new GuestAppointment;
                    $guest_appointment->guest_id = $key;
                    $guest_appointment->staff_id = Auth::id();
                    $guest_appointment->fa_status = 'FA';
                    $guest_appointment->fa_date = $fa_dates[$key];
                    $guest_appointment->fa_hour = $fa_hours[$key];
                    $guest_appointment->guest_status_after = 'Lead';
                    $guest_appointment->club_id = Auth::user()->club_id;
                    $guest_appointment->save();

                    //Set Appointment Status = Confirmed
                    $ga_status = new GuestAppointmentStatus;
                    $ga_status->status_id = '1';
                    $ga_status->c_date = Carbon::now();
                    $guest_appointment->guestappointment_gastatus()->save($ga_status);

                    //Set log_contacted_leads => Answered
                    $log_contact = new LogContactedLead;
                    $log_contact->guest_appointment_id = $guest_appointment->id;
                    $log_contact->staff_id = Auth::id();
                    $log_contact->status_id = '1';
                    $log_contact->save();
                else:
                    //Set log_contacted_new_leads => Not Answered
                    $check_log_contact = LogContactedNewLead::where('guest_id', '=', $key)->get();
                    $diff = $no_answers[$key] - count($check_log_contact);
                    if ($diff > 0):
                        for ($i = 0; $i < $diff; $i++):
                            $log_contact = new LogContactedNewLead;
                            $log_contact->guest_id = $key;
                            $log_contact->staff_id = Auth::id();
                            $log_contact->status = 'NotAnswered';
                            $log_contact->save();
                        endfor;
                    endif;
                endif;*/

                //Set log_contacted_new_leads => Not Answered
                $check_log_contact = LogContactedNewLead::where('guest_id', '=', $key)->get();
                $diff = $no_answers[$key] - count($check_log_contact);
                if ($diff > 0):
                    for ($i = 0; $i < $diff; $i++):
                        $log_contact = new LogContactedNewLead;
                        $log_contact->guest_id = $key;
                        $log_contact->staff_id = Auth::id();
                        $log_contact->status = 'NotAnswered';
                        $log_contact->save();
                    endfor;
                else:
                    if (!empty($fa_dates[$key] && $fa_hours[$key])):
                        //Create Appointment
                        $guest_appointment = new GuestAppointment;
                        $guest_appointment->guest_id = $key;
                        $guest_appointment->staff_id = Auth::id();
                        $guest_appointment->fa_status = 'FA';
                        $guest_appointment->fa_date = $fa_dates[$key];
                        $guest_appointment->fa_hour = Carbon::createFromFormat('H:i', Carbon::parse($fa_hours[$key])->format('H:i'), session('timezone'))->setTimezone('UTC');
                        $guest_appointment->guest_status_after = 'Lead';
                        $guest_appointment->club_id = Auth::user()->club_id;
                        $guest_appointment->save();

                        //Set Appointment Status = Confirmed
                        $ga_status = new GuestAppointmentStatus;
                        $ga_status->status_id = '1';
                        $ga_status->c_date = Carbon::now();
                        $guest_appointment->guestappointment_gastatus()->save($ga_status);

                        //Set log_contacted_leads => Answered
                        $log_contact = new LogContactedLead;
                        $log_contact->guest_appointment_id = $guest_appointment->id;
                        $log_contact->staff_id = Auth::id();
                        $log_contact->status_id = '1';
                        $log_contact->save();
                    endif;
                endif;
            endforeach;
        endif;

        Session::flash('flash-message', trans('messages.leads_appointment_has_been_modified'));

        return redirect('cf-lite/lead');
    }

    public function update_lead_data(Request $request) {
        $this->validate($request, [
            'lead_attribute' => 'required',
            'lead_name' => 'required',
            'lead_phone' => 'required',
            'lead_source' => 'sometimes',
            'wa_number' => 'sometimes',
            'email' => 'sometimes',
        ]);

        $lead = BuddyReferralLead::findOrFail($request->lead_attribute);
        $lead->fullname = $request->lead_name;
        $lead->phone = $request->lead_phone;

        if ($lead->is_br == 'NotBR'):
            if (!empty($request->lead_source)):
                $lead->source_id = $request->lead_source;
            endif;
        endif;

        if (!empty($request->wa_number)):
            $lead->wa_number = $request->wa_number;
        endif;

        if (!empty($request->email)):
            $lead->email = $request->email;
        endif;

        $lead->save();

        Session::flash('cf_lite.includes.flash-message', trans('messages.lead_data_has_been_updated'));
        return redirect('cf-lite/lead');
    }

    public function complete_fa_data($id) {
        $guest_appointment = GuestAppointment::findOrFail($id);
        $lead = BuddyReferralLead::findOrFail($guest_appointment->guest_id);

        $nosalereasons = NoSaleReason::pluck('nosale_reason', 'id');
        $guest_attributes = MemberAttribute::orderBy('attribute', 'asc')->pluck('attribute', 'id');

        //FA Coaches added 01032018
        $fa_coaches = User::where('club_id', '=', Auth::user()->club_id)
            ->where('role_id', '<>', '7')
            ->orderBy('name', 'asc')
            ->pluck('name', 'id');

        return view('cf_lite.leads.guest-detail', compact('guest_appointment', 'lead', 'nosalereasons', 'guest_attributes', 'fa_coaches'));
    }

    public function lead_update($id, GuestDetailRequest $request) {
        $guest_appointment = GuestAppointment::findOrFail($id);

        if (!empty($request->fa_coach)):
            if ($request->fa_coach != $guest_appointment->staff_id):
                $guest_appointment->staff_id = $request->fa_coach;
                $guest_appointment->save();
            endif;
        endif;

        //Update BuddyReferralLead
        $lead = BuddyReferralLead::findOrFail($guest_appointment->guest_id);
        $lead->fullname = $request->fullname;
        $lead->phone = $request->phone;
        $lead->save();


        //Add or Update GuestDetail
        if (!empty($lead->buddyreferrallead_guestdetail)):
            $guest_detail = $lead->buddyreferrallead_guestdetail;
        else:
            $guest_detail = new GuestDetail;
        endif;
        $guest_detail->birth = $request->birth;

        if (!empty($request->email)):
            $guest_detail->email = $request->email;
        endif;

        $guest_detail->occupation = $request->occupation;
        $guest_detail->status = $request->status;
        $guest_detail->fa_date = $guest_appointment->fa_date;
        $lead->buddyreferrallead_guestdetail()->save($guest_detail);


        //Sync Guest Attributes
        if (count($lead->lead_attribute) > 0):
            $lead->lead_attribute()->sync($request->input('guest_attribute'));
        else:
            $lead->lead_attribute()->attach($request->input('guest_attribute'));
        endif;


        //Add or Edit No Sale Reason
        if (!empty($guest_appointment->guestappointment_guestnosalereason)):
            $nosale_reason = $guest_appointment->guestappointment_guestnosalereason;
        else:
            $nosale_reason = new GuestNoSaleReason;
        endif;
        $nosale_reason->no_sale_reason_id = $request->no_sale_reason;
        $guest_appointment->guestappointment_guestnosalereason()->save($nosale_reason);


        //No Sale Guest Fat
        if(!empty($request->weight) || !empty($request->body_fat) || !empty($request->visceral)):
            $guest_fat = new GuestFat;
            $guest_fat->weight = $request->weight;
            $guest_fat->body_fat = $request->body_fat;
            $guest_fat->visceral = $request->visceral;
            $lead->buddyreferrallead_guestfat()->save($guest_fat);
        endif;

        Session::flash('flash_message', trans('messages.guest_detail_has_been_updated'));
//        return redirect('cf-lite/lead/'.$guest_appointment->id.'/complete-fa-data');
        return redirect('cf-lite/appointment/attendance');
    }

    //Lead Referrer
    public function br_referrer_list(){
        $buddyreferrals = BuddyReferralLead::where('club_id', '=', Auth::user()->club_id)
            ->where('is_br','=','BR')
            ->get();
        foreach ($buddyreferrals as $br) {
            if($br->status_gm == 'Member'){
                $br->setAttribute('br_status','Joined');
            }else if($br->status_gm == 'Guest'){
                if(count($br->buddyreferrallead_guestappointment) < 1){
                    $br->setAttribute('br_status','Failed');
                }else{
                    $br->setAttribute('br_status','Pending');
                }
            }

            if(!empty($br->buddyreferrallead_referrer)){
                $br->setAttribute('referrer_id',$br->buddyreferrallead_referrer->user_id);
            }else{
                $br->setAttribute('referrer_id','-1');
            }
        }
        $users = User::where('id','<>','1')->pluck('name','id');

        return view('cf_lite.leads.lead-br-referrer',compact('buddyreferrals','users'));
    }

    public function store_lead_referrer($id, Request $request){
        $this->validate($request,[
            'referrer' => 'required',
            'referrer_status' => 'required',
            ]);

        $buddyreferral = BuddyReferralLead::findOrFail($id);
        $userbr = User::findOrFail($request->referrer);
        if(!empty($userbr)){
            $buddyreferral->source_detail = $userbr->user_memberpersonal->key_tag_id;
            $buddyreferral->save();
        }
        $referrer = new Referrer;
        if(!empty($buddyreferral->buddyreferrallead_referrer)){
            $referrer = $buddyreferral->buddyreferrallead_referrer;
			//app('App\Http\Controllers\PointStaffController')->min_br_point($referrer->user_id,28,Carbon::today(),$buddyreferral);
        }
        $referrer->user_id = $request->referrer;
        $referrer->status = $request->referrer_status;
        $buddyreferral->buddyreferrallead_referrer()->save($referrer);

		//app('App\Http\Controllers\PointStaffController')->add_br_point($request->referrer,28,Carbon::today(),$buddyreferral);
		
        return redirect('cf-lite/lead/br-referrer');
    }

    public function import_view(){
        return view('cf_lite.leads.import-leads');
    }

    public function import_leads(Request $request){
        if ($request->hasFile('leads')){
            $path = $request->file('leads')->getRealPath();
            $data = Excel::load($path, function ($reader){/*$reader->dd();*/})->get();

            if (!empty($data) && $data->count()) {
                foreach ($data as $key => $value) {
                    //cek name and phone 'ILIKE', '%'.$request->name.'%'
                    $cek = BuddyReferralLead::where('fullname', 'ILIKE', '%'.$value->nama_lengkap.'%')
                            ->where('phone', '=', $value->telepon)->first();
                    $staff = StaffCode::where('code', '=', strtoupper($value->kode_staff))->first();
                    if (empty($cek)){
                        if (!empty($staff)){
                            //Insert to Table
                            $lead = new BuddyReferralLead();
                            $lead->fullname = $value->nama_lengkap;
                            $lead->phone = $value->telepon;
                            if ($value->sumber == 1){
                                $lead->is_br = 'BR';
                            } else {
                                $lead->is_br = 'NotBR';
                            }
                            $lead->source_id = $value->sumber;
                            $lead->staff_id = $staff->user_id;
                            $lead->club_id = Auth::user()->club_id;
                            $lead->status_gm = 'Guest';
                            $lead->save();
                        }
                    }
                }
                Session::flash('flash_message', trans('messages.success_import_leads'));
                return redirect('cf-lite/lead/import-leads');
            } else {
                Session::flash('flash_message', trans('messages.failed_import_leads'));
                return redirect('cf-lite/lead/import-leads');
            }
        } else {
            Session::flash('flash_message', trans('messages.no_data_has_been_imported'));
            return redirect('cf-lite/lead/import-leads');
        }
    }
}
