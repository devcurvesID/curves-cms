<?php

namespace App\Http\Controllers;

use App\Http\Requests\WMScheduleRequest;
use Session;
use App\Http\Requests\WMAddNewDataRequest;
use App\Http\Requests\WMGoalAddNewDataRequest;
use App\Model\Appointment\MemberAppointment;
use App\Model\MasterData\WeighMeasureAttribute;
use App\Model\WM\WeighMeasure;
use App\Model\WM\WeighMeasureGoal;
use App\Model\WM\WMRank;
use App\Model\WM\Xfactor;
use App\Model\Workout\Workout;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\ApiClientService;

class WeighAndMeasureController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('wm-only');
    }

    public function wm_member_list(Request $request) {
        $club = Auth::user()->club_id;
        $members = User::selectRaw('users.id, member_personals.photo, users.name, min(workout_date) as wo_date, EXTRACT(DAY FROM min(workout_date)) as wo_day, member_personals.phone, member_personals.cellphone, member_status.last_wm, member_status.wm_schedule')
            ->where('users.club_id', '=', $club)
            ->where(function ($q) {
                $q->where('member_status.status', '=', 'Active')
                    ->orWhere('member_status.status', '=', 'Hold');
            });

        $members = $members->leftJoin('workouts', function ($join) {
            $join->selectRaw('name, min(workout_date) as fwo');
            $join->on('users.id', '=', 'workouts.user_id');
        });

        $members = $members->join('member_personals', function($join){
            $join->on('users.id','=','member_personals.user_id');
        });

        $members = $members->join('member_status', function($join){
            $join->on('users.id','=','member_status.user_id');
        });

        $members = $members->groupBy('users.id','users.name','member_personals.photo','member_personals.phone','member_personals.cellphone','member_status.last_wm','member_status.wm_schedule','users.created_at')->orderBy('users.created_at', 'desc')->get();

        //WM Appointment List Last Month - Next 3 Months
        $mas = MemberAppointment::where('ap_type_id', '=', '1')
            ->whereDate('app_date', '>', Carbon::today()->subMonth())
            ->whereDate('app_date', '<=', Carbon::today()->addMonths(3))
            ->whereHas('memberappointment_user', function ($q) {
                $q->where('club_id', '=', Auth::user()->club_id)
                    ->whereHas('user_memberstatus', function ($r) {
                        $r->where('status', '=', 'Active')
                            ->orWhere('status', '=', 'Hold');
                    });
            })
            ->get();

        return view('cf_lite.wm.wm-member-list-v2', compact('members', 'mas'));
    }

    public function add_new_wm($id) {
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

        return view('cf_lite.wm.add-wm-data', compact('member', 'wm_attributes', 'total_wo_monthly', 'measurers', 'last_wm'));
    }

    public function store_new_wm_data($id, WMAddNewDataRequest $request) {
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
         // Jonviter DEV
        // $user_by_id = DB::table('usermobilecurves_v')->where('id', $wm->measured_by)->first();
        // if ($user_by_id) {
        //     $apiClientOne = new ApiClientService();
        //     $fetch_usercurves = $apiClientOne->post("/mobile-curves/user", (array) $user_by_id);
        // }
        // Jonviter DEV
        
//        $wm->measured_by = Auth::id();

        if (!empty($request->pro_workout)):
            $wm->pro_workout = 'Yes';
        else:
            $wm->pro_workout = 'No';
        endif;

        $wm->save();
        // Jonviter DEV

        $wmData = $wm->toArray();
        $apiClient = new ApiClientService();
        $mobile_curves_api = $apiClient->post("/mobile-curves/input-weigh-measure", $wmData);

        // Jonviter DEV
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

        // Add To WMRank Added 22/08/2019
        $wmrank_available_m = WMRank::where('user_id','=',$member->id)->where('type','=','monthly')->first();
        $wmrank_available_o = WMRank::where('user_id','=',$member->id)->where('type','=','overall')->first();

        // Check if there're another newer WM in the database
        $check_wm = WeighMeasure::where('user_id','=',$member->id)->where('wm_date','>',$request->wm_date)->first();

        if(empty($check_wm)){   // Check if there're another WM that has newer date than the one inserted
            $start_prev = new Carbon('first day of last month');
            $last_prev = new Carbon('last day of last month');

            $prev_wm = WeighMeasure::where('user_id','=',$member->id)->where('id','<>',$wm->id)->orderBy('wm_date','DESC')->first();

            $first_wm = WeighMeasure::where('user_id','=',$member->id)->where('id','<>',$wm->id)->orderBy('wm_date','ASC')->first();

            if(empty($prev_wm)){    // Check if previous wm exist, if empty create new WMRank with default value
                $newwmrank = new WMRank();
                $newwmrank->user_id = $member->id;
                $newwmrank->wm_one = 0;
                $newwmrank->wm_two = $wm->id;
                $newwmrank->body_fat_diff = 0;
                $newwmrank->weigh_diff = 0;
                $newwmrank->size_diff = 0;
                $newwmrank->day_diff = 0;
                $newwmrank->wo_count = 0;
                $newwmrank->type = 'monthly';
                $newwmrank->save();
            }else{  // So if previous wm exist
                // Count the workouts
                $workoutcount = Workout::where('user_id','=',$member->id)->where('workout_date','>=',Carbon::parse($prev_wm->wm_date))->where('workout_date','<=',Carbon::parse($wm->wm_date))->count();
                if(empty($wmrank_available_m)){ // Check if WMRank for this user and monthly type exist if it does not then create, if it does then update it
                    $newwmrank = new WMRank();
                    $newwmrank->user_id = $member->id;
                    $newwmrank->wm_one = $prev_wm->id;
                    $newwmrank->wm_two = $wm->id;
                    $newwmrank->body_fat_diff = $wm->body_fat - $prev_wm->body_fat;
                    $newwmrank->weigh_diff = $wm->weight - $prev_wm->weight;
                    $newwmrank->size_diff = $wm->total_measurement - $prev_wm->total_measurement;
                    $newwmrank->day_diff = Carbon::parse($wm->wm_date)->diffInDays(Carbon::parse($prev_wm->wm_date));
                    $newwmrank->wo_count = $workoutcount;
                    $newwmrank->type = 'monthly';
                    $newwmrank->save();
                }else{
                    $wmrank_available_m->user_id = $member->id;
                    $wmrank_available_m->wm_one = $prev_wm->id;
                    $wmrank_available_m->wm_two = $wm->id;
                    $wmrank_available_m->body_fat_diff = $wm->body_fat - $prev_wm->body_fat;
                    $wmrank_available_m->weigh_diff = $wm->weight - $prev_wm->weight;
                    $wmrank_available_m->size_diff = $wm->total_measurement - $prev_wm->total_measurement;
                    $wmrank_available_m->day_diff = Carbon::parse($wm->wm_date)->diffInDays(Carbon::parse($prev_wm->wm_date));
                    $wmrank_available_m->wo_count = $workoutcount;
                    $wmrank_available_m->type = 'monthly';
                    $wmrank_available_m->save();
                }
            }

            if(empty($first_wm)){   // Similiar to previous but for the first wm not previous check if the person has ever wm before she never, then create one with default value
                $newwmranko = new WMRank();
                $newwmranko->user_id = $member->id;
                $newwmranko->wm_one = 0;
                $newwmranko->wm_two = $wm->id;
                $newwmranko->body_fat_diff = 0;
                $newwmranko->weigh_diff = 0;
                $newwmranko->size_diff = 0;
                $newwmranko->day_diff = 0;
                $newwmranko->wo_count = 0;
                $newwmranko->type = 'overall';
                $newwmranko->save();
            }else{  // So the user has first wm
                // Count the workouts
                $workoutcount = Workout::where('user_id','=',$member->id)->where('workout_date','>=',Carbon::parse($first_wm->wm_date))->where('workout_date','<=',Carbon::parse($wm->wm_date))->count();
                if(empty($wmrank_available_o)){ // Check if user has wmrank before for overall type if it doesn't then create, if it does then update
                    $newwmranko = new WMRank();
                    $newwmranko->user_id = $member->id;
                    $newwmranko->wm_one = $first_wm->id;
                    $newwmranko->wm_two = $wm->id;
                    $newwmranko->body_fat_diff = $wm->body_fat - $first_wm->body_fat;
                    $newwmranko->weigh_diff = $wm->weight - $first_wm->weight;
                    $newwmranko->size_diff = $wm->total_measurement - $first_wm->total_measurement;
                    $newwmranko->day_diff = Carbon::parse($wm->wm_date)->diffInDays(Carbon::parse($first_wm->wm_date));
                    $newwmranko->wo_count = $workoutcount;
                    $newwmranko->type = 'overall';
                    $newwmranko->save();
                }else{
                    $wmrank_available_o->user_id = $member->id;
                    $wmrank_available_o->wm_one = $first_wm->id;
                    $wmrank_available_o->wm_two = $wm->id;
                    $wmrank_available_o->body_fat_diff = $wm->body_fat - $first_wm->body_fat;
                    $wmrank_available_o->weigh_diff = $wm->weight - $first_wm->weight;
                    $wmrank_available_o->size_diff = $wm->total_measurement - $first_wm->total_measurement;
                    $wmrank_available_o->day_diff = Carbon::parse($wm->wm_date)->diffInDays(Carbon::parse($first_wm->wm_date));
                    $wmrank_available_o->wo_count = $workoutcount;
                    $wmrank_available_o->type = 'overall';
                    $wmrank_available_o->save();
                }
            }
        }

        return redirect('cf-lite/weigh-measure/'.$wm->id.'/printout');
    }

    public function generate_wmrank_seeder(){
        $users = User::get();

        foreach ($users as $u) {
            $newestWM = WeighMeasure::where('user_id','=',$u->id)->orderBy('wm_date','DESC')->first();
            $previousWM = WeighMeasure::where('user_id','=',$u->id);
            $firstWM = WeighMeasure::where('user_id','=',$u->id);
            if(!empty($newestWM)){
                $previousWM = $previousWM->where('id','<>',$newestWM->id);
                $firstWM = $firstWM->where('id','<>',$newestWM->id);
            }
            $previousWM = $previousWM->orderBy('wm_date','DESC')->first();
            $firstWM = $firstWM->orderBy('wm_date','ASC')->first();

            $wmrank_available_m = WMRank::where('user_id','=',$u->id)->where('type','=','monthly')->first();
            $wmrank_available_o = WMRank::where('user_id','=',$u->id)->where('type','=','overall')->first();

            if(empty($wmrank_available_m)){

                if(empty($newestWM)){
                    $newwmrank = new WMRank();
                    $newwmrank->user_id = $u->id;
                    $newwmrank->wm_one = 0;
                    $newwmrank->wm_two = 0;
                    $newwmrank->body_fat_diff = 0;
                    $newwmrank->weigh_diff = 0;
                    $newwmrank->size_diff = 0;
                    $newwmrank->day_diff = 0;
                    $newwmrank->wo_count = 0;
                    $newwmrank->type = 'monthly';
                    $newwmrank->save();
                }else {
                    if(empty($previousWM)){    // Check if previous wm exist, if empty create new WMRank with default value
                        $newwmrank = new WMRank();
                        $newwmrank->user_id = $u->id;
                        $newwmrank->wm_one = 0;
                        $newwmrank->wm_two = $newestWM->id;
                        $newwmrank->body_fat_diff = 0;
                        $newwmrank->weigh_diff = 0;
                        $newwmrank->size_diff = 0;
                        $newwmrank->day_diff = 0;
                        $newwmrank->wo_count = 0;
                        $newwmrank->type = 'monthly';
                        $newwmrank->save();
                    }else{  // So if previous wm exist
                        // Count the workouts
                        $workoutcount = Workout::where('user_id','=',$u->id)->where('workout_date','>=',Carbon::parse($previousWM->wm_date))->where('workout_date','<=',Carbon::parse($newestWM->wm_date))->count();
                        $newwmrank = new WMRank();
                        $newwmrank->user_id = $u->id;
                        $newwmrank->wm_one = $previousWM->id;
                        $newwmrank->wm_two = $newestWM->id;
                        $newwmrank->body_fat_diff = $newestWM->body_fat - $previousWM->body_fat;
                        $newwmrank->weigh_diff = $newestWM->weight - $previousWM->weight;
                        $newwmrank->size_diff = $newestWM->total_measurement - $previousWM->total_measurement;
                        $newwmrank->day_diff = Carbon::parse($newestWM->wm_date)->diffInDays(Carbon::parse($previousWM->wm_date));
                        $newwmrank->wo_count = $workoutcount;
                        $newwmrank->type = 'monthly';
                        $newwmrank->save();
                    }
                }
            }

            if(empty($wmrank_available_o)){
                if(empty($newestWM)){
                    $newwmranko = new WMRank();
                    $newwmranko->user_id = $u->id;
                    $newwmranko->wm_one = 0;
                    $newwmranko->wm_two = 0;
                    $newwmranko->body_fat_diff = 0;
                    $newwmranko->weigh_diff = 0;
                    $newwmranko->size_diff = 0;
                    $newwmranko->day_diff = 0;
                    $newwmranko->wo_count = 0;
                    $newwmranko->type = 'overall';
                    $newwmranko->save();
                }else {
                    if(empty($firstWM)){    // Check if previous wm exist, if empty create new WMRank with default value
                        $newwmranko = new WMRank();
                        $newwmranko->user_id = $u->id;
                        $newwmranko->wm_one = 0;
                        $newwmranko->wm_two = $newestWM->id;
                        $newwmranko->body_fat_diff = 0;
                        $newwmranko->weigh_diff = 0;
                        $newwmranko->size_diff = 0;
                        $newwmranko->day_diff = 0;
                        $newwmranko->wo_count = 0;
                        $newwmranko->type = 'overall';
                        $newwmranko->save();
                    }else{  // So if previous wm exist
                        // Count the workouts
                        $workoutcount = Workout::where('user_id','=',$u->id)->where('workout_date','>=',Carbon::parse($firstWM->wm_date))->where('workout_date','<=',Carbon::parse($newestWM->wm_date))->count();
                        $newwmranko = new WMRank();
                        $newwmranko->user_id = $u->id;
                        $newwmranko->wm_one = $firstWM->id;
                        $newwmranko->wm_two = $newestWM->id;
                        $newwmranko->body_fat_diff = $newestWM->body_fat - $firstWM->body_fat;
                        $newwmranko->weigh_diff = $newestWM->weight - $firstWM->weight;
                        $newwmranko->size_diff = $newestWM->total_measurement - $firstWM->total_measurement;
                        $newwmranko->day_diff = Carbon::parse($newestWM->wm_date)->diffInDays(Carbon::parse($firstWM->wm_date));
                        $newwmranko->wo_count = $workoutcount;
                        $newwmranko->type = 'overall';
                        $newwmranko->save();
                    }
                }
            }
        }
    }

    public function wm_detail_list($id) {
        $member = User::findOrFail($id);

        $wm_list = WeighMeasure::select('id', 'wm_date')->where('user_id', '=', $id)->orderBy('wm_date', 'desc')->get();

        return view('cf_lite.wm.wm-detail-list', compact('member', 'wm_list'));
    }

    public function wm_detail($id) {
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

        $wm_attributes = WeighMeasureAttribute::orderBy('attribute_name', 'asc')->get();
        $measurers = User::where('club_id', '=', Auth::user()->club_id)
            ->where('role_id', '<>', '7')
            ->orderBy('name', 'asc')
            ->pluck('name', 'id');

//        return view('cf_lite.wm.wm-detail', compact('wm', 'prev_wm', 'wm_attributes'));
        return view('cf_lite.wm.wm-detail-v2', compact('wm', 'prev_wm', 'wm_attributes', 'measurers'));
    }

    public function update_wm_data($id, WMAddNewDataRequest $request) {
        $wm = WeighMeasure::findOrFail($id);

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

        //Pasang atribut rekomendasi
        $wm->weighmeasure_weighmeasureattribute()->sync($request->input('wm_attribute'));

        $user = User::findOrFail($wm->user_id);

        $this->sync_wmrank_data($user);

        Session::flash('flash_message', trans('messages.weigh_n_measure_has_been_updated'));
        return redirect('cf-lite/weigh-measure/'.$wm->id.'/detail');
    }

    public function delete_wm($id){
        $wm = WeighMeasure::findOrFail($id);
        $user = User::findOrFail($wm->user_id);
        $wm->delete();

        $this->sync_wmrank_data($user);

        return redirect('cf-lite/weigh-measure/'.$wm->user_id.'/history');
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

        //$workouts = Workout::select('workout_date')->where('user_id', '=', $wm->user_id)->get();
        $workouts = Workout::select('workout_date')->where('user_id', '=', $wm->user_id)->whereDate('workout_date','<=',$wm->wm_date)->get();

        $xfactors = Xfactor::select('xfactor')->where('user_id', '=', $wm->user_id)->get();

        return view('cf_lite.wm.wm-printout', compact('wm', 'prev_wm', 'wm_attributes', 'workouts', 'xfactors', 'first_wm'));
    }

    //WM goal
    public function add_new_wm_goal($id) {
        $member = User::findOrFail($id);

        $wm_goals = WeighMeasureGoal::where('user_id', '=', $member->id)->get()->count();
        if ($wm_goals > 0):
            return redirect('cf-lite/weigh-measure/'.$member->id.'/edit/goal');
        else:
            return view('cf_lite.wm.add-wm-goal-data', compact('member'));
        endif;
    }

    public function store_new_wm_goal_data($id, WMGoalAddNewDataRequest $request) {
        $member = User::findOrFail($id);

        $wm = new WeighMeasureGoal;
        $wm->user_id = $member->id;
        $wm->set_goal_date = $request->set_goal_date;
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
        $wm->save();

        //Xfactor
        foreach ($request->xfactor as $member_xfactor):
            $xfactor = new Xfactor;
            $xfactor->user_id = $member->id;
            $xfactor->xfactor = $member_xfactor;
            $xfactor->save();
        endforeach;

        return redirect('cf-lite/weigh-measure/'.$wm->id.'/detail/goal');
    }

    public function wm_goal_detail_list($id) {
        $member = User::findOrFail($id);

        $wm_list = WeighMeasureGoal::select('id', 'set_goal_date')->where('user_id', '=', $id)->orderBy('set_goal_date', 'desc')->get();

        return view('cf_lite.wm.wm-goal-detail-list', compact('member', 'wm_list'));
    }

    public function wm_goal_detail($id) {
        $wm = WeighMeasureGoal::findOrFail($id);

        return view('cf_lite.wm.wm-goal-detail', compact('wm'));
    }

    public function edit_wm_goal($id) {
        $member = User::findOrFail($id);

        $wm_goal = WeighMeasureGoal::where('user_id', '=', $member->id)->orderBy('set_goal_date', 'desc')->first();

        return view('cf_lite.wm.edit-wm-goal-data', compact('member', 'wm_goal'));
    }

    public function update_wm_goal_data($id, WMGoalAddNewDataRequest $request) {
        $member = User::findOrFail($id);

        $wm = new WeighMeasureGoal;
        $wm->user_id = $member->id;
        $wm->set_goal_date = $request->set_goal_date;
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
        $wm->save();

        //Xfactor
        if (!empty($request->edit_xfactor)):
            $xfactors = Xfactor::where('user_id', '=', $member->id)->get();
            foreach ($xfactors as $xf):
                $xf->delete();
            endforeach;

            foreach ($request->xfactor as $member_xfactor):
                $xfactor = new Xfactor;
                $xfactor->user_id = $member->id;
                $xfactor->xfactor = $member_xfactor;
                $xfactor->save();
            endforeach;
        endif;

        return redirect('cf-lite/weigh-measure/'.$wm->id.'/detail/goal');
    }

    //WM Schedule added 23032018
    public function wm_schedule($id) {
        $member = User::findOrFail($id);

        $wm_appts = MemberAppointment::where('user_id', '=', $member->id)
            ->where('ap_type_id', '=', '1')
            ->orderBy('app_date', 'asc')
            ->get();

        $workouts = Workout::where('user_id', '=', $member->id)->orderBy('workout_date', 'asc')->first();

        return view('cf_lite.wm.wm-schedule', compact('member', 'wm_appts', 'workouts'));
    }

    public function store_wm_schedule($id, WMScheduleRequest $request) {
        $member = User::findOrFail($id);

        $wm_dates = $request->wm_date;

        if (count($wm_dates) > 0):
            foreach ($wm_dates as $key => $value):
                if (!empty($value)):
                    $wm_appt = new MemberAppointment;
                    $wm_appt->user_id = $member->id;
                    $wm_appt->staff_id = Auth::id();
                    $wm_appt->ap_type_id = '1';
                    $wm_appt->app_date = $value;
                    $wm_appt->app_hour = '00:00';
                    $wm_appt->save();
                endif;
            endforeach;

            $member_status = $member->user_memberstatus;
            $member_status->wm_schedule = 'Set';
            $member->user_memberstatus()->save($member_status);

            Session::flash('flash_message', trans('messages.weigh_n_measure_schedule_has_been_created'));
        endif;

        return redirect('cf-lite/weigh-measure/'.$member->id.'/schedule');
    }

    public function update_wm_schedule($id, WMScheduleRequest $request) {
        $member = User::findOrFail($id);

        if (count($request->sch) > 0):
            $wm_dates = $request->wm_date;

            foreach ($wm_dates as $key => $value):
                if (!empty($value)):
                    $wm_appt = MemberAppointment::findOrFail($key);
                    $wm_appt->app_date = $value;
                    $wm_appt->save();
                endif;
            endforeach;

            Session::flash('flash_message', trans('messages.weigh_n_measure_schedule_has_been_updated'));
        endif;

        return redirect('cf-lite/weigh-measure/'.$member->id.'/schedule');
    }

    public function wm_member_list_all_club() {
        if ((Auth::user()->role_id == 8) && (Auth::id() == 8)):
            $members = User::selectRaw('users.id, member_personals.photo, users.name, min(workout_date) as wo_date, 
                EXTRACT(DAY FROM min(workout_date)) as wo_day, member_personals.phone, member_personals.cellphone, 
                member_status.last_wm, member_status.wm_schedule, club_infos.club_code,
                (SELECT total_measurement -
                (SELECT total_measurement FROM weigh_measures mi WHERE mi.wm_date < m.wm_date AND user_id = users.id 
                ORDER BY wm_date DESC LIMIT 1)
                FROM  weigh_measures m WHERE user_id = users.id ORDER BY wm_date DESC LIMIT 1) as tm,
                (SELECT weight -
                (SELECT weight FROM weigh_measures mi WHERE mi.wm_date < m.wm_date AND user_id = users.id 
                ORDER BY wm_date DESC LIMIT 1)
                FROM  weigh_measures m WHERE user_id = users.id ORDER BY wm_date DESC LIMIT 1) as weight,
                (SELECT total_body_fat -
                (SELECT total_body_fat FROM weigh_measures mi WHERE mi.wm_date < m.wm_date AND user_id = users.id 
                ORDER BY wm_date DESC LIMIT 1)
                FROM  weigh_measures m WHERE user_id = users.id ORDER BY wm_date DESC LIMIT 1) as body_fat')
                ->where(function ($q) {
                    $q->where('member_status.status', '=', 'Active')
                        ->orWhere('member_status.status', '=', 'Hold');
                });

            $members = $members->leftJoin('workouts', function ($join) {
                $join->selectRaw('name, min(workout_date) as fwo');
                $join->on('users.id', '=', 'workouts.user_id');
            });

            $members = $members->join('member_personals', function($join){
                $join->on('users.id','=','member_personals.user_id');
            });

            $members = $members->join('member_status', function($join){
                $join->on('users.id','=','member_status.user_id');
            });

            $members = $members->join('club_infos', function ($join) {
                $join->on('users.club_id', '=', 'club_infos.id');
            });

            $members = $members->groupBy('users.id','users.name','member_personals.photo','member_personals.phone',
                'member_personals.cellphone','member_status.last_wm','member_status.wm_schedule','users.created_at',
                'club_infos.club_code')
                ->orderBy('users.created_at', 'desc')
                ->get();

//            return $members;

            return view('cf_lite.wm.wm-member-list-all-club', compact('members'));
        else:
            Session::flash('flash_message', trans('messages.you_are_not_allowed_to_access_this_page'));
            return redirect('cf-lite/dashboard');
        endif;
    }

    public function sync_wmrank_data($user){
        $member = $user;
        // Add To WMRank Added 22/08/2019
        $wmrank_available_m = WMRank::where('user_id','=',$member->id)->where('type','=','monthly')->first();
        $wmrank_available_o = WMRank::where('user_id','=',$member->id)->where('type','=','overall')->first();

        $wm = WeighMeasure::where('user_id','=',$member->id)->orderBy('wm_date','DESC')->first();

        $prev_wm = WeighMeasure::where('user_id','=',$member->id)->where('id','<>',$wm->id)->orderBy('wm_date','DESC')->first();

        $first_wm = WeighMeasure::where('user_id','=',$member->id)->where('id','<>',$wm->id)->orderBy('wm_date','ASC')->first();

        if(empty($prev_wm)){    // Check if previous wm exist, if empty create new WMRank with default value
            $newwmrank = new WMRank();
            $newwmrank->user_id = $member->id;
            $newwmrank->wm_one = 0;
            $newwmrank->wm_two = $wm->id;
            $newwmrank->body_fat_diff = 0;
            $newwmrank->weigh_diff = 0;
            $newwmrank->size_diff = 0;
            $newwmrank->day_diff = 0;
            $newwmrank->wo_count = 0;
            $newwmrank->type = 'monthly';
            $newwmrank->save();
        }else{  // So if previous wm exist
            // Count the workouts
            $workoutcount = Workout::where('user_id','=',$member->id)->where('workout_date','>=',Carbon::parse($prev_wm->wm_date))->where('workout_date','<=',Carbon::parse($wm->wm_date))->count();
            if(empty($wmrank_available_m)){ // Check if WMRank for this user and monthly type exist if it does not then create, if it does then update it
                $newwmrank = new WMRank();
                $newwmrank->user_id = $member->id;
                $newwmrank->wm_one = $prev_wm->id;
                $newwmrank->wm_two = $wm->id;
                $newwmrank->body_fat_diff = $wm->body_fat - $prev_wm->body_fat;
                $newwmrank->weigh_diff = $wm->weight - $prev_wm->weight;
                $newwmrank->size_diff = $wm->total_measurement - $prev_wm->total_measurement;
                $newwmrank->day_diff = Carbon::parse($wm->wm_date)->diffInDays(Carbon::parse($prev_wm->wm_date));
                $newwmrank->wo_count = $workoutcount;
                $newwmrank->type = 'monthly';
                $newwmrank->save();
            }else{
                $wmrank_available_m->user_id = $member->id;
                $wmrank_available_m->wm_one = $prev_wm->id;
                $wmrank_available_m->wm_two = $wm->id;
                $wmrank_available_m->body_fat_diff = $wm->body_fat - $prev_wm->body_fat;
                $wmrank_available_m->weigh_diff = $wm->weight - $prev_wm->weight;
                $wmrank_available_m->size_diff = $wm->total_measurement - $prev_wm->total_measurement;
                $wmrank_available_m->day_diff = Carbon::parse($wm->wm_date)->diffInDays(Carbon::parse($prev_wm->wm_date));
                $wmrank_available_m->wo_count = $workoutcount;
                $wmrank_available_m->type = 'monthly';
                $wmrank_available_m->save();
            }
        }

        if(empty($first_wm)){   // Similiar to previous but for the first wm not previous check if the person has ever wm before she never, then create one with default value
            $newwmranko = new WMRank();
            $newwmranko->user_id = $member->id;
            $newwmranko->wm_one = 0;
            $newwmranko->wm_two = $wm->id;
            $newwmranko->body_fat_diff = 0;
            $newwmranko->weigh_diff = 0;
            $newwmranko->size_diff = 0;
            $newwmranko->day_diff = 0;
            $newwmranko->wo_count = 0;
            $newwmranko->type = 'overall';
            $newwmranko->save();
        }else{  // So the user has first wm
            // Count the workouts
            $workoutcount = Workout::where('user_id','=',$member->id)->where('workout_date','>=',Carbon::parse($first_wm->wm_date))->where('workout_date','<=',Carbon::parse($wm->wm_date))->count();
            if(empty($wmrank_available_o)){ // Check if user has wmrank before for overall type if it doesn't then create, if it does then update
                $newwmranko = new WMRank();
                $newwmranko->user_id = $member->id;
                $newwmranko->wm_one = $first_wm->id;
                $newwmranko->wm_two = $wm->id;
                $newwmranko->body_fat_diff = $wm->body_fat - $first_wm->body_fat;
                $newwmranko->weigh_diff = $wm->weight - $first_wm->weight;
                $newwmranko->size_diff = $wm->total_measurement - $first_wm->total_measurement;
                $newwmranko->day_diff = Carbon::parse($wm->wm_date)->diffInDays(Carbon::parse($first_wm->wm_date));
                $newwmranko->wo_count = $workoutcount;
                $newwmranko->type = 'overall';
                $newwmranko->save();
            }else{
                $wmrank_available_o->user_id = $member->id;
                $wmrank_available_o->wm_one = $first_wm->id;
                $wmrank_available_o->wm_two = $wm->id;
                $wmrank_available_o->body_fat_diff = $wm->body_fat - $first_wm->body_fat;
                $wmrank_available_o->weigh_diff = $wm->weight - $first_wm->weight;
                $wmrank_available_o->size_diff = $wm->total_measurement - $first_wm->total_measurement;
                $wmrank_available_o->day_diff = Carbon::parse($wm->wm_date)->diffInDays(Carbon::parse($first_wm->wm_date));
                $wmrank_available_o->wo_count = $workoutcount;
                $wmrank_available_o->type = 'overall';
                $wmrank_available_o->save();
            }
        }
    }
}
