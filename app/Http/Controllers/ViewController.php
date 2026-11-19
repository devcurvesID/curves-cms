<?php

namespace App\Http\Controllers;

use App\Model\CommunityPartner\CommunityPartnerMessage;
use App\Model\Member\ContinueMembership;
use App\Model\Member\GlobalMessage;
use App\Model\Member\MemberStatus;
use App\Model\Survey\Survey;
use Session;
use App\Events\ivanTrigger;
use App\MemberMeasurement;
use App\MemberWorkout;
use App\Model\Appointment\MemberAppointment;
use App\Model\Club\ClubLogin;
use App\Model\MasterData\Challenge;
use App\Model\Payment\MemberPayment;
use App\Model\WM\WeighMeasure;
use App\Model\Workout\Workout;
use App\User;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;

use App\AccurateAuth;
use App\Helpers\Accurate;
use stdClass;
use Storage;

use App\Services\ApiClientService;
use Illuminate\Support\Facades\DB;


class ViewController extends Controller
{
    public function index($id)
    {
        $the_status = 0;
        $club_login = ClubLogin::findOrFail($id);
        if (!empty(session('encrypted_password'))):
            if ($club_login && Hash::check($club_login->password, session('encrypted_password'))):
                $the_status = 1;
            endif;
        endif;

        if ($the_status == 1):
            return view('cf_member_view.index', compact('club_login'));
        elseif ($the_status == 0):
            return redirect('club-login');
        endif;
    }

    public function show($id)
    {
        $user = User::findOrFail($id);

        //Total Workout
        $total_workout = Workout::where('user_id', '=', $user->id)->get()->count();

        //Payment Due Date
        $debtcount = MemberPayment::where('user_id','=',$user->id)
            ->where('payment_date','<',Carbon::now())
            ->where('payment_status','<>','Paid')
            ->whereHas('memberpayment_user',function($r){
                $r->whereHas('user_memberstatus',function($s){
                    $s->where('payment_status','=','Set');
                });
            })
            ->get()
            ->count();

        //Nearest WO Challenge
        $challenges = Challenge::where('type', '=', 'workout')
            ->where('variable_target', '>', $total_workout)
            ->orderBy('variable', 'asc')
            ->first();


        //Workout this month
        $start = new Carbon('first day of this month');
        $end = new Carbon('last day of this month');

        $total_workout_this_month = Workout::where('user_id', '=', $user->id)
            ->whereDate('workout_date', '>=', $start->toDateString())
            ->whereDate('workout_date', '<=', $end->toDateString())
            ->get()
            ->count();

        $member_appointments = MemberAppointment::where('user_id', '=', $user->id)
            ->where('ap_type_id', '=', '1')
            ->whereDate('app_date', '>=', Carbon::now()->toDateString())
            ->orderBy('app_date', 'asc')
            ->take(3)
            ->get();

        if ($member_appointments):
            $wms = WeighMeasure::where('user_id', '=', $user->id)
                ->whereDate('wm_date', '<=', Carbon::now()->toDateString())
                ->orderBy('wm_date', 'desc')
                ->take(3)
                ->get();
        endif;

        //Check Global Message added 06032018
        $global_messages = GlobalMessage::whereDate('from_date', '<=', Carbon::today()->toDateString())
            ->whereDate('thru_date', '>=', Carbon::today()->toDateString())
            ->whereHas('globalmessage_club',function($r) use ($user){
                $r->where('club_id','=',$user->club_id);
            })
            ->get();
        //End of Check Global Message

        //Check Community Partner Message added 07032018
        $cp_message = CommunityPartnerMessage::whereDate('from_date', '<=', Carbon::today()->toDateString())
            ->whereDate('thru_date', '>=', Carbon::today()->toDateString())
            ->get();
        //End of Check Community Partner Message

        //Check Membership Time Left
        $member_status = MemberStatus::where('user_id','=',$user->id)->orderBy('thru_date','desc')->first();
        //End of Check Membership Time Left

        //Check Renewal
        $renewals = ContinueMembership::where('user_id', '=', $user->id)
            ->where('status', '=', 'Pending')
            ->get();
        //End of Check Renewal

        return view('cf_member_view.after_login', compact('user', 'challenges', 'debtcount', 'total_workout', 'total_workout_this_month', 'member_appointments', 'wms', 'global_messages','cp_message','member_status', 'renewals'));
    }

    public function show_member_not_fwo($id)
    {
        $user = User::findOrFail($id);

        return view('cf_member_view.after_login_not_fwo', compact('user'));
    }

    public function member_login($id, Request $request) {
        $club_id = $id;
        $keytag = $request->keytag;

        //Cek User
        $user = User::whereHas('user_memberstatus', function ($q) {
            $q->where('status', '=', 'Active');
        })
            ->whereHas('user_memberpersonal', function ($r) use ($keytag) {
                $r->where('key_tag_id', '=', $keytag);
            })
            ->first();

        if ($user):
//            $total_workout = Workout::where('user_id', '=', $user->id)->get()->count();

            //For Event Listener
            if (!empty($user->user_memberpersonal->photo)):
                $the_photo = 'images/member_photos/'.$user->user_memberpersonal->photo;
            else:
                $the_photo = 'images/img.png';
            endif;
//            $the_time = date('H:i T', strtotime(Carbon::now()));
            $the_time = date('H:i T', strtotime(Carbon::createFromFormat('Y-m-d H:i', Carbon::now()->format('Y-m-d H:i'), session('timezone'))));
            $the_time =  Carbon::parse($the_time)->setTimezone(session('timezone'))->format('H:i T');

//            event(new ivanTrigger($user, $club_id, $the_photo, $the_time));

            // Jonviter DEV
            // $user_by_id = DB::table('usermobilecurves_v')->where('id', $user->id)->first();
            // if ($user_by_id) {
            //     $apiClientOne = new ApiClientService();
            //     $fetch_usercurves = $apiClientOne->post("/mobile-curves/user", (array) $user_by_id);
            // }
            // Jonviter DEV

            //Check Workout
            $total_workout = Workout::where('user_id', '=', $user->id)->get()->count();
            if ($total_workout > 0):
                //Cek jika hari ini telah WO
                $wo_date = Workout::where('user_id', '=', $user->id)
                    ->whereDate('workout_date', '=', Carbon::today()->toDateString())
                    ->get()
                    ->count();
                if ($wo_date == 0):
                    try {
                        $workout = new Workout;
                        $workout->user_id = $user->id;
//                    $workout->workout_date = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::now()->format('Y-m-d H:i:s'), session('timezone'))->setTimezone('UTC');
                        $workout->workout_date = Carbon::now();
                        $workout->club_id = $club_id;
                        $workout->save();
                        // Jonviter DEV
                        
                        $workout->workout_date = $workout->workout_date->toDateTimeString(); 
                        $workoutData = $workout->toArray();

                        $apiClient = new ApiClientService();
                        $mobile_curves_api = $apiClient->post("/mobile-curves/workout", $workoutData);
                        // Jonviter DEV
                        // var_dump($workoutData); die;
                        // Add Survey Status for Member App Added 07102019
                        $survey = new Survey;
                        $survey->answered = false;
                        $workout->workout_survey()->save($survey);
                    } catch (\Exception $e) {
                        event(new ivanTrigger($user, $club_id, $the_photo, $the_time));
                        return redirect('member-details/'.$user->id);
                    }
                endif;

                event(new ivanTrigger($user, $club_id, $the_photo, $the_time));
                return redirect('member-details/'.$user->id);
            else:
                event(new ivanTrigger($user, $club_id, $the_photo, $the_time));
                return redirect('member-details/create-fwo/'.$user->id);
            endif;
        else:
            Session::flash('flash_message', trans('messages.member_not_found'));
            return redirect('member-scan/'.$club_id);
        endif;
    }

    public function check_club_login(Request $request) {
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required',
        ]);

        $the_status = 0;

        $check = ClubLogin::where('username', '=', $request->username)->first();
        if ($check && Hash::check($request->password, $check->encrypted_password)):
            $the_status = 1;
        endif;

        if ($the_status == 1):
            session(['encrypted_password' => bcrypt($request->password)]);
            session(['timezone' => $request->timezone]);
            session(['the_club' => $check->club_id]);
            return redirect('member-scan/'.$check->club_id);
        elseif ($the_status == 0):
            return redirect('club-login');
        endif;
    }

    //AJAX
    public function wm_today_check(Request $request) {
        $member = User::findOrFail($request->member_id);

        $wm_schedules = MemberAppointment::where('user_id', '=', $member->id)
            ->where('ap_type_id', '=', '1')
            ->whereDate('app_date', '<=', Carbon::now()->toDateString())
            ->orderBy('app_date', 'asc')
//            ->take(1)
            ->get();

        $return = 0;
        $wm_day = 0;
        $pay_day = 0;
        if ($wm_schedules != null):
            if ($wm_schedules):
                //Check WM after that day Added 13102018
                $the_date = null;
                $open_wm = 0;
                foreach ($wm_schedules as $wm_schedule):
                    $the_date = $wm_schedule->app_date;
                endforeach;

                if ($the_date != null):
                    $last_wm = WeighMeasure::where('user_id', '=', $member->id)
                        ->whereDate('wm_date', '>=', Carbon::parse($the_date)->toDateString())
                        ->whereDate('wm_date', '<=', Carbon::now()->toDateString())
                        ->get();

                    if (count($last_wm) == 0):
                        $open_wm = 1;
                    endif;
                else:
                    $open_wm = 1;
                endif;

                if ($open_wm == 1):
                    $wm_day = 1;
                    $return  = 1;
                endif;
                //Check WM after that day Added 13102018
//                if (!empty($member->user_memberpersonal->photo)):
//                    $member->setAttribute('photo', 'images/member_photos/'.$member->user_memberpersonal->photo);
//                else:
//                    $member->setAttribute('photo', 'images/img.png');
//                endif;
//
//                $return  = 1;
            endif;
        endif;

        //Update Member Personal's Flag Added 05102018
        $workouts = Workout::where('user_id', '=', $member->id)
            ->whereDate('workout_date', '>=', Carbon::parse($member->user_memberstatus->from_date)->toDateString())
            ->whereDate('workout_date', '<=', Carbon::parse($member->user_memberstatus->thru_date)->toDateString())
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

        $profile = $member->user_memberpersonal;
        $profile->flag = $flag;
        $member->user_memberpersonal()->save($profile);
        //Update Member Personal's Flag Added 05102018

        //Payment Due Date Added 13102018
        $payments = MemberPayment::where('user_id', '=', $member->id)
            ->where('payment_category', '=', 'Monthly')
            ->where('payment_status', '=', 'Unpaid')
            ->whereDate('payment_date', '<=', Carbon::now()->toDateString())
            ->get();

        if (count($payments) > 0):
            $pay_day = 1;
            $return = 1;
        endif;
        //Payment Due Date Added 13102018

        if ($return == 1):
            if (!empty($member->user_memberpersonal->photo)):
                $member->setAttribute('photo', 'images/member_photos/'.$member->user_memberpersonal->photo);
            else:
                $member->setAttribute('photo', 'images/img.png');
            endif;

            return response()->json(array('status' => 'open', 'wm_day' => $wm_day, 'payment_day' => $pay_day, 'member' => $member));
        else:
            return response()->json(array('status' => 'close'));
        endif;
    }


    public function due_payments() {
        $currentClub = Auth::user()->club_id;
        $now = Carbon::now('UTC');
        $currentDate = Carbon::now('UTC')->toDateString();
        $toDate = Carbon::now('UTC')->addDays(6)->toDateString();
        $yesterday = Carbon::yesterday()->toDateString();

        //Payment Due
        $sevenDaysPayments = MemberPayment::where('payment_status', '=', 'Unpaid')
            ->whereBetween('payment_date', [$currentDate, $toDate])
            //->where('payment_date', '<=', $toDate )
            ->with('memberpayment_user.user_memberpersonal')
            ->with('memberpayment_user.user_memberstatus')
            ->with('memberpayment_user.user_workout',function ($q) {
                $q->orderBy('id', 'desc');
                //$q->first();
            })
            ->orderBy('payment_date', 'desc')
            ->get();

        $pastDues = MemberPayment::where('payment_status', '=', 'Unpaid')
            //->whereBetween('payment_date', [$currentDate, $toDate])
            ->where('payment_date', '<=', $yesterday )
            ->with('memberpayment_user.user_memberpersonal')
            ->with('memberpayment_user.user_memberstatus')
            ->with('memberpayment_user.user_workout',function ($q) {
                $q->orderBy('id', 'desc');
                //$q->first();
            })
            ->orderBy('payment_date', 'desc')
            ->get();

        $sevenDaysData = []; $pastDuesData = []; $pastDuesInactive = [];
        if (count($sevenDaysPayments) > 0):
            $sevenDaysData = $this->processPaymentData($sevenDaysPayments, $currentClub, 'Active');
        endif;

        if (count($pastDues) > 0):
            $pastDuesData = $this->processPaymentData($pastDues, $currentClub, 'Active');
        endif;

        if (count($pastDues) > 0):
            $pastDuesInactive = $this->processPaymentData($pastDues, $currentClub, 'Inactive');
        endif;

        return response()->json(array('now'=>$now, 'countUpcoming' => count($sevenDaysData),'countPastDues' => count($pastDuesData), 'countPastDuesInactive' => count($pastDuesInactive), 'current'=> $currentDate, 'toDate' => $toDate, 'yesterday' => $yesterday, 'club_id'=>$currentClub, 'upcoming'=>$sevenDaysData, 'pastDues'=>$pastDuesData, 'pastDuesInactive'=>$pastDuesInactive));

    }

    public function processPaymentData($data, $currentClub, $memberType = ''){
        $payments = [];

        foreach ($data as $item) {
            if($item->memberpayment_user->club_id == $currentClub){

                if($memberType === 'Active'){
                    if($item->memberpayment_user->user_memberstatus->status == 'Active'){

                        if (!empty($item->memberpayment_user->user_memberpersonal->photo)){
                            $photo = URL::asset('images/member_photos/'.$item->memberpayment_user->user_memberpersonal->photo);
                        }else{
                            $photo = URL::asset('images/img.png');
                        }
                        //workout human date
                        if(count($item->memberpayment_user->user_workout) > 0){
                            //$lastWorkout =  Carbon::createFromFormat('Y-m-d H:i:s', $item->memberpayment_user->user_workout[0]->workout_date)->format('d/M/Y');

                            $difference = Carbon::now()->diff($item->memberpayment_user->user_workout[0]->workout_date)->days;

                            if($difference > 60){
                                $lastWorkout =  Carbon::createFromFormat('Y-m-d H:i:s', $item->memberpayment_user->user_workout[0]->workout_date)->format('d/M/Y');
                            }else{
                                $lastWorkout =  Carbon::parse($item->memberpayment_user->user_workout[0]->workout_date)->locale('en')->diffForHumans();
                            }
                        }else{
                            $lastWorkout =  '';
                        }
                        $data = [
                            'id' => $item->id,
                            'amount_due' => number_format($item->amount_due),
                            'payment_date' => Carbon::createFromFormat('Y-m-d H:i:s', $item->payment_date)->format('d/M/Y'),
                            'payment_status' => $item->payment_status,
                            'name' => $item->memberpayment_user->name,
                            'club_id' => $item->memberpayment_user->club_id,
                            'photo' => $photo,
                            'billUrl' => url('cf-lite/payment/bill-list/'.$item->memberpayment_user->id),
                            'userStatus' => $item->memberpayment_user->user_memberstatus->status,
                            'workout' => $lastWorkout,
                            'workouts' => (count($item->memberpayment_user->user_workout) > 0) ?$item->memberpayment_user->user_workout[0]->workout_date : ''
                        ];
                        $payments[] = $data;
                    }
                }elseif($memberType === 'Inactive'){
                    if($item->memberpayment_user->user_memberstatus->status === 'Inactive' || $item->memberpayment_user->user_memberstatus->status === 'Hold' || $item->memberpayment_user->user_memberstatus->status === 'Stopped'){

                        if (!empty($item->memberpayment_user->user_memberpersonal->photo)){
                            $photo = URL::asset('images/member_photos/'.$item->memberpayment_user->user_memberpersonal->photo);
                        }else{
                            $photo = URL::asset('images/img.png');
                        }

                        //workout human date
                        if(count($item->memberpayment_user->user_workout) > 0){
                            $difference = Carbon::now()->diff($item->memberpayment_user->user_workout[0]->workout_date)->days;

                            if($difference > 60){
                                $lastWorkout =  Carbon::createFromFormat('Y-m-d H:i:s', $item->memberpayment_user->user_workout[0]->workout_date)->format('d/M/Y');
                            }else{
                                $lastWorkout =  Carbon::parse($item->memberpayment_user->user_workout[0]->workout_date)->locale('en')->diffForHumans();
                            }

                        }else{
                            $lastWorkout =  '';
                        }

                        $data = [
                            'id' => $item->id,
                            'amount_due' => number_format($item->amount_due),
                            'payment_date' => Carbon::createFromFormat('Y-m-d H:i:s', $item->payment_date)->format('d/M/Y'),
                            'payment_status' => $item->payment_status,
                            'name' => $item->memberpayment_user->name,
                            'club_id' => $item->memberpayment_user->club_id,
                            'photo' => $photo,
                            'billUrl' => url('cf-lite/payment/bill-list/'.$item->memberpayment_user->id),
                            'userStatus' => $item->memberpayment_user->user_memberstatus->status,
                            'workout' => $lastWorkout,
                            'workouts' => (count($item->memberpayment_user->user_workout) > 0) ?$item->memberpayment_user->user_workout[0]->workout_date : ''
                        ];
                        $payments[] = $data;
                    }
                }
            }
        }
        return $payments;
    }

    public function accurate_membership($club, $keyword){
        //GET VARIABLES
        $accurate = AccurateAuth::find(1);
        $databaseID = Accurate::accurateClub($club);
        $openDB = Accurate::AccurateOpenDB($databaseID, $accurate->access_token );
        $branchID = Accurate::accurateBranch($club);


        $accurateItem = Accurate::membershipTypes($keyword);
        if($accurateItem){
            //do nothing
        }else{
            //$accurateItem = Accurate::AccurateCreateItem($openDB, $userData, 'INVENTORY');
        }

        dd($accurateItem);
    }

    public function accurateOauth(){
        $url = 'https://account.accurate.id/oauth/authorize';

        $params['client_id'] = env('ACCURATE_CLIENT_ID');
        $params['secret'] = env('ACCURATE_CLIENT_SECRET');
        $params['response_type'] = 'code';
        $params['redirect_uri'] = env('ACCURATE_REDIRECT_URI');

        $params['scope'] = "item_view item_save customer_save sales_invoice_save customer_view";

        $paramString = '?'.http_build_query($params);
        header("Location: ".$url .$paramString);
        exit;
    }

    public function oauth(){

        try {
            $response = $this->accurateOauth();
            //print_r( response()->json($response) );
            print_r($response);
        } catch (\Exception $e) {
            $response = Accurate::handleExceptionAndLog($e, 'Accurate', '', 'oauth()');
            return response()->json($response, $response['errorcode']);
        }
    }

    //FIRST LINE TO CHECK TOKEN
    public function oauth_aol(){
        //Check if we have tokens in db table
        $accurate = AccurateAuth::find(1);
        $databaseID = Auth::user() ? Accurate::accurateClub(Auth::user()->club_id) : env('ACCURATE_DATABASE_ID');
        /* if(Auth::user()){
            $databaseID = Accurate::accurateClub(Auth::user()->club_id);
        }else{
            $databaseID = '';
        } */
        //dd(Accurate::accurateGetDB());

        //echo 'authorization catcher page..';
        //if($databaseID != ''){
            if(isset($_GET['code'])){
                $code = $_GET["code"];

                $obj = new stdClass;
                $this->oauth_getToken($code, $obj, [], true);
            }elseif ($accurate) {

                $openDB = Accurate::AccurateOpenDB($databaseID, $accurate->access_token );

                if(!$openDB){
                    $this->accurateOauth();
                }else{
                    $this->oauth_getToken($accurate->code, $accurate, $openDB, false);
                }
            }else{
                $this->accurateOauth();
            }
        //}else{
        //    echo 'Selected Club no equivalent accurate database..';
        //}

    }

    public function accurate_item($club, $keyword, $itemType){
        //GET VARIABLES
        $accurate = AccurateAuth::find(1);
        $databaseID = Accurate::accurateClub($club);
        $openDB = Accurate::AccurateOpenDB($databaseID, $accurate->access_token );
        $branchID = Accurate::accurateBranch($club);

        $userData = [
            'name' => $keyword,
            'item' => $keyword,
            'fa_date' => date("d/m/Y"),
            'pay' => '5000000',
        ];

        $accurateItem = Accurate::accurateCheckItem_TEST($openDB, $userData, $itemType);

        /* if($accurateItem){
            //do nothing
        }else{
            //$accurateItem = Accurate::AccurateCreateItem($openDB, $userData, 'INVENTORY');
            $accurateItem = Accurate::accurateCheckItem_TEST($openDB, $userData, 'PROMO');
        } */

        dd($accurateItem);
    }

    public function accurate_items($club, $itemType, $page, $max){
        //GET VARIABLES
        $accurate = AccurateAuth::find(1);
        $databaseID = Accurate::accurateClub($club);
        $openDB = Accurate::AccurateOpenDB($databaseID, $accurate->access_token );
        $branchID = Accurate::accurateBranch($club);

        $x = 1;
        $accurateItems = [];
        while($x <= 100) {
            $_accurateItems = Accurate::accurateCheckItems($openDB, $itemType, $x, $max);

            if($_accurateItems){
                //do nothing
                $accurateItems = array_replace_recursive($accurateItems, $_accurateItems);
                //$accurateItems[] = $_accurateItems;
            }else{
                break;
            }
            $x++;
        }

        dd($accurateItems);
    }

    public function oauth_getToken($code, $obj, $openDB, $newcode){
        $clientId = env('ACCURATE_CLIENT_ID');
        $clientSecret = env('ACCURATE_CLIENT_SECRET');
        $oauthCallback = env('ACCURATE_REDIRECT_URI');
        //$databaseID = env('ACCURATE_DATABASE_ID');
        $databaseID = Auth::user() ? Accurate::accurateClub(Auth::user()->club_id) : env('ACCURATE_DATABASE_ID');

        $basicAuth = base64_encode("$clientId:$clientSecret");

        // Header
        $header = array(
            "Authorization: Basic $basicAuth",
            "Content-Type: application/x-www-form-urlencoded"
        );


        if($newcode){
            // Content
            $content = array(
                "code" => $code,
                "grant_type" => "authorization_code",
                "redirect_uri" => $oauthCallback
            );

            // Connect API
            $opts = array("http" =>
                array(
                    "method" => "POST",
                    "header" => $header,
                    "content" => http_build_query($content),
                    "ignore_errors" => true,
                )
            );

            $url = "https://account.accurate.id/oauth/token";

            $context  = stream_context_create($opts);
            $response = file_get_contents($url, false, $context);

            // Output
            $json = json_decode($response);
            $accessToken = $json->{"access_token"};
            $refreshToken = $json->{"refresh_token"};

            //save to DB
            $accurate = AccurateAuth::findOrCreate(1);
            $accurate->access_token = $accessToken;
            $accurate->refresh_token = $refreshToken;
            $accurate->code = $code;
            $accurate->save();

            echo "<div>OAuth New Token</div>";
            echo "<ul>";
            echo "<li>access_token = $accessToken</li>";
            echo "<li>refresh_token = $refreshToken</li>";
            echo "<li>Open DB = Open</li>";

            $openDB = Accurate::AccurateOpenDB($databaseID, $accurate->access_token );
            //$openDB = $this->open_db($databaseID, $accurate->access_token );

            if($openDB){
                echo "<li>Session = ". $openDB['session'] ."</li>";
                echo "<li>Host = ". $openDB['host'] ."</li>";
                /* $items = json_decode($this->accurate_check_items($openDB));
                //echo "<li>Items = ". $items ."</li>";
                if($items){
                    foreach ($items->d as $key => $value) {
                        echo "<li>Item = ". $value->id ." : ".$value->no."</li>";
                    }
                } */
            }
            echo "</ul>";

        }else{
            $accessToken = $obj->access_token;
            $refreshToken = $obj->refresh_token;
            $created_date = $obj->updated_at;

            $created_date = Carbon::parse($created_date);
            $diffdays = Carbon::today()->diffInDays($created_date);
            $expiry_date = Carbon::parse($created_date)->addDays(15);

            if($diffdays > 13){
                //refresh token
                // Content
                $content = array(
                    "grant_type" => "refresh_token",
                    "refresh_token" => $refreshToken,
                );

                // Connect API
                $opts = array("http" =>
                    array(
                        "method" => "POST",
                        "header" => $header,
                        "content" => http_build_query($content),
                        "ignore_errors" => true,
                    )
                );

                $url = "https://account.accurate.id/oauth/token";

                $context  = stream_context_create($opts);
                $response = file_get_contents($url, false, $context);
                //print_r($response);
                // Output
                $json = json_decode($response);

                if(isset($json->access_token)){
                    $accessToken = $json->{"access_token"};
                    $refreshToken = $json->{"refresh_token"};

                    //Update to DB
                    $accurate = AccurateAuth::find(1);
                    $accurate->access_token = $accessToken;
                    $accurate->save();

                    echo "<div>OAuth Refresh Token</div>";
                    echo "<ul>";
                    echo "<li>Created: $created_date</li>";
                    echo "<li>Diff: $diffdays</li>";
                    echo "<li>Expiry: $expiry_date</li>";
                    echo "<li>access_token = $accessToken</li>";
                    echo "<li>refresh_token = $refreshToken</li>";

                    /* if($openDB){
                        echo "<li>Session = ". $openDB['session'] ."</li>";
                        echo "<li>Host = ". $openDB['host'] ."</li>";
                        $items = json_decode($this->accurate_check_items($openDB));
                        //echo "<li>Items = ". $items ."</li>";
                        if($items){
                            foreach ($items->d as $key => $value) {
                                echo "<li>Item = ". $value->id ." : ".$value->no."</li>";
                            }
                        }
                    } */
                    echo "</ul>";
                }else{
                    echo "<div>OAuth no refresh token</div>";
                    echo "<ul>";
                    echo "<li>Created: $created_date</li>";
                    echo "<li>Diff: $diffdays</li>";
                    echo "<li>Expiry: $expiry_date</li>";
                    echo "<li>access_token = $accessToken</li>";
                    echo "<li>refresh_token = $refreshToken</li>";
                    /* if($openDB){
                        echo "<li>Session = ". $openDB['session'] ."</li>";
                        echo "<li>Host = ". $openDB['host'] ."</li>";
                        $items = json_decode($this->accurate_check_items($openDB));
                        //echo "<li>Items = ". $items ."</li>";
                        if($items){
                            foreach ($items->d as $key => $value) {
                                echo "<li>Item = ". $value->id ." : ".$value->no."</li>";
                            }
                        }
                    } */
                    echo "</ul>";
                }

            }else{

                echo "<div>OAuth re-used token</div>";
                echo "<ul>";
                echo "<li>Created: $created_date</li>";
                echo "<li>Diff: $diffdays</li>";
                echo "<li>Expiry: $expiry_date</li>";
                echo "<li>access_token = $accessToken</li>";
                echo "<li>refresh_token = $refreshToken</li>";
                echo "<li>Open DB = Open</li>";

                if($openDB){
                    echo "<li>Session = ". $openDB['session'] ."</li>";
                    echo "<li>Host = ". $openDB['host'] ."</li>";
                    $items = json_decode($this->accurate_check_items($openDB));
                    //echo "<li>Items = ". $items ."</li>";
                    /* if($items){
                        foreach ($items->d as $key => $value) {
                            echo "<li>Item = ". $value->id ." : ".$value->no."</li>";
                        }
                    } */

                    /* $member = json_decode($this->accurate_check_member($openDB));

                    if($member){
                        foreach ($member->d as $key => $value) {
                            echo "<li>Member = ". $value->name ." | ". $value->customerNo ."</li>";
                        }
                    } */
                }
                echo "</ul>";
            }
        }


    }

    public function open_db($id, $token){  //moved to Accurate Helper
        $accessToken = $token;
        $openDB = Accurate::AccurateOpenDB($id, $token );
        dd($openDB);
        /* // Header
        $header = array(
            "Authorization: Bearer $accessToken"
        );

        // URL
        $url = "https://account.accurate.id/api/open-db.do?id=" . $id;

        // Connect API
        $opts = array("http" =>
            array(
                "method" => "GET",
                "header" => $header,
                "ignore_errors" => true,
            )
        );
        $context  = stream_context_create($opts);
        $response = json_decode(file_get_contents($url, false, $context));

        // Output
        if(isset($response->session)){
            $session = $response->session;
            $host = $response->host;
            $data = [
                'session' => $response->session,
                'host' => $response->host,
                'access_token' => $accessToken,
            ];
            //$menuUrl = "menu.php?access_token=$accessToken&session=$session&host=$host";
            //return $data;
            dd($data);
        }else{
            print_r($response);
            //return false;
        } */

    }

    public function accurate_check_items($obj){
        $accessToken = $obj["access_token"];
        $session = $obj["session"];
        $host = $obj["host"];

        // Header
        $header = array(
            "Authorization: Bearer $accessToken",
            "X-SESSION-ID: $session"
        );
        // Connect API
        $opts = array("http" =>
            array(
                "method" => "GET",
                "header" => $header,
                "ignore_errors" => true,
            )
        );
        $context  = stream_context_create($opts);

        // Content
        $content = array(
            "fields" => "id,no,name,itemType",
            "sp.pageSize"=>200,
            "filter.itemType" => "SERVICE",
            "filter.keywords" => "MM"

        );

        // URL
        $url = $host . "/accurate/api/item/list.do?" . http_build_query($content);

        $result = file_get_contents($url, false, $context);

        return $result;
    }

    public function accurate_check_member($obj){
        $accessToken = $obj["access_token"];
        $session = $obj["session"];
        $host = $obj["host"];

        // Header
        $header = array(
            "Authorization: Bearer $accessToken",
            "X-SESSION-ID: $session"
        );
        // Connect API
        $opts = array("http" =>
            array(
                "method" => "GET",
                "header" => $header,
                "ignore_errors" => true,
            )
        );
        $context  = stream_context_create($opts);

        // Content
        $content = array(
            "fields" => "id,name,customerNo",
            "keywords" => "Rom Gomez"
        );

        // URL
        $url = $host . "/accurate/api/customer/list.do?" . http_build_query($content);


        $result = file_get_contents($url, false, $context);

        //echo $result;
        return $result;

    }

//    public function show_index($id, $template)
//    {
//        if($template == 1){
//            $user = User::findOrFail($id);
//            return view('views_member.template-1', compact('user'));
//        }else if($template == 2){
//            $user = User::findOrFail($id);
//            return view('views_member.template-2', compact('user'));
//        }else if($template == 3){
//            $user = User::findOrFail($id);
//            return view('views_member.template-3', compact('user'));
//        }
//    }
}
