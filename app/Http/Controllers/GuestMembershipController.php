<?php

namespace App\Http\Controllers;

use App\Http\Requests\WMAddNewDataRequest;
use App\Model\Club\ClubInfo;
use App\Model\Guest\BuddyReferralLead;
use App\Model\GuestMembership\GuestMemberPayment;
use App\Model\GuestMembership\GuestMembership;
use App\Model\GuestMembership\GuestMembershipMember;
use App\Model\GuestMembership\GuestWM;
use App\Model\GuestMembership\GuestWO;
use App\Model\MasterData\MemberSource;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;

require_once __DIR__.'/../../WebClientPrint/WebClientPrint.php';
use Neodynamic\SDK\Web\WebClientPrint;

class GuestMembershipController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function guest_membership(){
        $gms = GuestMembership::get();
        $clubs = ClubInfo::where('status', '=', 'Active')->pluck('club_name', 'id');

        return view('cf_lite.guest-membership.guest-membership', compact('gms', 'clubs'));
    }

    public function store_guest_membership(Request $request){
        $this->validate($request, [
            'gm_name' => 'required|unique:guest_memberships,gm_name',
            'price' => 'required',
            'duration' => 'required',
            'from_date' => 'required',
            'thru_date' => 'required',
            'max_wo' => 'required',
            'clubs' => 'required',
        ]);

        $gm = new GuestMembership;
        $gm->gm_name = $request->gm_name;
        $gm->duration_type = $request->select_type;
        $gm->duration_long = $request->duration;
        $gm->max_workout = $request->max_wo;
        $gm->from_date = $request->from_date;
        $gm->thru_date = $request->thru_date;
        $gm->price = $request->price;
        $gm->save();

        $gm->gm_clubgm()->attach($request->input('clubs'));

        Session::flash('flash_message', trans('messages.successfully_added_new_guest_membership'));
        return redirect('cf-lite/guest-membership');
    }

    public function update_guest_membership(Request $request){
        $this->validate($request, [
            'edit_gm_name' => 'required|unique:guest_memberships,gm_name,'.$request->gm_id,
            'edit_price' => 'required',
            'edit_duration' => 'required',
            'edit_from_date' => 'required',
            'edit_thru_date' => 'required',
            'edit_max_wo' => 'required',
            'edit_clubs' => 'required',
        ]);

        $gm = GuestMembership::findOrFail($request->gm_id);
        if(!empty($gm)):
            $gm->gm_name = $request->edit_gm_name;
            $gm->duration_type = $request->edit_select_type;
            $gm->duration_long = $request->edit_duration;
            $gm->max_workout = $request->edit_max_wo;
            $gm->from_date = $request->edit_from_date;
            $gm->thru_date = $request->edit_thru_date;
            $gm->price = $request->edit_price;
            $gm->save();

            if($gm->save()):
                $gm->gm_clubgm()->sync($request->input('edit_clubs'));
                Session::flash('flash_message',trans('messages.guest_membership_updated'));
            else:
                Session::flash('error_message',trans('messages.failed_to_update_bank'));
            endif;
        else:
            Session::flash('error_message',trans('messages.guest_membership_not_found'));
        endif;

        return redirect('cf-lite/guest-membership');
    }

    public function delete_guest_membership($id){
        $gm = GuestMembership::findOrFail($id);
        $gm->gm_clubgm()->detach();
        try{
            $gm->delete();
            Session::flash('flash_message',trans('messages.guest_membership_has_been_deleted'));
        }catch(\Illuminate\Database\QueryException $e){
            Session::flash('error_message',trans('messages.failed_to_delete_guest_membership'));
        }

        return redirect('cf-lite/guest-membership');
    }

    public function guest_member_list(){
        $club = ClubInfo::where('id', '=', Auth::user()->club_id)->first();

//        $members = BuddyReferralLead::where('status_gm', '=', 'Guest')
//            ->join('guest_membership_members', 'guest_membership_members.guest_id', '=', 'buddy_referral_leads.id')
//            ->get();

//        $promo = GuestMembership::selectRaw('guest_membership_members.id as gmm_id, buddy_referral_leads.fullname, buddy_referral_leads.phone,
//            buddy_referral_leads.source_id, buddy_referral_leads.staff_id,
//            guest_membership_members.from_date, guest_membership_members.thru_date, guest_memberships.gm_name, guest_memberships.id as gm_id')
//            ->join('guest_membership_members', 'guest_memberships.id', '=', 'guest_membership_members.gm_id')
//            ->join('buddy_referral_leads', 'guest_membership_members.guest_id', '=', 'buddy_referral_leads.id')
//            ->get();
        $promo = GuestMembershipMember::whereHas('gmmember_guest', function ($q) {
            $q->where('club_id', '=', Auth::user()->club_id)
                ->whereDoesntHave('buddyreferrallead_memberpersonal')
                ->where('status_gm', '=', 'Guest');
        })->get();

        $gms = GuestMembership::where('thru_date', '>', Carbon::now())->get();

        $coach = User::where('club_id','=', Auth::user()->club_id)
            ->where('role_id','=',5)
            ->orWhere('role_id','=',6)
            ->pluck('name','id');

        $sources = MemberSource::orderBy('source', 'asc')->pluck('source', 'id');

        return view('cf_lite.guest-membership.guest-member-list', compact('club', 'promo', 'coach', 'sources', 'gms'));
    }

    public function update_guest_member_list(Request $request){
        $gm_member = GuestMembershipMember::findOrFail($request->gmm_id);
        $lead = BuddyReferralLead::findOrFail($gm_member->guest_id);

        //Update Buddy Referral Lead
        $lead->fullname = $request->fullname;
        $lead->phone = $request->phone;
        $lead->source_id = $request->source;

        if ($request->source == '1'):
            $lead->is_br = 'BR';
        else:
            $lead->is_br = 'NotBR';
        endif;

        $lead->staff_id = $request->coach;
        $lead->save();

        //Update Guest Membership Member
        $gm_member->gm_id = $request->promo;
        $gm_member->from_date = $request->from_date;
        $gm_member->thru_date = $request->thru_date;
        $gm_member->save();

        Session::flash('flash_message', trans('messages.guest_membership_promo_data_has_been_updated'));
        return redirect('cf-lite/guest-member/list');
    }

    public function edit_guest_membership_ajax(Request $request){
        $this->validate($request, [
            'gm_id' => 'required',
        ]);

        $gm = GuestMembership::findOrFail($request->gm_id);

        return response()->json(array('gm'=>$gm));
    }

    public function guest_member_search(Request $request)
    {
        $nama = $request->name;
        if(!empty($nama)){
            $guests = GuestMembershipMember::selectRaw('guest_membership_members.id as id, fullname, phone')->join('buddy_referral_leads','guest_membership_members.guest_id','=','buddy_referral_leads.id')->whereHas('gmmember_guest',function($q) use ($nama){
                $q->where('fullname','ILIKE','%'.$nama.'%');
                $q->where('club_id','=',Auth::user()->club_id);
                $q->where('status_gm','=','Guest');
            })->get();
        }else{
            //Dummy Empty
            $guests = collect();
        }

        return view('cf_lite.guest-membership.guest-member-wo-search', compact('guests'));
    }

    public function past_gm_wo($id){
        $gmm = GuestMembershipMember::findOrFail($id);
        $date = Carbon::now();

        $userWo = GuestWO::where('guest_membership_member_id','=',$id)->get();

        return view('cf_lite.guest-membership.guest-member-manual-wo', compact('gmm','date','userWo'));
    }

    public function store_guest_member_workout($id, Request $request){
        // Add to Workout
        $dates = $request->date_chosen;
        $selDate = explode(" ", $dates);

        $gmm = GuestMembershipMember::findOrFail($request->id);
        foreach($selDate as $date){
            $the_status = 0;
            foreach ($gmm->gmmember_guestwo as $workout):
                if (Carbon::parse($workout->wo_date)->format('d-m-Y') == Carbon::parse($date)->format('d-m-Y')):
                    $the_status = 1;
                endif;
            endforeach;

            if ($the_status == 0):
                $wo = new GuestWO;
                $wo->guest_id = $gmm->guest_id;
                $wo->wo_date = Carbon::parse($date);
                $wo->club_id = Auth::user()->club_id;
                $wo->guest_membership_member_id = $gmm->id;
                $wo->save();
            endif;
        }

        //Delete from Workout
        $deldates = $request->date_chosen_delete;
        $selDelDate = explode(" ", $deldates);

        foreach($selDelDate as $date){
            $the_status = 0;
            foreach ($gmm->gmmember_guestwo as $workout):
                if (Carbon::parse($workout->wo_date)->format('d-m-Y') == Carbon::parse($date)->format('d-m-Y')):
                    $the_status = $workout->id;
                endif;
            endforeach;

            if ($the_status != 0):
                $wo = GuestWO::findOrFail($the_status);
                $wo->delete();
            endif;
        }

        return redirect('cf-lite/guest-member/list');
    }

    public function wm_guest_list(){
        $club = Auth::user()->club_id;

        $members = GuestMembershipMember::selectRaw('guest_membership_members.guest_id as guest_id, buddy_referral_leads.fullname, buddy_referral_leads.phone, max(guest_wms.wm_date) as wm_date')
            ->where('club_id', '=', $club)
            ->join('buddy_referral_leads', 'guest_membership_members.guest_id', '=', 'buddy_referral_leads.id')
            ->join('guest_wms', 'guest_wms.guest_id', '=', 'buddy_referral_leads.id')
            ->groupBy('guest_membership_members.guest_id', 'buddy_referral_leads.fullname', 'buddy_referral_leads.phone')
            ->get();

        return view('cf_lite.guest-membership.wm-guest-list', compact('members', 'mas'));
    }

    public function wm_guest_history($id){
        $member = BuddyReferralLead::selectRaw('fullname, guest_membership_members.guest_id, guest_membership_members.from_date')
            ->join('guest_membership_members', 'guest_membership_members.guest_id', '=', 'buddy_referral_leads.id')
            ->where('buddy_referral_leads.id', '=', $id)
            ->orderBy('guest_membership_members.from_date', 'desc')
            ->first();

        $wm_list = GuestWM::select('id', 'wm_date')
            ->where('guest_id', '=', $id)
            ->orderBy('wm_date', 'desc')
            ->get();

        $last_wm = GuestWM::selectRaw('guest_id, max(wm_date) as wm_date')
            ->where('guest_id', '=', $id)
            ->groupBy('guest_id')
            ->first();

        return view('cf_lite.guest-membership.wm-guest-history', compact('member', 'wm_list', 'last_wm'));
    }

    public function wm_guest_detail($id) {
        $gwm = GuestWM::findOrFail($id);
        $gmm = GuestMembershipMember::where('guest_id', '=', $gwm->guest_id)->first();

        //count total WM
        $count_wm = GuestWM::where('guest_id', '=', $gwm->guest_id)->get()->count();

        if ($count_wm > 1):
            $prev_wm = GuestWM::where('guest_id', '=', $gwm->guest_id)
                ->whereDate('wm_date', '<', $gwm->wm_date)
                ->orderBy('wm_date', 'desc')
                ->first();
        else:
            $prev_wm = GuestWM::where('guest_id', '=', $gwm->guest_id)
                ->whereDate('wm_date', '<=', $gwm->wm_date)
                ->orderBy('wm_date', 'desc')
                ->first();
        endif;

        $last_wm = GuestWM::selectRaw('guest_id, max(wm_date) as wm_date')
            ->where('guest_id', '=', $id)
            ->groupBy('guest_id')
            ->first();

        $measurers = User::where('club_id', '=', Auth::user()->club_id)
            ->where('role_id', '<>', '7')
            ->orderBy('name', 'asc')
            ->pluck('name', 'id');

        return view('cf_lite.guest-membership.wm-guest-detail', compact('gwm', 'gmm', 'prev_wm', 'last_wm', 'measurers'));
    }

    public function update_wm_guest_data($id, WMAddNewDataRequest $request) {
        $wm = GuestWM::findOrFail($id);

        $wm->wm_date = $request->wm_date;
        $wm->age = $request->age;
        $wm->height = $request->height;
        $wm->bp_high = $request->bp_high;
        $wm->bp_low = $request->bp_low;
        $wm->rhr = $request->rhr;
        $wm->chest = $request->chest;
        $wm->waist = $request->waist;
        $wm->abdomen = $request->abdomen;
        $wm->hip = $request->hip;
        $wm->thigh = $request->thigh;
        $wm->arm = $request->arm;
        $wm->weight = $request->weight;
        $wm->body_fat = $request->body_fat;
        $wm->muscle_mass = $request->muscle_mass;
        $wm->bone_mass = $request->bone_mass;
        $wm->bmi = $request->bmi;
        $wm->dci = $request->dci;
        $wm->metabolic = $request->metabolic;
        $wm->body_water = $request->body_water;
        $wm->visceral = $request->visceral;
        $wm->total_measurement = $request->chest + $request->waist + $request->abdomen + $request->hip + (2 * $request->thigh) + (2 * $request->arm);
        $wm->total_body_fat = $request->body_fat;
        $wm->total_hydration = $request->body_water;
        $wm->recommendation = $request->recommendation;

        if (!empty($request->measured_by)):
            $wm->measured_by = $request->measured_by;
        else:
            if (!empty(session('staff_session'))):
                $wm->measured_by = session('staff_session');
            else:
                $wm->measured_by = Auth::id();
            endif;
        endif;

        if (!empty($request->pro_workout)):
            $wm->pro_workout = 'Yes';
        else:
            $wm->pro_workout = 'No';
        endif;

        $wm->save();

        Session::flash('flash_message', trans('messages.weigh_n_measure_has_been_updated'));
        return redirect('cf-lite/guest-member/guest-weight-measure/'.$wm->id.'/detail');
    }

    public function delete_wm($id){
        $wm = GuestWM::findOrFail($id);
        $wm->delete();

        return redirect('cf-lite/guest-weight-measure/'.$wm->guest_id.'/history');
    }

    public function wm_guest_printout($id) {
        $gwm = GuestWM::findOrFail($id);

        //count total WM
        $count_wm = GuestWM::where('guest_id', '=', $gwm->guest_id)->get()->count();

        if ($count_wm > 1):
            $prev_wm = GuestWM::where('guest_id', '=', $gwm->guest_id)
                ->whereDate('wm_date', '<', $gwm->wm_date)
                ->orderBy('wm_date', 'desc')
                ->first();
        else:
            $prev_wm = GuestWM::where('guest_id', '=', $gwm->guest_id)
                ->whereDate('wm_date', '<=', $gwm->wm_date)
                ->orderBy('wm_date', 'desc')
                ->first();
        endif;

        $first_wm = GuestWM::where('guest_id', '=', $gwm->guest_id)
            ->orderBy('wm_date', 'asc')
            ->first();

        $workouts = GuestWO::select('wo_date')->where('guest_id', '=', $gwm->guest_id)->get();

        return view('cf_lite.guest-membership.wm-guest-printout', compact('gwm', 'prev_wm', 'workouts', 'first_wm'));
    }

    public function receipt($id){
        $gmp = GuestMemberPayment::where('guest_membership_member_id', '=', $id)->first();

        $staffs = User::where('club_id', '=', Auth::user()->club_id)
            ->where('role_id', '<>', '7')
            ->where(function ($q) {
                $q->whereDoesntHave('user_staffinactive')
                    ->orWhere(function ($r) {
                        $r->whereHas('user_staffinactive', function ($s) {
                            $s->where('status', '=', 'Reactivated')
                                ->where('thru_date', null);
                        });
                    });
            })
            ->orderBy('name', 'asc')
            ->pluck('name', 'id');

        $wcpScript = WebClientPrint::createScript(action('WebClientPrintController@processRequest'), action('PrintESCPOSController@guestMembershipPayment'), Session::getId());

        return view('cf_lite.guest-membership.receipt', compact('gmp', 'staffs', 'wcpScript'));
    }
}
