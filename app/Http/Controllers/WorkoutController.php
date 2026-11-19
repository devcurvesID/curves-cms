<?php

namespace App\Http\Controllers;

use App\Model\Workout\Workout;
use App\User;
use Illuminate\Support\Facades\Auth;
use Session;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\ApiClientService;
use Illuminate\Support\Facades\DB;

class WorkoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('wm-only');
    }

    public function index()
    {
    }

    public function show_result_login_member(Request $request)
    {
        $this->validate($request,[
            'name' => 'required_if:keytag,null',
            'keytag' => 'required_if:name,null',
        ]);

        $nama = $request->name;
        $keytag = $request->keytag;
        if (empty($nama) && empty($keytag)) {
            $members = User::where('role_id', '>', '1')->where('club_id','=',Auth::user()->club_id)->get();
        } else if (!empty($nama) && !empty($keytag)) {
            $members = User::where('role_id', '>', '1')->where('name', 'ILIKE', '%' . $nama . '%')->where('club_id','=',Auth::user()->club_id)->whereHas('user_memberpersonal', function ($r) use ($keytag) {
                $r->where('key_tag_id', '=', $keytag);
            })->get();

//            where('key_tag_id', '=', $keytag)->get();
        } else {
            if (empty($nama)) {
                $members = User::where('role_id', '>', '1')->where('club_id','=',Auth::user()->club_id)->whereHas('user_memberpersonal', function ($r) use ($keytag) {
                    $r->where('key_tag_id', '=', $keytag);
                })->get();
            } else {
                $members = User::where('role_id', '>', '1')->where('club_id','=',Auth::user()->club_id)->where('name', 'ILIKE', '%' . $nama . '%')->get();
            }
        }

        return view('cf_lite.workout.search-result-login', compact('members'));
    }

    public function past_wo($id){
        $user = User::findOrFail($id);
        $date = Carbon::now();

        $userWo = Workout::where('user_id','=',$id)->get();

        return view('cf_lite.workout.add-past-wo', compact('user','date','userWo'));
    }

    public function store_past_wo(Request $request){
        // Add to Workout
        $dates = $request->date_chosen;
        $times = $request->time;
        $selDate = explode(" ", $dates);

        $user = User::findOrFail($request->id);

        // Jonviter DEV
        $user_by_id = DB::table('usermobilecurves_v')->where('id', $request->id)->first();
        if ($user_by_id) {
            $apiClientOne = new ApiClientService();
            $fetch_usercurves = $apiClientOne->post("/mobile-curves/user", (array) $user_by_id);
        }
        // Jonviter DEV
        foreach($selDate as $date){
            $the_status = 0;
            foreach ($user->user_workout as $workout):
                if (Carbon::parse($workout->workout_date)->format('d-m-Y') == Carbon::parse($date)->format('d-m-Y')):
                    $the_status = 1;
                endif;
            endforeach;

            if ($the_status == 0):
                $wo = new Workout;
                $wo->user_id = $user->id;
                $wo->workout_date = Carbon::parse($date.' '.$times[$date]);
                $wo->club_id = Auth::user()->club_id;
                $wo->save();
                 // Jonviter DEV
                $wo->workout_date = $wo->workout_date->toDateTimeString(); 
                $workoutsJson = $wo->toArray();
                $apiClient = new ApiClientService();
                $mobile_curves_api = $apiClient->post("/mobile-curves/workout", $workoutsJson);
                // Jonviter DEV
            endif;
        }

        //Delete from Workout
        $deldates = $request->date_chosen_delete;
        $selDelDate = explode(" ", $deldates);

        foreach($selDelDate as $date){
            $the_status = 0;
            foreach ($user->user_workout as $workout):
                if (Carbon::parse($workout->workout_date)->format('d-m-Y') == Carbon::parse($date)->format('d-m-Y')):
                    $the_status = $workout->id;
                endif;
            endforeach;

            if ($the_status != 0):
                $wo = Workout::findOrFail($the_status);
                $wo->delete();
            endif;
        }

        //Update Member Personal's Flag Added 05102018
        $workouts = Workout::where('user_id', '=', $user->id)
            ->whereDate('workout_date', '>=', Carbon::parse($user->user_memberstatus->from_date)->toDateString())
            ->whereDate('workout_date', '<=', Carbon::parse($user->user_memberstatus->thru_date)->toDateString())
            ->orderBy('workout_date', 'asc')
            ->get();
      

        $flag = 'E';
        if (count($workouts) > 0):
            $fwo = $workouts[0];

            //Hitung lama join
            $fwo_date = Carbon::parse($fwo->workout_date);
            $diffdays = Carbon::today()->diffInDays($fwo_date);
            $diffweeks = floor($diffdays / 7);

            if ($diffweeks == 0):
                $diffweeks = 1;
            endif;

            if ((count($workouts) / $diffweeks) >= 3):
                $flag = 'A';
            elseif (((count($workouts) / $diffweeks) < 3) && ((count($workouts) / $diffweeks) >= 2)):
                $flag = 'B';
            elseif (((count($workouts) / $diffweeks) < 2) && ((count($workouts) / $diffweeks) >= 1)):
                $flag = 'C';
            elseif (((count($workouts) / $diffweeks) < 1) && ((count($workouts) / $diffweeks) >= 0.5)):
                $flag = 'D';
            elseif ((count($workouts) / $diffweeks) < 0.5):
                $flag = 'E';
            endif;
        endif;



        $profile = $user->user_memberpersonal;
        $profile->flag = $flag;
        $user->user_memberpersonal()->save($profile);
        //Update Member Personal's Flag Added 05102018

        return redirect('cf-lite/manual-login/');
    }
}
