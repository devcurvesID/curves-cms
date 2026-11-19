<?php

namespace App\Http\Controllers;

use App\Http\Requests\WMAddNewDataRequest;
use App\Model\Appointment\MemberAppointment;
use App\Model\GuestMembership\GuestMembershipMember;
use App\Model\GuestMembership\GuestWM;
use App\Model\GuestMembership\GuestWO;
use App\Model\MasterData\WeighMeasureAttribute;
use App\Model\WM\WeighMeasure;
use App\Model\WM\Xfactor;
use App\Model\Workout\Workout;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;

class WMLiteController extends Controller
{
    public function __construct()
    {
        $this->middleware('cf-wm', ['except' => [
            'login',
            'login_authentication',
        ]]);
    }

    public function login() {
        return view('cf_wm.login-page');
    }

    public function login_authentication(Request $request) {
        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            session(['wm-only' => '1', 'timezone' => $request->timezone]);

            return redirect('cf-wm/wm-member-scan');
        }

        return redirect('cf-wm/login');
    }

    public function logout(Request $request) {
        $this->guard()->logout();

        $request->session()->invalidate();

        return redirect('cf-wm');
    }

    public function wm_member_scan(){
        return view('cf_wm.wm-member-scan');
    }

    public function wm_main(Request $request) {
        $club = Auth::user()->club_id;

        $members = User::where('club_id', '=', $club)
            ->whereHas('user_memberpersonal', function ($q) use($request) {
                $q->where('key_tag_id', 'LIKE', '%'. $request->keytag .'%');
                })
            ->orderBy('created_at', 'desc')
            ->paginate(100);

        if(!count($members)){
            Session::flash('cf_lite.includes.flash-message', trans('messages.data_not_found'));
            return view('cf_wm.wm-member-scan');
        }

        $id = $members->first()->id;

        return redirect('cf-wm/'.$id.'/add');
    }

    public function add_wm($id) {
        $member = User::findOrFail($id);
        $wm_attributes = WeighMeasureAttribute::orderBy('attribute_name', 'asc')->pluck('attribute_name', 'id');
        $total_wo_monthly = Workout::where('user_id', '=', $member->id)
            ->whereDate('workout_date', '<=', Carbon::now()->toDateString())
            ->whereDate('workout_date', '>=', Carbon::now()->subDays(30)->toDateString())
            ->get()
            ->count();

        $measurers = User::where('club_id', '=', Auth::user()->club_id)
            ->where('role_id', '<>', '7')
            ->orderBy('name', 'asc')
            ->pluck('name', 'id');

        //Last WM Added 02062018
        $last_wm = WeighMeasure::where('user_id', '=', $member->id)
            ->orderBy('wm_date', 'desc')
            ->first();

        return view('cf_wm.add-wm-data', compact('member', 'wm_attributes', 'total_wo_monthly', 'measurers', 'last_wm'));
    }

    public function store_add_wm($id, WMAddNewDataRequest $request) {
        $member = User::findOrFail($id);

        $wm = new WeighMeasure;
        $wm->user_id = $member->id;
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
        $wm->three_times_a_week = $request->three_times_a_week;
        if (!empty($request->total_wo_per_month)):
            $wm->total_wo_per_month = $request->total_wo_per_month;
        else:
            $wm->total_wo_per_month = 0;
        endif;
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

        //Pasang atribut rekomendasi
        $wm->weighmeasure_weighmeasureattribute()->attach($request->input('wm_attribute'));

        //Set Tanggal WM terakhir
        $member_status = $member->user_memberstatus;
        $member_status->last_wm = $request->wm_date;
        $member->user_memberstatus()->save($member_status);

        //Atur WM selanjutnya
        if (!empty($request->set_next_wm)):
            $wm_appointment = new MemberAppointment;
            $wm_appointment->user_id = $member->id;
            $wm_appointment->staff_id = Auth::id();
            $wm_appointment->ap_type_id = '1';
            $wm_appointment->app_date = $request->next_wm_date;
            $wm_appointment->app_hour = $request->next_wm_hour;
            $wm_appointment->save();
        endif;

        return redirect('cf-wm/'.$wm->id.'/printout');
    }

    public function wm_printout($id) {
        $wm = WeighMeasure::findOrFail($id);

        //count total WM
        $count_wm = WeighMeasure::where('user_id', '=', $wm->user_id)->get()->count();

        if ($count_wm > 1):
            $prev_wm = WeighMeasure::where('user_id', '=', $wm->user_id)
                ->whereDate('wm_date', '<', $wm->wm_date)
                ->orderBy('wm_date', 'desc')
                ->first();
        else:
            $prev_wm = WeighMeasure::where('user_id', '=', $wm->user_id)
                ->whereDate('wm_date', '<=', $wm->wm_date)
                ->orderBy('wm_date', 'desc')
                ->first();
        endif;

        $first_wm = WeighMeasure::where('user_id', '=', $wm->user_id)
            ->orderBy('wm_date', 'asc')
            ->first();

        $wm_attributes = WeighMeasureAttribute::orderBy('attribute_name', 'asc')->get();

        $workouts = Workout::select('workout_date')->where('user_id', '=', $wm->user_id)->get();

        $xfactors = Xfactor::select('xfactor')->where('user_id', '=', $wm->user_id)->get();

        return view('cf_wm.wm-printout', compact('wm', 'prev_wm', 'wm_attributes', 'workouts', 'xfactors', 'first_wm'));
    }

    public function new_guest_membership(Request $request){
        $gmm = GuestMembershipMember::get();

        if (!empty($request->guest)):
            $guest = GuestMembershipMember::findOrFail($request->guest);

            $last_wm = GuestWM::where('guest_id', '=', $guest->id)
                ->orderBy('wm_date', 'desc')
                ->first();

            $total_wo_monthly = Workout::where('user_id', '=', $guest->id)
                ->whereDate('workout_date', '<=', Carbon::now()->toDateString())
                ->whereDate('workout_date', '>=', Carbon::now()->subDays(30)->toDateString())
                ->get()
                ->count();
        endif;

        $measurers = User::where('club_id', '=', Auth::user()->club_id)
            ->where('role_id', '<>', '7')
            ->orderBy('name', 'asc')
            ->pluck('name', 'id');

        return view('cf_wm.add-guest-wm-data', compact('gmm', 'guest', 'measurers', 'last_wm', 'total_wo_monthly'));
    }

    public function store_guest_add_wm($id, WMAddNewDataRequest $request){
        $member = GuestMembershipMember::findOrFail($id);

        $wm = new GuestWM;
        $wm->guest_id = $member->guest_id;
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
        $wm->three_times_a_week = $request->three_times_a_week;
        if (!empty($request->total_wo_per_month)):
            $wm->total_wo_per_month = $request->total_wo_per_month;
        else:
            $wm->total_wo_per_month = 0;
        endif;

        $wm->total_measurement = $request->chest + $request->waist + $request->abdomen + $request->hip + (2 * $request->thigh) + (2 * $request->arm);
        $wm->total_body_fat = $request->body_fat;
        $wm->total_hydration = $request->body_water;

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
        $wm->recommendation = $request->recommendation;
        $wm->save();

        return redirect('cf-wm/guest-membership/'.$wm->id.'/printout');
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

        $wm_attributes = WeighMeasureAttribute::orderBy('attribute_name', 'asc')->get();

        $workouts = GuestWO::select('wo_date')->where('guest_id', '=', $gwm->guest_id)->get();

        return view('cf_wm.wm-guest-printout', compact('gwm', 'prev_wm', 'wm_attributes', 'workouts', 'first_wm'));
    }

    protected function guard()
    {
        return Auth::guard();
    }
}
