<?php

namespace App\Http\Controllers\API;

use App\AutoNumber;
use App\Http\Controllers\Controller;
use App\Model\Appointment\MemberAppointment;
use App\Model\Club\ClubInfo;
use App\Model\Curves\Advertise\Publicity;
use App\Model\Guest\CoachLeadSequence;
use App\Model\MasterData\Challenge;
use App\Model\Survey\Question;
use App\Model\Survey\Survey;
use App\Model\Survey\SurveyAnswer;
use App\Model\Hydration\DailyHydration;
use App\Model\Hydration\LogHydration;
use App\Model\Hydration\MemberHydration;
use App\User;
use App\Model\Guest\BuddyReferralLead;
use App\Model\Workout\Workout;
use App\Model\WM\WeighMeasure;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Support\Facades\DB;

class MemberAppController extends Controller
{
	public $successStatus = 200;
	public $failedStatus = 500;

	public function member_profile() {
        $user = Auth::user();

        if (!empty($user->user_memberpersonal->photo)):
            $user->setAttribute('photo', $user->user_memberpersonal->photo);
        else:
            $user->setAttribute('photo', '');
        endif;

        $member_hydration = MemberHydration::where('user_id','=',$user->id)->first();
        if(empty($member_hydration)){
            $member_hydration = new MemberHydration;
            $member_hydration->user_id = $user->id;
            $member_hydration->hydration_goal = 2000;
            $user->user_memberhydration()->save($member_hydration);
        }

        $user->setAttribute('keytag', $user->user_memberpersonal->key_tag_id);
        $user->setAttribute('role', $user->user_role->role);
        $user->setAttribute('club', $user->user_club->club_name);
        $user->setAttribute('birthday', $user->user_memberpersonal->birth);
        $user->setAttribute('target_hydration', $user->user_memberhydration->hydration_goal);
        $user->user_club;

        return response()->json(['profile' => $user], $this->successStatus);
    }

    public function member_detail() {
        $user = Auth::user();

        $profile = User::findOrFail($user->id);
        if (!empty($profile->user_memberpersonal)):
            $profile->setAttribute('address', $profile->user_memberpersonal->address);
            $profile->setAttribute('phone', $profile->user_memberpersonal->phone);

            if (!empty($profile->user_memberpersonal->photo)):
                $profile->setAttribute('photo', 'member_photos/'.$profile->user_memberpersonal->photo);
            else:
                $profile->setAttribute('photo', 'img.png');
            endif;

            $last_wo = Workout::where('user_id', '=', $user->id)->orderBy('workout_date', 'desc')->first();
            if (!empty($last_wo)):
                $profile->setAttribute('last_wo', Carbon::parse($last_wo->workout_date)->format('d M Y'));
            else:
                $profile->setAttribute('last_wo', '-');
            endif;

            $wos = Workout::where('user_id', '=', $user->id)->get();

            $counter = 0;
            if (!empty($wos)):
                $profile->setAttribute('total_wo', count($wos));

                $date = Carbon::now();
                foreach ($wos as $wo):
                    if ((Carbon::parse($wo->workout_date) >= Carbon::parse($date->startOfWeek())) && ((Carbon::parse($wo->workout_date) <= Carbon::parse($date->endOfWeek())))):
                        $counter++;
                    endif;
                endforeach;

                $profile->setAttribute('target', $this->target_wo(count($wos)));
            else:
                $profile->setAttribute('total_wo', 0);
                $profile->setAttribute('target', 100);
            endif;
            $profile->setAttribute('wo_week', $counter);

            // Check Next WM
            $next_wm = MemberAppointment::where('user_id', '=', $user->id)
                ->whereDate('app_date', '>', Carbon::today())
                ->orderBy('app_date', 'asc')
                ->first();

            if (!empty($next_wm)):
                $profile->setAttribute('next_wm', $next_wm->app_date);
            else:
                $profile->setAttribute('next_wm', '1970-01-01');
            endif;
        endif;

        $profile->user_memberstatus;

        if ($profile->role_id == 7) {
            if (!empty($profile->memberuser_coachmember)) {
                $profile->setAttribute('current_coach', $profile->memberuser_coachmember->coachmember_coachuser->name);
            } else {
                $profile->setAttribute('current_coach', '-');
            }
        } else {
            $profile->setAttribute('current_coach', '-');
        }

        return response()->json($profile, $this->successStatus);
    }

    public function member_wo_main(){
        $user = Auth::user();

        $womos = Workout::selectRaw('COUNT(user_id) as jumlah, extract(month from workout_date) as month, extract(year from workout_date) as year')
            ->where('user_id','=',$user->id)
            ->groupBy('year')
            ->groupBy('month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        $temp_month = 0;
        $temp_year = 0;
        $temp_x = 0;
        foreach ($womos as $wo) {
            if (empty($temp_year)):
                $temp_year = $wo->year;
                $temp_month = $wo->month;
                $temp_x = $temp_year*12 + $temp_month;
            endif;

            if ($temp_x < $wo->year*12 + $wo->month):
                for ($temp_x; $temp_x < $wo->year*12 + $wo->month; $temp_x++):
                    $cur_month = $temp_x%12;
                    $cur_year = floor($temp_x/12);
                    $moname = Carbon::parse('01-'.sprintf('%02d', $cur_month).'-'.$cur_year)->format('F');
                    $womos->push(['jumlah' => 0, 'month' => sprintf('%02d', $cur_month), 'year' => sprintf('%02d', $cur_year), 'moname' => $moname, 'percent' => '0', 'sort' => $cur_year.sprintf('%02d', $cur_month)]);
                endfor;
            endif;

            $wo->setAttribute('moname', Carbon::parse('01-'.sprintf('%02d', $wo->month).'-'.$wo->year)->format('F'));
            $wo->setAttribute('month', sprintf('%02d', $wo->month));
            $wo->setAttribute('sort', $wo->year.sprintf('%02d', $wo->month));
            if($wo->jumlah >= 12){
                $wo->setAttribute('percent','100');
            }else{
                $percent = round(($wo->jumlah / 12)*100, 2);
                $wo->setAttribute('percent', ''.$percent);
            }

            $temp_x++;
        }

        $womos = $womos->sortByDesc('sort')->values();

        return response()->json($womos, $this->successStatus);
    }

    public function member_wm_list() {
        $user = Auth::user();

        $wms = WeighMeasure::where('user_id', '=', $user->id)
            ->orderBy('wm_date', 'desc')
            ->get();

        foreach ($wms as $wm):
            $wm->setAttribute('wmdate', Carbon::parse($wm->wm_date)->format('d M Y'));
            $wm->setAttribute('wmday',Carbon::parse($wm->wm_date)->format('d'));
            $wm->setAttribute('wmmonth',Carbon::parse($wm->wm_date)->format('M'));
            $wm->setAttribute('wmyear',Carbon::parse($wm->wm_date)->format('Y'));
        endforeach;

        return response()->json($wms, $this->successStatus);
    }

    // REGISTRATION
    public function registration_permission(Request $request) {
        if (!empty($request->club)):
            $text = explode('_', $request->club);
            $club = $text[0];
            $keytag = $text[1];

            $check_club = ClubInfo::where('club_code', '=', $club)->first();

            if (!empty($check_club)):
                if (!empty($keytag)):
                    $check_member = User::whereHas('user_memberpersonal', function ($q) use ($keytag) {
                        $q->where('key_tag_id', '=', $keytag);
                    })->first();

                    if (!empty($check_member)):
                        return response()->json($check_member, $this->successStatus);
                    else:
                        return response()->json('failed', $this->failedStatus);
                    endif;
                else:
                    return response()->json('failed', $this->failedStatus);
                endif;
            else:
                return response()->json('failed', $this->failedStatus);
            endif;
        endif;
    }

    public function register($id, Request $request) {
        $this->validate($request, [
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $check_user = User::findOrFail($id);

        if (!empty($check_user)):
            // Update username & password
            $check_user->username = $request->username;
            $check_user->password = bcrypt($request->password);
            $check_user->save();

            // Update qr_generate
            $member_status = $check_user->user_memberstatus;
            $member_status->qr_generate = true;
            $check_user->user_memberstatus()->save($member_status);

            // Login
            if(Auth::attempt(['username' => $request->username, 'password' => $request->password])){
                $user = Auth::user();
                $success['token'] =  $user->createToken('MyApp')->accessToken;

                //For LoginTwo
                return response()->json(['success' => true, 'user' => $success], $this->successStatus);
            }
            else{
                //For LoginTwo
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }
        endif;
    }

    public function get_publicities(Request $request){
        $publicities = Publicity::where('from_date','<=',Carbon::today())->where('thru_date','>=',Carbon::today())->where('status','publish')->get();

        return response()->json($publicities, $this->successStatus);
    }

    public function get_publicity($id, Request $request){
        $publicity = Publicity::find($id);

        return response()->json($publicity, $this->successStatus);
    }

    public function get_club_list(){
        $clubs = ClubInfo::where('id','<>',5)->get();

        return response()->json($clubs, $this->successStatus);
    }

    public function register_new_lead(Request $request){
        if(!empty($request->fullname) && !empty($request->phone) && !empty($request->club)){
            $club = ClubInfo::find($request->club);

            $newLead = new BuddyReferralLead;
            $newLead->fullname = $request->fullname;
            $newLead->phone = $request->phone;
            $newLead->wa_number = $request->phone;
            $newLead->source_id = 4;
            $newLead->is_br = 'NotBR';
            $newLead->status_gm = 'Guest';
            $newLead->email = null;
            $newLead->no_more_call = false;
            $newLead->club_id = $request->club;

            $coach_lead_sequence = CoachLeadSequence::where('club_id','=',$request->club)->orderBy('sequence','DESC')->get();
            $max_seq = 0;
            if($coach_lead_sequence->first()){
                $max_seq = $coach_lead_sequence[0]->sequence;
            }
            foreach ($coach_lead_sequence->where('sequence', $club->current_sequence) as $coach_seq):
                //Insert to coach member table
                $newLead->staff_id = $coach_seq->coach_lead_id;

                //Update club current sequence
                if ($club->current_sequence < $max_seq):
                    $club->current_sequence = $club->current_sequence + 1;
                elseif ($club->current_sequence == $max_seq):
                    $club->current_sequence = 1;
                endif;
                $club->save();
            endforeach;
            $newLead->save();

            return response()->json($this->successStatus);
        }
    }

    public function club_now() {
        $user = Auth::user();

        $members = Workout::selectRaw('workouts.workout_date, users.name, member_personals.photo')
            ->where('workouts.club_id', '=', $user->club_id)
            ->where('workouts.workout_date', '>=', Carbon::now()->subMinutes('45'))
            ->join('users', function ($join) {
                $join->on('users.id', '=', 'workouts.user_id');
                $join->where('users.role_id', '=', '7');
            })
            ->join('member_personals', function ($join) {
                $join->on('users.id', '=', 'member_personals.user_id');
            })
            ->orderBy('workouts.workout_date', 'desc')
            ->get();

        return response()->json($members, $this->successStatus);
    }

    public function show_reward(Request $request) {
        if (!empty($request->target)):
            $reward = Challenge::where('type', '=', 'workout')
                ->where('variable_target', '=', $request->target)
                ->where('status', '=', 'aktif')
                ->first();

            return response()->json($reward, $this->successStatus);
        else:
            return response()->json($this->failedStatus);
        endif;
    }

    public function target_wo($wos) {
        $target = 0;
        if ($wos < 100):
            $target = 100;
        elseif (($wos >= 100) && ($wos < 200)):
            $target = 200;
        elseif (($wos >= 200) && ($wos < 300)):
            $target = 300;
        elseif (($wos >= 300) && ($wos < 400)):
            $target = 400;
        elseif (($wos >= 400) && ($wos < 500)):
            $target = 500;
        elseif (($wos >= 500) && ($wos < 600)):
            $target = 600;
        elseif (($wos >= 600) && ($wos < 700)):
            $target = 700;
        elseif (($wos >= 700) && ($wos < 800)):
            $target = 800;
        elseif (($wos >= 800) && ($wos < 900)):
            $target = 900;
        elseif ($wos >= 900):
            $target = 1000;
        endif;

        return $target;
    }

    public function get_member_birthday(Request $request){
        $user = User::find($request->user_id);

        return response()->json($user->user_memberpersonal->birth);
    }
	
	public function survey_list() {
        $user = Auth::user();

        $surveys = Workout::selectRaw('workouts.id, workouts.workout_date, club_infos.club_name')
            ->where('workouts.user_id', '=', $user->id)
            ->whereHas('workout_survey', function ($sv) {
                $sv->where('answered', '=', false);
            })
            ->join('club_infos', function ($join) {
                $join->on('club_infos.id', '=', 'workouts.club_id');
            })
            ->orderBy('workout_date', 'desc')
            ->get();

        return response()->json($surveys, $this->successStatus);
    }

    public function get_survey() {
        $questions = Question::where('status', '=', 'Active')->get();

        return response()->json($questions, $this->successStatus);
    }

    public function store_survey($id, Request $request) {
        $workout = Workout::findOrFail($id);

        if (!empty($workout->workout_survey)):
            $survey = $workout->workout_survey;
            $survey->answered = true;
            $workout->workout_survey()->save($survey);

            if (!empty($request->q1)):
                $answer = new SurveyAnswer;
                $answer->workout_id = $workout->id;
                $answer->question_id = $request->q1;
                $answer->answer = $request->a1;
                $answer->save();
            endif;

            if (!empty($request->q2)):
                $answer = new SurveyAnswer;
                $answer->workout_id = $workout->id;
                $answer->question_id = $request->q2;
                $answer->answer = $request->a2;
                $answer->save();
            endif;

            if (!empty($request->q3)):
                $answer = new SurveyAnswer;
                $answer->workout_id = $workout->id;
                $answer->question_id = $request->q3;
                $answer->answer = $request->a3;
                $answer->save();
            endif;

            if (!empty($request->q4)):
                $answer = new SurveyAnswer;
                $answer->workout_id = $workout->id;
                $answer->question_id = $request->q4;
                $answer->answer = $request->a4;
                $answer->save();
            endif;

            if (!empty($request->q5)):
                $answer = new SurveyAnswer;
                $answer->workout_id = $workout->id;
                $answer->question_id = $request->q5;
                $answer->answer = $request->a5;
                $answer->save();
            endif;

            return response()->json($this->successStatus);
        endif;

        return response()->json($this->failedStatus);
    }

    public function skip_survey($id) {
        $workout = Workout::findOrFail($id);

        if (!empty($workout->workout_survey)):
            $survey = $workout->workout_survey;
            $survey->answered = true;
            $workout->workout_survey()->save($survey);

            return response()->json($this->successStatus);
        endif;

        return response()->json($this->failedStatus);
    }

    public function get_survey_counter() {
        $user = Auth::user();

        // Check Surveys Added 07102019
        $surveys = Workout::where('user_id', '=', $user->id)
            ->whereHas('workout_survey', function ($q) {
                $q->where('answered', '=', false);
            })
            ->get()
            ->count();

        return response()->json(['survey' => $surveys], $this->successStatus);
    }

    public function add_daily_hydration(Request $request){
        $user = User::find($request->user_id);

        $member_hydration = new MemberHydration();
        if(empty($user->user_memberhydration)){
            $member_hydration->user_id = $request->user_id;
            $member_hydration->hydration_goal = 2000;
            $user->user_memberhydration()->save($member_hydration);
        }else{
            $member_hydration = $user->user_memberhydration;
        }

        $daily_hydration = DailyHydration::where('user_id','=',$request->user_id)->where('hydration_date','=',Carbon::today())->first();

        if(empty($daily_hydration)){
            $daily_hydration = new DailyHydration();
            $daily_hydration->user_id = $request->user_id;
            $daily_hydration->hydration_date = Carbon::today();
            $daily_hydration->total_hydration = $request->water_inc;
            $daily_hydration->target_hydration = $member_hydration->hydration_goal;
        }else{
            if($daily_hydration->total_hydration + $request->water_inc < 0){
                $daily_hydration->total_hydration = 0;
            }else{
                $daily_hydration->total_hydration += $request->water_inc;
            }
        }

        $daily_hydration->save();

        $log_hydration = new LogHydration();
        $log_hydration->user_id = $request->user_id;
        $log_hydration->hydration_datetime = Carbon::now();
        $log_hydration->hydration_value = $request->water_inc;
        $log_hydration->save();

        return response()->json($daily_hydration, $this->successStatus);
    }

    public function update_hydration_value(Request $request){
        $user = User::find($request->user_id);

        $member_hydration = new MemberHydration();
        if(empty($user->user_memberhydration)){
            $member_hydration->user_id = $request->user_id;
            $member_hydration->hydration_goal = $request->target_hydration;
            $user->user_memberhydration()->save($member_hydration);
        }else{
            $user->user_memberhydration->hydration_goal = $request->target_hydration;
            $user->user_memberhydration->save();
        }
        
        return response()->json($this->successStatus);
    }

    public function get_current_hydration(){
        $user = Auth::user();

        $member_hydration = MemberHydration::where('user_id','=',$user->id)->first();
        if(empty($member_hydration)){
            $member_hydration = new MemberHydration;
            $member_hydration->user_id = $user->id;
            $member_hydration->hydration_goal = 2000;
            $user->user_memberhydration()->save($member_hydration);
        }

        $daily_hydration = DailyHydration::where('user_id','=',$user->id)->where('hydration_date','=',Carbon::today())->first();
        if(empty($daily_hydration)){
            $daily_hydration = new DailyHydration();
            $daily_hydration->user_id = $user->id;
            $daily_hydration->hydration_date = Carbon::today();
            $daily_hydration->total_hydration = 0;
            $daily_hydration->target_hydration = $user->user_memberhydration->hydration_goal;
            $daily_hydration->save();
        }

        return response()->json($daily_hydration, $this->successStatus);
    }

    public function get_member_by_name(Request $request){
        $members = User::where('role_id','=',7)->where('name','ILIKE', '%'.$request->q.'%')->limit(10)->get();

        return response()->json($members, $this->successStatus);
    }

    public function get_wo_weekly(Request $request) {
        $user = Auth::user();

        $wos = Workout::selectRaw('COUNT(user_id) as jumlah, (extract(WEEK FROM workout_date) - extract(WEEK FROM TIMESTAMP \'' . $request->year.'-'.$request->month.'-01\')) as week')
            ->where('user_id','=', $user->id)
            ->whereMonth('workout_date', $request->month)
            ->whereYear('workout_date', $request->year)
            ->groupBy('week')
            ->orderBy('week', 'asc')
            ->get();

        $first_date = Carbon::createFromFormat('Y-m-d', $request->year.'-'.$request->month.'-01')->toDateString();
        $end_date = Carbon::createFromFormat('Y-m', $request->year.'-'.$request->month)->lastOfMonth()->toDateString();

        $query_made = '';
        $count_week = 0;
        while($first_date <= $end_date) {
            $next_week = Carbon::parse($first_date)->addWeek(1)->toDateString();
            if ($next_week > $end_date) {
                $next_week = Carbon::parse($end_date)->addDay(1)->toDateString();
            }
            $count_week++;
            $query_made .= '(
                SELECT COUNT(user_id)
                FROM workouts
                WHERE user_id = \''.$user->id.'\' AND (CAST(workout_date as DATE) >= \''.$first_date.'\'
                    AND CAST(workout_date as DATE) < \''.$next_week.'\')
            ) as week'.$count_week.', ';
            $first_date = Carbon::parse($first_date)->addWeek(1)->toDateString();
        }

        $query_made = 'SELECT '.$query_made;
        $arrays = DB::select(substr($query_made, 0, -2));
        $datas = collect($arrays);

        $object = new \stdClass();
        foreach ($datas[0] as $key => $value) {
            $object->$key = $value;
        }

//        $wos = $wos->sortBy('week')->values();

	    return response()->json($object, $this->successStatus);
    }
}