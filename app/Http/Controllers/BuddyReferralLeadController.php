<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuestDetailRequest;
use App\Model\Appointment\GuestAppointmentStatus;
use App\Model\Guest\BuddyReferralLead;
use App\Model\Guest\GuestAttribute;
use App\Model\Guest\GuestDetail;
use App\Model\MasterData\Country;
use App\Model\MasterData\MemberAttribute;
use App\Model\MasterData\MemberSource;
use App\Model\Member\EmergencyContact;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class BuddyReferralLeadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('wm-only');
    }

    public function index()
    {
        $brl_list = BuddyReferralLead::where('status_gm', 'Guest')->get();
        $count = count($brl_list);
        $sources = MemberSource::orderBy('source', 'asc')->pluck('source', 'id');
        $countries = Country::orderBy('country', 'asc')->pluck('country', 'id');

        return view('cf_back.lead.leads', compact('brl_list', 'count', 'sources', 'countries'));
    }

    public function index_filter()
    {
        return view('cf_back.lead.select_date_for_filter_lead_appointment');
    }

    public function guest_appointment_list($tanggal)
    {
        $guest_appointments = GuestAppointmentStatus::whereDate('s_date', '=', $tanggal)->whereIn('status_id', [3, 5, 6])->get();
        return view('cf_back.lead.leads_appointment', compact('guest_appointments', 'tanggal'));
    }

    public function leads_to_guest($tanggal, $id)
    {
        if (Auth::check()) {
            $staff = Auth::user();
            $staffs = User::orderBy('name')->pluck('name', 'id');
            $br = BuddyReferralLead::findOrFail($id);
            $attributes = MemberAttribute::all();

            $guest_detail = GuestDetail::find($id);
            if (!$guest_detail) {
                $guest_detail = new GuestDetail;
            }
            $emergency_contact = EmergencyContact::find($id);
            if (!$emergency_contact) {
                $emergency_contact = new EmergencyContact;
            }
            $saved_attrs = GuestAttribute::select('member_attribute_id')->where('user_id', '=', $id)->get();
            return view('cf_back.lead.lead_to_guest', compact('br', 'tanggal', 'attributes', 'staffs', 'staff', 'guest_detail', 'emergency_contact', 'saved_attrs'));
        } else {
            return redirect('login');
        }
    }

    public function store_guest_detail($the_null, $id, GuestDetailRequest $request)
    {
        $br = BuddyReferralLead::findOrFail($id);
        $br->fullname = $request->full_name;
        $br->phone = $request->phone;
        $br->save();

        $guest_detail = GuestDetail::find($id);
        if (!$guest_detail) {
            $guest_detail = new GuestDetail;
            $guest_detail->guest_id = $id;
        }
        $guest_detail->birth = $request->birthday;
        $guest_detail->email = $request->email;
        $guest_detail->occupation = $request->occupation;
        $guest_detail->status = $request->status;
        $guest_detail->fa_date = $request->fa_date;
        $guest_detail->save();

        $emergency_contact = EmergencyContact::find($id);
        if (!$emergency_contact) {
            $emergency_contact = new EmergencyContact;
            $emergency_contact->user_id = $id;
        }
        $emergency_contact->family_name = $request->emergency_name;
        $emergency_contact->relation = $request->relation;
        $emergency_contact->emergency_contact_number = $request->emergency_phone;
        $emergency_contact->save();

        $saved_attrs = GuestAttribute::where('user_id', '=', $id);
        foreach ($saved_attrs as $attr) {
            $attr->delete();
        }
        $data = Input::get('member_attributes');
        foreach ($data as $x) {
            $attr = new GuestAttribute;
            $attr->user_id = $id;
            $attr->member_attribute_id = $x;
            $attr->save();
        }

        return redirect('cf-back/lead/date/' . $the_null . '/' . $id);
    }

    public function show($id)
    {
        $brl = BuddyReferralLead::findOrFail($id);

        return view('brleads.brlead-detail', compact('brl'));
    }

    public function create_fa($id)
    {
        $member = User::findOrFail($id);
        $countries = Country::orderBy('country')->pluck('country', 'id');
        $fa = 'first-assessment';

        return view('brleads.tambah-brlead', compact('member', 'countries', 'fa'));
    }

    public function create($id)
    {
        $member = User::findOrFail($id);
        $countries = Country::orderBy('country')->pluck('country', 'id');

        return view('brleads.tambah-brlead', compact('member', 'countries'));
    }

    public function store($id, BuddyReferralLeadRequest $request)
    {
        $member = User::findOrFail($id);

        $brl = new BuddyReferralLead;
        /*$brl->first_name = $request->first_name;

        if (!empty($request->middle_name)):
            $brl->middle_name = $request->middle_name;
        endif;

        $brl->sur_name = $request->sur_name;*/

        $names = explode(' ', $request->name);
        $total_name = count($names);

        $brl->first_name = $names[0];

        if ($total_name == 1):
            $brl->sur_name = $names[0];
        elseif ($total_name == 2):
            $brl->sur_name = $names[1];
        elseif ($total_name == 3):
            $brl->middle_name = $names[1];
            $brl->sur_name = $names[2];
        endif;

        $brl->country_id = $request->country_id;
        $brl->province_id = $request->province_id;
        $brl->city_id = $request->city_id;

        if (!empty($request->postal)):
            $brl->postal = $request->postal;
        endif;

        $brl->address = $request->address;

        if (!empty($request->phone)):
            $brl->phone = $request->phone;
        endif;

        $brl->cellphone = $request->cellphone;

        if (!empty($request->email)):
            $brl->email = $request->email;
        endif;

        $brl->guest_pass = 'No';
        $brl->coupon = 'No';
        $brl->source_id = 1;
        $brl->status = 'Guest';
        $brl->save();

        $referrer = new Referrer;
        $referrer->referral_id = $brl->id;
        $referrer->user_id = $member->id;
        $referrer->status = 'Pending';
        $brl->buddyreferrallead_referrer()->save($referrer);

        Session::flash('flash_message', trans('messages.brleads_create_message'));

        if ($request->has('tambahlagi')):
            return redirect('leads/' . $member->id . '/create');
        elseif ($request->has('selesai')):
            return redirect('leads');
        elseif ($request->has('fromfa')):
            return redirect('weight-measure/' . $member->id . '/choose');
        endif;
    }

    public function store_lead_from_admin(LeadFromAdminRequest $request)
    {
        $lead = new BuddyReferralLead;

        /*$lead->first_name = $request->first_name;

        if (!empty($request->middle_name)):
            $lead->middle_name = $request->middle_name;
        endif;

        $lead->sur_name = $request->sur_name;*/

        $names = explode(' ', $request->name);
        $total_name = count($names);

        $lead->first_name = $names[0];

        if ($total_name == 1):
            $lead->sur_name = $names[0];
        elseif ($total_name == 2):
            $lead->sur_name = $names[1];
        elseif ($total_name == 3):
            $lead->middle_name = $names[1];
            $lead->sur_name = $names[2];
        endif;

        $lead->country_id = $request->country_id;
        $lead->province_id = $request->province_id;
        $lead->city_id = $request->city_id;
        $lead->postal = $request->postal;
        $lead->address = $request->address;
        $lead->phone = $request->phone;
        $lead->cellphone = $request->cellphone;
        $lead->email = $request->email;
        $lead->guest_pass = 'No';
        $lead->coupon = 'No';
        $lead->source_id = $request->source_id;
        $lead->status = 'Guest';
        $lead->save();

        if ($request->source_id == '1'):
            $brl = new Referrer;
            $brl->user_id = $request->br_id;
            $brl->status = 'Pending';
            $lead->buddyreferrallead_referrer()->save($brl);
        endif;

        Session::flash('flash_message', trans('messages.brleads_create_message'));

        return redirect('leads');
    }
}
