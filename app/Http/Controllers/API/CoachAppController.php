<?php

namespace App\Http\Controllers\API;

use App\AutoNumber;
use App\Http\Controllers\Controller;
use App\Model\Appointment\GuestAppointment;
use App\Model\Appointment\GuestAppointmentStatus;
use App\Model\Appointment\GuestAppointmentNote;
use App\Model\Appointment\MemberAppointmentStatus;
use App\Model\CoachDailyReport\CoachDailyReportArDetail;
use App\Model\CoachDailyReport\CoachDailyReportHeader;
use App\Model\CoachDailyReport\CoachDailyReportSalesDetail;
use App\Model\Guest\BuddyReferralLead;
use App\Model\Appointment\MemberAppointment;
use App\Model\Club\ClubOpenHour;
use App\Model\Event\GlobalEvent;
use App\Model\Guest\GuestMonitoring;
use App\Model\Guest\GuestMonitoringDetail;
use App\Model\GuestMembership\GuestMemberPayment;
use App\Model\Locker\LockerPayment;
use App\Model\Log\LogContactedLead;
use App\Model\Log\LogContactedNewLead;
use App\Model\Log\LogStaffPointAcquisition;
use App\Model\MasterData\CoachDailyReportCategory;
use App\Model\MasterData\MembershipType;
use App\Model\MasterData\MemberSource;
use App\Model\MasterData\Point;
use App\Model\MasterData\TargetCategory;
use App\Model\Member\MemberPersonal;
use App\Model\Member\MIAMonitoring;
use App\Model\Member\StaffReport;
use App\Model\Payment\MemberPayment;
use App\Model\Product\ProductPayment;
use App\Model\Product\ProductPaymentDetail;
use App\Model\WM\WeighMeasure;
use App\Model\WM\WMRank;
use App\Model\WM\Xfactor;
use App\Model\Workout\Workout;
use App\Model\MasterData\ThirtyDay;
use App\Model\Member\MemberThirtyDay;
use App\Model\Curves\CurvesLearning\ClCategory;
use App\Model\Curves\CurvesLearning\ClCoachAnswer;
use App\Model\Curves\CurvesLearning\ClCoachPas;
use App\Model\Curves\CurvesLearning\ClMaterialContent;
use App\Model\Curves\CurvesLearning\ClMaterialHeader;
use App\Model\Curves\CurvesLearning\ClMaterialQuestion;
use App\Model\Curves\CurvesLearning\ClMaterialSupport;
use App\Model\Staff\CoachTarget;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Support\Facades\DB;

class CoachAppController extends Controller
{
    public $successStatus = 200;
    public $failedStatus = 500;

    public function coach_profile() {
        $user = Auth::user();

        if (!empty($user->user_memberpersonal->photo)):
            $user->setAttribute('photo', $user->user_memberpersonal->photo);
        else:
            $user->setAttribute('photo', '');
        endif;

        $user->setAttribute('keytag', $user->user_memberpersonal->key_tag_id);
        $user->setAttribute('role', $user->user_role->role);
        $user->user_club;

        return response()->json(['profile' => $user], $this->successStatus);
    }

    public function member_data(Request $request) {
        $user = Auth::user();

        if (!empty($request->name)):
            $members = User::where('club_id', '=', $user->club_id)
                ->where('name', 'ilike', '%'.$request->name.'%')
                ->orderBy('name', 'asc')
                ->get();
        else:
            $members = User::where('role_id', '=', '7')
                ->whereHas('user_memberstatus', function ($q) {
                    $q->where('status', '=', 'Active')
                        ->orWhere('status', '=', 'Hold');
                })
                ->whereHas('memberuser_coachmember', function ($r) use ($user) {
                    $r->where('coach_id', '=', $user->id);
                })->whereHas('user_memberpayment')
                ->orderBy('name', 'asc')
                ->get();
        endif;

        foreach ($members as $member):
            if (!empty($member->user_memberpersonal->photo)):
                $member->setAttribute('photo', 'member_photos/'.$member->user_memberpersonal->photo);
            else:
                $member->setAttribute('photo', 'img.png');
            endif;

            $brlmember = BuddyReferralLead::findOrFail($member->user_memberpersonal->guest_id);

            $member->setAttribute('whatsapp',$brlmember->wa_number);

            //Check FWO
            $workout = Workout::where('user_id', '=', $member->id)
                ->orderBy('workout_date', 'asc')
                ->first();

            if ($workout != null):
                $member->setAttribute('fwo', Carbon::parse($workout->workout_date)->format('d M Y'));

                //Check 30 days challenge
                $fwo = Carbon::parse($workout->workout_date);
                $diffdays = $fwo->diffInDays(Carbon::now());

                if ($diffdays <= 30):
                    $member->setAttribute('thirty', $diffdays);
                else:
                    $member->setAttribute('thirty', 0);
                endif;
            else:
                $member->setAttribute('fwo', '');
                $member->setAttribute('thirty', 0);
            endif;

            $is_debt = 'no';

            foreach ($member->user_memberpayment as $payment ) {
                if($payment->payment_date < Carbon::now() && $payment->payment_status!='Paid'){
                    $is_debt = 'yes';
                }
            }

            if($is_debt == 'yes'){
                $member->setAttribute('debt_member','yes');
            }else{
                $member->setAttribute('debt_member','no');
            }

            $member->setAttribute('sk_member', $member->user_memberstatus->status);

            $member->setAttribute('flag_member', $member->user_memberpersonal->flag);
        endforeach;

        return response()->json(['member' => $members], $this->successStatus);
//        return response()->json(/*'member' => */$members, $this->successStatus);
    }

    public function member_detail($id) {
        $user = Auth::user();

        $from_date = Carbon::parse(Carbon::now())->startOfWeek();
        $thru_date = Carbon::parse(Carbon::now())->endOfWeek();

        $profile = User::findOrFail($id);
//        $profiles = User::where('id', '=', $id)->get();

//        foreach ($profiles as $profile):
            if (!empty($profile->user_memberpersonal)):
                $profile->setAttribute('address', $profile->user_memberpersonal->address);
                $profile->setAttribute('phone', $profile->user_memberpersonal->phone);

                if (!empty($profile->user_memberpersonal->photo)):
                    $profile->setAttribute('photo', 'member_photos/'.$profile->user_memberpersonal->photo);
                else:
                    $profile->setAttribute('photo', 'img.png');
                endif;

                $last_wo = Workout::where('user_id', '=', $id)->orderBy('workout_date', 'desc')->first();
                if (!empty($last_wo)):
                    $profile->setAttribute('last_wo', Carbon::parse($last_wo->workout_date)->format('d M Y'));
                else:
                    $profile->setAttribute('last_wo', '-');
                endif;

                $totalwo = Workout::where('user_id', '=', $id)->whereDate('workout_date', '>=', Carbon::parse($from_date)->toDateString())->whereDate('workout_date', '<=', Carbon::parse($thru_date)->toDateString())->get()->count();
                if ($totalwo > 0) {
                    $profile->setAttribute('total_wo', $totalwo);
                } else {
                    $profile->setAttribute('total_wo', 0);
                }
            endif;
//        endforeach;

        $profile->user_memberstatus;

        return response()->json($profile, $this->successStatus);
    }

    public function member_wo_detail($id){
        $user = Auth::user();

        $wos = Workout::selectRaw('user_id, workout_date, extract(month from workout_date) as month, extract(year from workout_date) as year, club_id, club_infos.club_code')->where('user_id','=',$id)->whereHas('workout_user')->join('club_infos',function($join){
            $join->on('workouts.club_id','=','club_infos.id');
        })->orderBy('workout_date','ASC')->get();

        $month_count = 0;
        $curmonth = 0;

        foreach ($wos as $wo) {
            $wo->setAttribute('moname', Carbon::parse($wo->workout_date)->format('F'));
            $wo->setAttribute('daydate', Carbon::parse($wo->workout_date)->format('d'));
        }

        return response()->json(['wos' => $wos], $this->successStatus);
    }

    public function member_wo_main($id){
        $user = Auth::user();

        $womos = Workout::selectRaw('COUNT(user_id) as jumlah, extract(month from workout_date) as month, extract(year from workout_date) as year')
            ->where('user_id','=',$id)
            ->groupBy('year')
            ->groupBy('month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

//        return response()->json($womos, $this->successStatus);

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
//                $wo->setAttribute('moname', Carbon::parse('01-'.sprintf('%02d', $temp_month).'-'.$temp_year)->format('F'));
//                $wo->setAttribute('month', sprintf('%02d', $temp_month));
//                $wo->setAttribute('sort', $temp_year.sprintf('%02d', $temp_month));
            if($wo->jumlah >= 12){
                $wo->setAttribute('percent','100');
            }else{
                $percent = round(($wo->jumlah / 12)*100, 2);
                $wo->setAttribute('percent', ''.$percent);
            }

            $temp_x++;
        }

        $womos = $womos->sortByDesc('sort')->values();
//        $womos = $womos->values();

//        return response()->json(['womos' => $womos]);
        return response()->json($womos, $this->successStatus);
    }

    public function member_wo_chart($id){
        $user = Auth::user();

        $womos = Workout::selectRaw('COUNT(user_id) as jumlah, extract(month from workout_date) as month, extract(year from workout_date) as year')
            ->where('user_id','=',$id)
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
//                $wo->setAttribute('moname', Carbon::parse('01-'.sprintf('%02d', $temp_month).'-'.$temp_year)->format('F'));
//                $wo->setAttribute('month', sprintf('%02d', $temp_month));
//                $wo->setAttribute('sort', $temp_year.sprintf('%02d', $temp_month));
            if($wo->jumlah >= 12){
                $wo->setAttribute('percent','100');
            }else{
                $percent = round(($wo->jumlah / 12)*100, 2);
                $wo->setAttribute('percent', ''.$percent);
            }

            $temp_x++;
        }

        $womos = $womos->sortBy('sort')->values();

//        return response()->json(['womos' => $womos]);
        return response()->json($womos, $this->successStatus);
    }
	
	public function member_wm_list($id) {
        $user = Auth::user();

        $wms = WeighMeasure::where('user_id', '=', $id)
            ->orderBy('wm_date', 'desc')
            ->get();

        foreach ($wms as $wm):
            $wm->setAttribute('wmdate', Carbon::parse($wm->wm_date)->format('d M Y'));
            $wm->setAttribute('wmday',Carbon::parse($wm->wm_date)->format('d'));
            $wm->setAttribute('wmmonth',Carbon::parse($wm->wm_date)->format('M'));
            $wm->setAttribute('wmyear',Carbon::parse($wm->wm_date)->format('Y'));
        endforeach;

//        return response()->json(['wm' => $wms], $this->successStatus);
        return response()->json($wms, $this->successStatus);
    }

    public function wm_detail($id) {
        $user = Auth::user();

        $wm = WeighMeasure::findOrFail($id);
//        $wms = WeighMeasure::where('id', '=', $id)->get();
//        foreach ($wms as $wm):
        $wm->setAttribute('date', Carbon::parse($wm->wm_date)->format('d M Y'));
        $member = User::findOrFail($wm->user_id);
        $wm->setAttribute('name', $member->name);

        if (!empty($member->user_memberpersonal->photo)):
            $wm->setAttribute('photo', 'member_photos/'.$member->user_memberpersonal->photo);
        else:
            $wm->setAttribute('photo', 'img.png');
        endif;

        //Check previous WM
        $check_wms = WeighMeasure::where('user_id', '=', $member->id)
            ->where('id', '<', $wm->id)
            ->orderBy('wm_date', 'desc')
            ->take(1)
            ->get();

        $s_weight = 0;
        $s_measurement = 0;
        $s_bodyfat = 0;
        $s_bodywater = 0;
        if ($check_wms != null):
            foreach ($check_wms as $check_wm):
                if (($wm->weight - $check_wm->weight) != 0):
                    $s_weight = $wm->weight - $check_wm->weight;
                endif;
                if (($wm->total_measurement - $check_wm->total_measurement) != 0):
                    $s_measurement = $wm->total_measurement - $check_wm->total_measurement;
                endif;
                if (($wm->body_fat - $check_wm->body_fat) != 0):
                    $s_bodyfat = $wm->body_fat - $check_wm->body_fat;
                endif;
                if (($wm->body_water - $check_wm->body_water) != 0):
                    $s_bodywater = $wm->body_water - $check_wm->body_water;
                endif;
            endforeach;
        endif;

        $wm->setAttribute('s_weight', $s_weight);
        $wm->setAttribute('s_measurement', $s_measurement);
        $wm->setAttribute('s_bodyfat', $s_bodyfat);
        $wm->setAttribute('s_bodywater', $s_bodywater);

        //Check First WM
        $check_first_wms = WeighMeasure::where('user_id', '=', $member->id)
            ->where('id', '<', $wm->id)
            ->orderBy('wm_date', 'asc')
            ->take(1)
            ->get();

        $f_weight = 0;
        $f_measurement = 0;
        $f_bodyfat = 0;
        $f_bodywater = 0;
        if ($check_first_wms != null):
            foreach ($check_first_wms as $check_wm):
                if (($wm->weight - $check_wm->weight) != 0):
                    $f_weight = $wm->weight - $check_wm->weight;
                endif;
                if (($wm->total_measurement - $check_wm->total_measurement) != 0):
                    $f_measurement = $wm->total_measurement - $check_wm->total_measurement;
                endif;
                if (($wm->body_fat - $check_wm->body_fat) != 0):
                    $f_bodyfat = $wm->body_fat - $check_wm->body_fat;
                endif;
                if (($wm->body_water - $check_wm->body_water) != 0):
                    $f_bodywater = $wm->body_water - $check_wm->body_water;
                endif;
            endforeach;
        endif;

        $wm->setAttribute('f_weight', $f_weight);
        $wm->setAttribute('f_measurement', $f_measurement);
        $wm->setAttribute('f_bodyfat', $f_bodyfat);
        $wm->setAttribute('f_bodywater', $f_bodywater);

        //Check 3x per week WO for last 1 month
        $workouts = Workout::selectRaw('COUNT(user_id) as jumlah, extract(week from workout_date) as week, extract(year from workout_date) as year')
            ->where('user_id','=',$wm->user_id)
            ->whereDate('workout_date', '<=', Carbon::parse($wm->wm_date)->toDateString())
            ->whereDate('workout_date', '>=', Carbon::parse($wm->wm_date)->subWeeks(4)->toDateString())
            ->groupBy('year')
            ->groupBy('week')
            ->orderBy('year', 'desc')
            ->orderBy('week', 'desc')
            ->get();

        $avg = 0;
        $weeks = 0;
        foreach ($workouts as $workout):
            $avg += $workout->jumlah;
            $weeks++;
        endforeach;

        if ($weeks > 0):
            if (($avg / $weeks) >= 3):
                $three = 'Yes';
            else:
                $three = 'No';
            endif;
        else:
            $three = 'No';
        endif;

        $wm->setAttribute('three_times_a_week', $three);
//        endforeach;

//        return response()->json(['wms' => $wms], $this->successStatus);
        return response()->json($wm, $this->successStatus);
    }

    public function wm_detail_chart($id, $title) {
        $user = Auth::user();
        $wms = WeighMeasure::where('user_id', '=', $id);

        if ($title == 'weight'):
            $wms->select('weight', 'wm_date');
        elseif ($title == 'bodyfat'):
            $wms->select('total_body_fat', 'wm_date');
        elseif ($title == 'bodywater'):
            $wms->select('total_hydration', 'wm_date');
        elseif ($title == 'measurement'):
            $wms->select('total_measurement', 'wm_date');
        endif;

        $wms = $wms->orderBy('wm_date', 'desc')->get();

        return response()->json($wms, $this->successStatus);
    }

    public function keytag(){
        $user = Auth::user();

        return response()->json(['qr' => $user->user_memberpersonal->key_tag_id], $this->successStatus);
    }

    public function member_thirty_profile_main($id) {
        $user = Auth::user();

        $member_thirty = MemberThirtyDay::selectRaw('user_id, menu, sequence, result, mtd.id as thirty_id')->rightJoin('mst_thirty_days as mtd', function($join) use ($id){
                $join->on('member_thirty_days.thirty_days_id','=','mtd.id')->where('member_thirty_days.user_id','=',$id);
            })->orderBy('mtd.sequence','ASC')->get();

        return response()->json(['memberthirty'=>$member_thirty], $this->successStatus);
    }

    public function member_add_thirty($id, Request $request){

        $memberthirty = new MemberThirtyDay;
        $memberthirty->user_id = $request->user_id;
        $memberthirty->thirty_days_id = $request->thirty_days_id;
        $memberthirty->result = $request->result;
        $memberthirty->staff_id = $request->staff_id;
        
        if($memberthirty->save()){
            return response()->json(['success' => true, 'user' => 'SUCCESS'], $this->successStatus);
        }else{
            return response()->json(['success'=>false,'user'=>'FAILURE'],$this->successStatus);
        }
    }

    public function mini_member_data() {
        $user = Auth::user();

        $members = User::where('role_id', '=', '7')
            ->whereHas('user_memberstatus', function ($q) {
                $q->where('status', '<>', 'Inactive')
                    ->orWhere('status', '<>', 'Stopped');
            })
            ->whereHas('memberuser_coachmember', function ($r) use ($user) {
                $r->where('coach_id', '=', $user->id);
            })
            ->orderBy('name', 'asc')
            ->get();

        foreach ($members as $member):
            if (!empty($member->user_memberpersonal->photo)):
                $member->setAttribute('photo', 'member_photos/'.$member->user_memberpersonal->photo);
            else:
                $member->setAttribute('photo', 'img.png');
            endif;
        endforeach;

        return response()->json(['member' => $members], $this->successStatus);
    }

    public function check(Request $request) {
        $member_list = json_decode($request->checklist, true);
        $message = $request->message;
        $sender = '62'.substr('8112266977', 1);
        $counter = 0;

        foreach ($member_list as $member):
            if ($member['check'] == true):
                $the_member = User::findOrFail($member['memberId']);

                if (!empty($the_member->user_memberpersonal->memberpersonal_buddyreferrallead->wa_number)):
                    //Update Auto Number
                    $temp_auto_number = AutoNumber::findOrFail('4');
                    $msg_number = $temp_auto_number->current_number;
                    $temp_auto_number->current_number = $temp_auto_number->current_number + 1;
                    $temp_auto_number->save();

                    $wa_numbers = '62'.substr($the_member->user_memberpersonal->memberpersonal_buddyreferrallead->wa_number, 1);
                    //Send Message
//                    $response = Curl::to('https://www.waboxapp.com/api/send/chat')
//                        ->withData(array('token' => 'ba830f3e8cc80603e164bb46bd07dc015ab4826b5aff7', 'uid' => $sender, 'to' => $wa_numbers, 'custom_uid' => 'msg-'.sprintf('%08d', $msg_number), 'text' => $message))
//                        ->get();

                    $counter++;
                endif;
            endif;
        endforeach;

        if ($counter > 0):
            return response()->json(['success' => true, 'status' => 'sent'], $this->successStatus);
        else:
            return response()->json(['success' => true, 'status' => 'notsent'], $this->successStatus);
        endif;
    }

    //Coach Performance
    public function coach_performance(Request $request) {
        $from_date = new Carbon('first day of this month');
        $thru_date = new Carbon('last day of this month');

        //APO
        $gp_today = GuestAppointment::where('staff_id', '=', Auth::id())
            ->whereDate('fa_date', '=', Carbon::today()->toDateString())
            ->get()
            ->count();

        $gp_month = GuestAppointment::where('staff_id', '=', Auth::id())
            ->whereDate('fa_date', '>=', Carbon::parse($from_date)->toDateString())
            ->whereDate('fa_date', '<=', Carbon::parse($thru_date)->toDateString())
            ->get()
            ->count();

        $gp_all = GuestAppointment::where('staff_id', '=', Auth::id())
            ->get()
            ->count();

        //Club Avg APO
        $avg_gp_today = GuestAppointment::selectRaw('COUNT(id) as total, COUNT(DISTINCT staff_id) as staff')
            ->where('club_id', '=', Auth::user()->club_id)
            ->whereDate('fa_date', '=', Carbon::today()->toDateString())
            ->first();

        $avg_gp_month = GuestAppointment::selectRaw('COUNT(id) as total, COUNT(DISTINCT staff_id) as staff')
            ->where('club_id', '=', Auth::user()->club_id)
            ->whereDate('fa_date', '>=', Carbon::parse($from_date)->toDateString())
            ->whereDate('fa_date', '<=', Carbon::parse($thru_date)->toDateString())
            ->first();

        $avg_gp_all = GuestAppointment::selectRaw('COUNT(id) as total, COUNT(DISTINCT staff_id) as staff')
            ->where('club_id', '=', Auth::user()->club_id)
            ->first();

        //Kehadiran
        $show_today = GuestAppointment::where('staff_id', '=', Auth::id())
            ->whereHas('guestappointment_gastatus', function ($q) {
                $q->whereDate('s_date', '=', Carbon::today()->toDateString());
            })->get()->count();

        $show_month = GuestAppointment::where('staff_id', '=', Auth::id())
            ->whereHas('guestappointment_gastatus', function ($q) use ($from_date, $thru_date) {
                $q->whereDate('s_date', '>=', Carbon::parse($from_date)->toDateString())
                    ->whereDate('s_date', '<=', Carbon::parse($thru_date)->toDateString());
            })->get()->count();

        $show_all = GuestAppointment::where('staff_id', '=', Auth::id())
            ->whereHas('guestappointment_gastatus', function ($q) {
                $q->whereNotNull('s_date');
            })->get()->count();

        $show_today_percent = 0;
        $show_month_percent = 0;
        $show_all_percent = 0;

        if (!empty($gp_today)):
//            $show_today_percent = number_format(($show_today / $gp_today * 100) , 2 , '.' , ',');
            $show_today_percent = round($show_today / $gp_today * 100);
        endif;

        if (!empty($gp_month)):
//            $show_month_percent = number_format(($show_month / $gp_month * 100) , 2 , '.' , ',');
            $show_month_percent = round($show_month / $gp_month * 100);
        endif;

        if (!empty($gp_all)):
//            $show_all_percent = number_format(($show_all / $gp_all * 100) , 2 , '.' , ',');
            $show_all_percent = round($show_all / $gp_all * 100);
        endif;

        //Club Avg Kehadiran
        $avg_show_today = GuestAppointment::selectRaw('COUNT(id) as total, COUNT(DISTINCT staff_id) as staff')
            ->where('club_id', '=', Auth::user()->club_id)
            ->whereHas('guestappointment_gastatus', function ($q) {
                $q->whereDate('s_date', '=', Carbon::today()->toDateString());
            })->first();

        $avg_show_month = GuestAppointment::selectRaw('COUNT(id) as total, COUNT(DISTINCT staff_id) as staff')
            ->where('club_id', '=', Auth::user()->club_id)
            ->whereHas('guestappointment_gastatus', function ($q) use ($from_date, $thru_date) {
                $q->whereDate('s_date', '>=', Carbon::parse($from_date)->toDateString())
                    ->whereDate('s_date', '<=', Carbon::parse($thru_date)->toDateString())
                    ->where('status_id', '>=', '4');
            })->first();

        $avg_show_all = GuestAppointment::selectRaw('COUNT(id) as total, COUNT(DISTINCT staff_id) as staff')
            ->where('club_id', '=', Auth::user()->club_id)
            ->whereHas('guestappointment_gastatus', function ($q) {
                $q->whereNotNull('s_date');
            })->first();

        //Sales
        $sales_today = GuestAppointment::where('staff_id', '=', Auth::id())
            ->whereHas('guestappointment_gastatus', function ($q) {
                $q->whereDate('sa_date', '=', Carbon::today()->toDateString())
                    ->where('status_id', '=', '5');
            })->get()->count();

        $sales_month = GuestAppointment::where('staff_id', '=', Auth::id())
            ->whereHas('guestappointment_gastatus', function ($q) use ($from_date, $thru_date) {
                $q->whereDate('sa_date', '>=', Carbon::parse($from_date)->toDateString())
                    ->whereDate('sa_date', '<=', Carbon::parse($thru_date)->toDateString())
                    ->where('status_id', '=', '5');
            })->get()->count();

        $sales_all = GuestAppointment::where('staff_id', '=', Auth::id())
            ->whereHas('guestappointment_gastatus', function ($q) {
                $q->where('status_id', '=', '5');
            })->get()->count();

        $sales_today_percent = 0;
        $sales_month_percent = 0;
        $sales_all_percent = 0;

        if (!empty($show_today)):
//            $sales_today_percent = number_format(($sales_today / $show_today * 100) , 2 , '.' , ',');
            $sales_today_percent = round($sales_today * 100 / $show_today);
        endif;

        if (!empty($show_month)):
//            $sales_month_percent = number_format(($sales_month / $show_today * 100) , 2 , '.' , ',');
            $sales_month_percent = round($sales_month * 100 / $show_month);
        endif;

        if (!empty($show_all)):
//            $sales_all_percent = number_format(($sales_all / $show_all * 100) , 2 , '.' , ',');
            $sales_all_percent = round($sales_all * 100 / $show_all);
        endif;

        //Club Avg Sales
        $avg_sales_today = GuestAppointment::selectRaw('COUNT(id) as total, COUNT(DISTINCT staff_id) as staff')
            ->where('club_id', '=', Auth::user()->club_id)
            ->whereHas('guestappointment_gastatus', function ($q) {
                $q->whereDate('sa_date', '=', Carbon::today()->toDateString())
                    ->where('status_id', '=', '5');
            })->first();

        $avg_sales_month = GuestAppointment::selectRaw('COUNT(id) as total, COUNT(DISTINCT staff_id) as staff')
            ->where('club_id', '=', Auth::user()->club_id)
            ->whereHas('guestappointment_gastatus', function ($q) use ($from_date, $thru_date) {
                $q->whereDate('sa_date', '>=', Carbon::parse($from_date)->toDateString())
                    ->whereDate('sa_date', '<=', Carbon::parse($thru_date)->toDateString())
                    ->where('status_id', '=', '5');
            })->first();

        $avg_sales_all = GuestAppointment::selectRaw('COUNT(id) as total, COUNT(DISTINCT staff_id) as staff')
            ->where('club_id', '=', Auth::user()->club_id)
            ->whereHas('guestappointment_gastatus', function ($q) {
                $q->where('status_id', '=', '5');
            })->first();

        //Contact
        $contacts_all = LogContactedNewLead::where('staff_id', '=', Auth::id())
            ->get()->count();

        $contacts_month = LogContactedNewLead::where('staff_id', '=', Auth::id())
            ->whereDate('created_at', '>=', Carbon::parse($from_date)->toDateString())
            ->whereDate('created_at', '<=', Carbon::parse($thru_date)->toDateString())
            ->get()->count();

        $contacts_today = LogContactedNewLead::where('staff_id', '=', Auth::id())
            ->whereDate('created_at', '=', Carbon::now()->toDateString())
            ->get()->count();

        //Club Avg Contact
        $avg_contacts_all = LogContactedNewLead::selectRaw('COUNT(id) as total, COUNT(DISTINCT staff_id) as staff')
            ->whereHas('logcontactednewlead_user', function ($q) {
                $q->where('club_id', '=', Auth::user()->club_id);
            })
            ->first();

        $avg_contacts_month = LogContactedNewLead::selectRaw('COUNT(id) as total, COUNT(DISTINCT staff_id) as staff')
            ->whereHas('logcontactednewlead_user', function ($q) {
                $q->where('club_id', '=', Auth::user()->club_id);
            })
            ->whereDate('created_at', '>=', Carbon::parse($from_date)->toDateString())
            ->whereDate('created_at', '<=', Carbon::parse($thru_date)->toDateString())
            ->first();

        $avg_contacts_today = LogContactedNewLead::selectRaw('COUNT(id) as total, COUNT(DISTINCT staff_id) as staff')
            ->whereHas('logcontactednewlead_user', function ($q) {
                $q->where('club_id', '=', Auth::user()->club_id);
            })
            ->whereDate('created_at', '=', Carbon::now()->toDateString())
            ->first();

        //Reconfirmation
//        $reconfirms_all = LogContactedLead::where('staff_id', '=', Auth::id())
//            ->get()->count();
//
//        $reconfirms_month = LogContactedLead::where('staff_id', '=', Auth::id())
//            ->whereDate('created_at', '>=', Carbon::parse($from_date)->toDateString())
//            ->whereDate('created_at', '<=', Carbon::parse($thru_date)->toDateString())
//            ->get()->count();
//
//        $reconfirms_today = LogContactedLead::where('staff_id', '=', Auth::id())
//            ->whereDate('created_at', '=', Carbon::now()->toDateString())
//            ->get()->count();

        //Club Avg Reconfirmation
//        $avg_reconfirms_all = LogContactedLead::selectRaw('COUNT(id) as total, COUNT(DISTINCT staff_id) as staff')
//            ->whereHas('logcontactedlead_user', function ($q) {
//                $q->where('club_id', '=', Auth::user()->club_id);
//            })
//            ->first();
//
//        $avg_reconfirms_month = LogContactedLead::selectRaw('COUNT(id) as total, COUNT(DISTINCT staff_id) as staff')
//            ->whereHas('logcontactedlead_user', function ($q) {
//                $q->where('club_id', '=', Auth::user()->club_id);
//            })
//            ->whereDate('created_at', '>=', Carbon::parse($from_date)->toDateString())
//            ->whereDate('created_at', '<=', Carbon::parse($thru_date)->toDateString())
//            ->first();
//
//        $avg_reconfirms_today = LogContactedLead::selectRaw('COUNT(id) as total, COUNT(DISTINCT staff_id) as staff')
//            ->whereHas('logcontactedlead_user', function ($q) {
//                $q->where('club_id', '=', Auth::user()->club_id);
//            })
//            ->whereDate('created_at', '=', Carbon::now()->toDateString())
//            ->first();

        $data = [
            'gp_today' => $gp_today,
            'gp_month' => $gp_month,
            'gp_total' => $gp_all,
            'show_today' => $show_today,
            'show_month' => $show_month,
            'show_all' => $show_all,
            'show_today_percent' => $show_today_percent,
            'show_month_percent' => $show_month_percent,
            'show_all_percent' => $show_all_percent,
            'sales_today' => $sales_today,
            'sales_month' => $sales_month,
            'sales_all' => $sales_all,
            'sales_today_percent' => $sales_today_percent,
            'sales_month_percent' => $sales_month_percent,
            'sales_all_percent' => $sales_all_percent,
            'contact_today' => $contacts_today,
            'contact_month' => $contacts_month,
            'contact_all' => $contacts_all,
//            'reconfirm_today' => $reconfirms_today,
//            'reconfirm_month' => $reconfirms_month,
//            'reconfirm_all' => $reconfirms_all,
            'avg_gp_today' => (!empty($avg_gp_today->staff) ? round($avg_gp_today->total / $avg_gp_today->staff) : 0),
            'avg_gp_month' => (!empty($avg_gp_month->staff) ? round($avg_gp_month->total / $avg_gp_month->staff) : 0),
            'avg_gp_all' => (!empty($avg_gp_all->staff) ? round($avg_gp_all->total / $avg_gp_all->staff) : 0),
            'avg_show_today' => (!empty($avg_show_today->staff) ? round($avg_show_today->total / $avg_show_today->staff) : 0),
            'avg_show_month' => (!empty($avg_show_month->staff) ? round($avg_show_month->total / $avg_show_month->staff) : 0),
            'avg_show_all' => (!empty($avg_show_all->staff) ? round($avg_show_all->total / $avg_show_all->staff) : 0),
            'avg_show_today_percent' => (!empty($avg_show_today->staff) ? round(($avg_show_today->total / $avg_show_today->staff) / (!empty($avg_gp_today->staff) ? round($avg_gp_today->total / $avg_gp_today->staff) : 1) * 100) : 0),
            'avg_show_month_percent' => (!empty($avg_show_month->staff) ? round(($avg_show_month->total / $avg_show_month->staff) / (!empty($avg_gp_month->staff) ? round($avg_gp_month->total / $avg_gp_month->staff) : 1) * 100) : 0),
            'avg_show_all_percent' => (!empty($avg_show_all->staff) ? round(($avg_show_all->total / $avg_show_all->staff) / (!empty($avg_gp_all->staff) ? round($avg_gp_all->total / $avg_gp_all->staff) : 1) * 100) : 0),
            'avg_sales_today' => (!empty($avg_sales_today->staff) ? round($avg_sales_today->total / $avg_sales_today->staff) : 0),
            'avg_sales_month' => (!empty($avg_sales_month->staff) ? round($avg_sales_month->total / $avg_sales_month->staff) : 0),
            'avg_sales_all' => (!empty($avg_sales_all->staff) ? round($avg_sales_all->total / $avg_sales_all->staff) : 0),
            'avg_sales_today_percent' => (!empty($avg_sales_today->staff) ? round(($avg_sales_today->total / $avg_sales_today->staff) / (!empty($avg_show_today->staff) ? round($avg_show_today->total / $avg_show_today->staff) : 1) * 100) : 0),
            'avg_sales_month_percent' => (!empty($avg_sales_month->staff) ? round(($avg_sales_month->total / $avg_sales_month->staff) / (!empty($avg_show_month->staff) ? round($avg_show_month->total / $avg_show_month->staff) : 1) * 100) : 0),
            'avg_sales_all_percent' => (!empty($avg_sales_all->staff) ? round(($avg_sales_all->total / $avg_sales_all->staff) / (!empty($avg_show_all->staff) ? round($avg_show_all->total / $avg_show_all->staff) : 1) * 100) : 0),
            'avg_contact_today' => (!empty($avg_contacts_today->staff) ? round($avg_contacts_today->total / $avg_contacts_today->staff) : 0),
            'avg_contact_month' => (!empty($avg_contacts_month->staff) ? round($avg_contacts_month->total / $avg_contacts_month->staff) : 0),
            'avg_contact_all' => (!empty($avg_contacts_all->staff) ? round($avg_contacts_all->total / $avg_contacts_all->staff) : 0),
//            'avg_reconfirm_today' => (!empty($avg_reconfirms_today->staff) ? round($avg_reconfirms_today->total / $avg_reconfirms_today->staff) : 0),
//            'avg_reconfirm_month' => (!empty($avg_reconfirms_month->staff) ? round($avg_reconfirms_month->total / $avg_reconfirms_month->staff) : 0),
//            'avg_reconfirm_all' => (!empty($avg_reconfirms_all->staff) ? round($avg_reconfirms_all->total / $avg_reconfirms_all->staff) : 0),
        ];

        return response()->json($data, $this->successStatus);
    }

    public function coach_performance_detail($category, $time_filter) {
        $from_date = new Carbon('first day of this month');
        $thru_date = new Carbon('last day of this month');

        if (($category == 'contact') || ($category == 'reconfirm')):
            if ($category == 'contact'):
                $leads = LogContactedNewLead::selectRaw('buddy_referral_leads.fullname, buddy_referral_leads.phone')
                    ->where('log_contacted_new_leads.staff_id', '=', Auth::id())
                    ->whereHas('logcontactednewlead_user', function ($q) {
                        $q->where('club_id', '=', Auth::user()->club_id);
                    })
                    ->join('buddy_referral_leads', 'buddy_referral_leads.id', '=', 'log_contacted_new_leads.guest_id');
            elseif ($category == 'reconfirm'):
                $leads = LogContactedLead::selectRaw('buddy_referral_leads.fullname, buddy_referral_leads.phone')
                    ->where('log_contacted_leads.staff_id', '=', Auth::id())
                    ->whereHas('logcontactedlead_user', function ($q) {
                        $q->where('club_id', '=', Auth::user()->club_id);
                    })
                    ->join('guest_appointments', 'guest_appointments.id', '=', 'log_contacted_leads.guest_appointment_id')
                    ->join('buddy_referral_leads', 'buddy_referral_leads.id', '=', 'guest_appointments.guest_id');
            endif;

            if ($time_filter == 'today'):
                $leads->whereDate('created_at', '=', Carbon::now()->toDateString());
            elseif ($time_filter == 'month'):
                $leads->whereDate('created_at', '>=', Carbon::parse($from_date)->toDateString())
                    ->whereDate('created_at', '<=', Carbon::parse($thru_date)->toDateString());
            endif;
        else:
            $leads = GuestAppointment::selectRaw('buddy_referral_leads.fullname, buddy_referral_leads.phone');

            if ($category == 'apo'):
                $leads->where('guest_appointments.staff_id', '=', Auth::id())
                    ->join('buddy_referral_leads', 'buddy_referral_leads.id', '=', 'guest_appointments.guest_id');

                if ($time_filter == 'today'):
                    $leads->whereDate('fa_date', '=', Carbon::now()->toDateString());
                elseif ($time_filter == 'month'):
                    $leads->whereDate('fa_date', '>=', Carbon::parse($from_date)->toDateString())
                        ->whereDate('fa_date', '<=', Carbon::parse($thru_date)->toDateString());
                endif;
            elseif ($category == 'show'):
                $leads->where('guest_appointments.staff_id', '=', Auth::id())
                    ->whereHas('guestappointment_gastatus', function ($q) use ($time_filter, $from_date, $thru_date) {
                        if ($time_filter == 'today'):
                            $q->whereDate('s_date', '=', Carbon::today()->toDateString());
                        elseif ($time_filter == 'month'):
                            $q->whereDate('s_date', '>=', Carbon::parse($from_date)->toDateString())
                                ->whereDate('s_date', '<=', Carbon::parse($thru_date)->toDateString());
                        else:
                            $q->whereNotNull('s_date');
                        endif;
                    })
                    ->join('buddy_referral_leads', 'buddy_referral_leads.id', '=', 'guest_appointments.guest_id');
            elseif ($category == 'sales'):
                $leads->where('guest_appointments.staff_id', '=', Auth::id())
                    ->whereHas('guestappointment_gastatus', function ($q) use ($time_filter, $from_date, $thru_date) {
                        if ($time_filter == 'today'):
                            $q->whereDate('sa_date', '=', Carbon::today()->toDateString())
                                ->where('status_id', '=', '5');
                        elseif ($time_filter == 'month'):
                            $q->whereDate('sa_date', '>=', Carbon::parse($from_date)->toDateString())
                                ->whereDate('sa_date', '<=', Carbon::parse($thru_date)->toDateString())
                                ->where('status_id', '=', '5');
                        else:
                            $q->where('status_id', '=', '5');
                        endif;
                    })
                    ->join('buddy_referral_leads', 'buddy_referral_leads.id', '=', 'guest_appointments.guest_id');
            endif;
        endif;

        $leads = $leads->get();
        return response()->json($leads, $this->successStatus);
    }

    public function guest_list()
    {
        $user = Auth::user();

        $datas = GuestAppointment::join('guest_appointment_status','guest_appointment_status.ga_id','=','guest_appointments.id')
            ->join('buddy_referral_leads',function($q){
                $q->on('buddy_referral_leads.id','=','guest_appointments.guest_id');
            })
            ->join('member_sources',function($q){
                $q->on('buddy_referral_leads.source_id','=','member_sources.id');
            })
            ->leftJoin('referrers','referrers.referral_id','=','buddy_referral_leads.id')
            ->leftJoin('users','referrers.user_id','=','users.id')
            ->leftJoin('guest_appointment_notes','guest_appointment_notes.ga_id','=','guest_appointments.id')
            ->where('guest_appointments.club_id','=',$user->club_id)
            ->where(function ($q) {
                $q->where('guest_appointment_status.status_id','=',1);
                    // ->orWhere('guest_appointment_status.status_id','=',2)
                    // ->orWhere('guest_appointment_status.status_id','=',4)
                    // ->orWhere('guest_appointment_status.status_id','=',6);
            })
            ->where('guest_appointments.guest_status_after', '=', 'Lead')
            ->where('buddy_referral_leads.status_gm', '=', 'Guest')
            ->whereHas('guestappointment_buddyreferrallead', function ($r) {
                $r->whereDoesntHave('buddyreferrallead_memberpersonal');
            })
            ->selectRaw('buddy_referral_leads.fullname as guest_fullname,
                                    buddy_referral_leads.phone as guest_phone,
                                    guest_appointments.id as this_id,
                                    guest_appointments.fa_date as fa_date,
                                    guest_appointments.fa_hour as fa_hour,
                                    guest_appointment_notes.note as guest_note,
                                    buddy_referral_leads.source_id as source_id,
                                    source,
                                    users.name as brname,
                                    buddy_referral_leads.staff_id as coach')
            ->groupBy('buddy_referral_leads.fullname', 'buddy_referral_leads.staff_id', 'buddy_referral_leads.phone',
                'fa_date', 'fa_hour',
                'guest_note', 'guest_appointments.id','buddy_referral_leads.source_id', 'source','brname')
            ->orderBy('guest_appointments.fa_date', 'desc')
            ->get();

        return response()->json($datas, $this->successStatus);
    }

    public function missing_in_action(Request $request) {
        $user = Auth::user();

        if ($request->date != ''):
            $date = Carbon::parse($request->date);
            $mias = User::selectRaw("users.id as id, users.name as name,
            (SELECT workout_date FROM workouts WHERE user_id = users.id AND workout_date <= '".Carbon::today()->toDateString()." 23:59:59' 
            AND workout_date >= '".Carbon::parse($date)->toDateString()." 00:00:00' ORDER BY workout_date DESC LIMIT 1) as workout_date,
            (SELECT workout_date FROM workouts WHERE user_id = users.id AND workout_date <= '".Carbon::today()->toDateString()." 23:59:59' 
            ORDER BY workout_date DESC LIMIT 1) as workout_all,
            member_personals.joined, member_personals.flag, member_personals.photo, member_personals.cellphone");
        else:
//            $date = new Carbon('first day of this month');
            $date = Carbon::today();
            $mias = User::selectRaw("users.id as id, users.name as name,
            (SELECT workout_date FROM workouts WHERE user_id = users.id AND workout_date <= '".Carbon::today()->toDateString()." 23:59:59' 
            ORDER BY workout_date DESC LIMIT 1) as workout_date, 
            member_personals.joined, member_personals.flag, member_personals.photo, member_personals.cellphone");
        endif;

        $mias =  $mias->whereHas('user_memberstatus', function ($q) {
                $q->where('status', '=', 'Active');
            })
            ->whereHas('memberuser_coachmember', function ($s) use ($user) {
                $s->where('coach_id', '=', $user->id);
            })
            ->join('member_personals', 'member_personals.user_id', '=', 'users.id')
            ->whereDate('member_personals.joined', '<=', Carbon::parse($date)->toDateString())
            ->get();

        foreach ($mias as $mia):
            $miaMonitoring = MIAMonitoring::where('caller_id', '=', $user->id)
                ->where('last_wo', '=', Carbon::parse($mia->workout_date)->format('Y-m-d'))
                ->orderBy('calling_date', 'desc')
                ->first();
            if (!empty($miaMonitoring->reason)):
                $mia->setAttribute('reason', $miaMonitoring->reason);
            else:
                $mia->setAttribute('reason', '');
            endif;

            if (!empty($mia->workout_date)):
                $diff_day = Carbon::today()->diffInDays(Carbon::parse($mia->workout_date));
                $mia->setAttribute('workout_date', Carbon::parse($mia->workout_date)->format('d-m-Y'));
                $mia->setAttribute('days', $diff_day);
            else:
                $mia->setAttribute('workout_date', Carbon::parse($mia->joined)->format('d-m-Y'));

                if ($request->date != ''):
                    if (!empty($mia->workout_all)):
                        $mia->setAttribute('days', 0);
                    else:
                        $mia->setAttribute('days', 999);
                    endif;
                else:
                    $mia->setAttribute('days', 999);
                endif;
            endif;
        endforeach;

        if (!empty($request->mirror)):
            $mias = $mias->sortBy('flag')->values();
        else:
            $mias = $mias->sortByDesc('days')->values();
        endif;

        return response()->json($mias, $this->successStatus);
    }

    public function store_missing_in_action(Request $request){
        $user = Auth::user();

        if(!empty($request->mia)):
            $mia = new MIAMonitoring;
            $mia->caller_id = $user->id;
            $mia->member_id = $request->userId;
            $mia->calling_date = Carbon::now();
            $mia->reason = $request->mia;

            $last_wo = Workout::where('user_id', '=', $request->userId)
                ->where('workout_date', '<', Carbon::now())
                ->orderBy('workout_date', 'desc')
                ->first();

            $mia->last_wo = $last_wo->workout_date;
            $mia->save();
        endif;

        return response()->json(['success' => true], $this->successStatus);
    }

    public function missing_in_action_history($id) {
        $last_wo = Workout::where('user_id', '=', $id)
            ->orderBy('workout_date', 'desc')
            ->first();

        $workouts = Workout::selectRaw('COUNT(user_id) as jumlah, extract(week from workout_date) as week, extract(year from workout_date) as year')
            ->where('user_id','=',$id)
            ->whereDate('workout_date', '<=', Carbon::parse($last_wo->workout_date)->toDateString())
            ->whereDate('workout_date', '>=', Carbon::parse($last_wo->workout_date)->subWeeks(4)->toDateString())
            ->groupBy('year')
            ->groupBy('week')
            ->orderBy('year', 'desc')
            ->orderBy('week', 'desc')
            ->get();

        $date = Carbon::now();
        foreach ($workouts as $workout):
            $workout->setAttribute('date', $date->setISODate($workout->year, $workout->week)->endOfWeek()->format('d-m-Y'));
        endforeach;

        return response()->json($workouts, $this->successStatus);
    }

    
    // LEADERBOARDS
    // TOP WEIGHT LOSER
    public function get_top_weight_losers(){
        $user = Auth::user();

        $members = $members = User::selectRaw('users.id, member_personals.photo, users.name,
                (SELECT weight -
                (SELECT weight FROM weigh_measures mi WHERE mi.wm_date < m.wm_date AND user_id = users.id
                ORDER BY wm_date DESC LIMIT 1)
                FROM  weigh_measures m WHERE user_id = users.id ORDER BY wm_date DESC LIMIT 1) as lossamount,
                EXTRACT( EPOCH FROM (SELECT wm_date -
                (SELECT wm_date FROM weigh_measures mi WHERE mi.wm_date < m.wm_date AND user_id = users.id
                ORDER BY wm_date DESC LIMIT 1)
                FROM  weigh_measures m WHERE user_id = users.id ORDER BY wm_date DESC LIMIT 1))/86400 as diffdays')
                ->where(function ($q) {
                    $q->where('member_status.status', '=', 'Active')
                        ->orWhere('member_status.status', '=', 'Hold');
                })->where(function ($q) use($user) {
                    $q->where('coach_members.coach_id','=',$user->id);
                });

            $members = $members->leftJoin('workouts', function ($join) {
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

            $members = $members->join('coach_members', function($join){
                $join->on('users.id','=','coach_members.member_id');
            });

            $members = $members->groupBy('users.id','users.name','member_personals.photo','member_personals.phone',
                'member_personals.cellphone','member_status.last_wm','member_status.wm_schedule','users.created_at',
                'club_infos.club_code')
                ->orderBy('lossamount', 'asc')
                ->orderBy('diffdays', 'asc')
                ->get();

        $counter = 1;
        foreach ($members as $member):
            if (!empty($member->photo)):
                $member->setAttribute('photo', 'member_photos/'.$member->photo);
            else:
                $member->setAttribute('photo', 'img.png');
            endif;

            if($member->lossamount == null){
                $member->lossamount = '-';
            } else {
                $member->setAttribute('lossamount', number_format($member->lossamount, 2));
            }

            if($member->diffdays == null){
                $member->diffdays = '-';
            }

            $member->setAttribute('rank',$counter);
            $counter++;
        endforeach;

        return response()->json( $members, $this->successStatus);
    }

    public function get_top_weight_losers_v2(){
        $user = Auth::user();

        $members = $members = User::selectRaw('users.id, member_personals.photo, users.name,
                (SELECT weight -
                (SELECT weight FROM weigh_measures mi WHERE mi.wm_date < m.wm_date AND user_id = users.id
                ORDER BY wm_date DESC LIMIT 1)
                FROM  weigh_measures m WHERE user_id = users.id ORDER BY wm_date DESC LIMIT 1) as lossamount,
                EXTRACT( EPOCH FROM (SELECT wm_date -
                (SELECT wm_date FROM weigh_measures mi WHERE mi.wm_date < m.wm_date AND user_id = users.id
                ORDER BY wm_date DESC LIMIT 1)
                FROM  weigh_measures m WHERE user_id = users.id ORDER BY wm_date DESC LIMIT 1))/86400 as diffdays')
                ->where(function ($q) {
                    $q->where('member_status.status', '=', 'Active')
                        ->orWhere('member_status.status', '=', 'Hold');
                })->where(function ($q) use($user) {
                    $q->where('coach_members.coach_id','=',$user->id);
                });

            $members = $members->leftJoin('workouts', function ($join) {
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

            $members = $members->join('coach_members', function($join){
                $join->on('users.id','=','coach_members.member_id');
            });

            $members = $members->groupBy('users.id','users.name','member_personals.photo','member_personals.phone',
                'member_personals.cellphone','member_status.last_wm','member_status.wm_schedule','users.created_at',
                'club_infos.club_code')
                ->orderBy('lossamount', 'asc')
                ->orderBy('diffdays', 'asc')
                ->get();

        $counter = 1;
        foreach ($members as $member):
            if (!empty($member->photo)):
                $member->setAttribute('photo', 'member_photos/'.$member->photo);
            else:
                $member->setAttribute('photo', 'img.png');
            endif;

            if($member->lossamount == null){
                $member->lossamount = '-';
            } else {
                $member->setAttribute('lossamount', number_format($member->lossamount, 2));
            }

            if($member->diffdays == null){
                $member->diffdays = '-';
            }

            if($member->lossamount != '-' && $member->diffdays != '-' ){
                if($member->lossamount != 0 && $member->diffdays != 0){
                    $member->setAttribute('avg', $member->lossamount / $member->diffdays);
                }else if($member->lossamount != 0 && $member->diffdays == 0){
                    $member->setAttribute('avg',$member->lossamount);
                }else {
                    $member->setAttribute('avg', 0);
                } 
            }else{
                $member->setAttribute('avg', 99999999);
            }

        endforeach;

        $members = $members->sortBy('avg')->values();

        foreach ($members as $member) {
            $member->setAttribute('rank',$counter);
            $counter++;
        }

        return response()->json( $members, $this->successStatus);
    }

    public function get_top_weight_losers_v3(){
        $user = Auth::user();

        // $members = User::selectRaw('users.id, member_personals.photo, users.name,
        //         (SELECT weight -
        //         (SELECT weight FROM weigh_measures mi WHERE mi.wm_date < m.wm_date AND user_id = users.id
        //         ORDER BY wm_date DESC LIMIT 1)
        //         FROM  weigh_measures m WHERE user_id = users.id AND m.wm_date > \''.Carbon::now()->startOfMonth()->format('d-m-Y').'\' AND m.wm_date < \''.Carbon::now()->endOfMonth()->format('d-m-Y').'\' ORDER BY wm_date DESC LIMIT 1) as lossamount,
        //         EXTRACT( EPOCH FROM (SELECT wm_date -
        //         (SELECT wm_date FROM weigh_measures mi WHERE mi.wm_date < m.wm_date AND user_id = users.id
        //         ORDER BY wm_date DESC LIMIT 1)
        //         FROM  weigh_measures m WHERE user_id = users.id ORDER BY wm_date DESC LIMIT 1))/86400 as diffdays')
        //         ->where(function ($q) {
        //             $q->where('member_status.status', '=', 'Active');
        //         })->where(function ($q) use($user) {
        //             $q->where('coach_members.coach_id','=',$user->id);
        //         });

            $members = User::selectRaw('users.id, member_personals.photo, users.name, users.id as memberid, member_personals.cellphone as memberphone, (SELECT workout_date FROM workouts WHERE user_id = users.id AND workout_date <= \''.Carbon::today()->toDateString().' 23:59:59\' ORDER BY workout_date DESC LIMIT 1) as workout_date,
                (SELECT weight -
                (SELECT weight FROM weigh_measures mi WHERE mi.wm_date < m.wm_date AND user_id = users.id
                ORDER BY wm_date DESC LIMIT 1)
                FROM  weigh_measures m WHERE user_id = users.id AND m.wm_date > \''.Carbon::now()->startOfMonth()->format('Y-m-d').'\' AND m.wm_date < \''.Carbon::now()->endOfMonth()->format('Y-m-d').'\' ORDER BY wm_date DESC LIMIT 1) as lossamount,
                EXTRACT( EPOCH FROM (SELECT wm_date -
                (SELECT wm_date FROM weigh_measures mi WHERE mi.wm_date < m.wm_date AND user_id = users.id
                ORDER BY wm_date DESC LIMIT 1)
                FROM  weigh_measures m WHERE user_id = users.id ORDER BY wm_date DESC LIMIT 1))/86400 as diffdays, 
                (SELECT count(workout_date) FROM workouts WHERE user_id = users.id AND workout_date >= (SELECT (SELECT mi.wm_date FROM weigh_measures mi WHERE mi.wm_date < m.wm_date AND user_id = users.id
                ORDER BY wm_date DESC LIMIT 1)
                FROM  weigh_measures m WHERE user_id = users.id AND m.wm_date > \''.Carbon::now()->startOfMonth()->format('Y-m-d').'\' AND m.wm_date < \''.Carbon::now()->endOfMonth()->format('Y-m-d').'\' ORDER BY wm_date DESC LIMIT 1) AND workout_date <= (SELECT m.wm_date
                    FROM  weigh_measures m WHERE user_id = users.id AND m.wm_date > \''.Carbon::now()->startOfMonth()->format('Y-m-d').'\' AND m.wm_date < \''.Carbon::now()->endOfMonth()->format('Y-m-d').'\' ORDER BY wm_date DESC LIMIT 1)) as wo_count')
                ->where(function ($q) {
                    $q->where('member_status.status', '=', 'Active');
                })->where(function ($q) use($user) {
                    $q->where('coach_members.coach_id','=',$user->id);
                });

                $members->leftJoin('workouts', function ($join) {
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

            $members = $members->join('coach_members', function($join){
                $join->on('users.id','=','coach_members.member_id');
            });

            $members = $members->groupBy('users.id','users.name','member_personals.photo','member_personals.phone',
                'member_personals.cellphone','member_status.last_wm','member_status.wm_schedule','users.created_at',
                'club_infos.club_code')
                ->orderBy('lossamount', 'asc')
                ->orderBy('diffdays', 'asc')
                ->get();

        $counter = 1;
        foreach ($members as $index => $member):
            if (!empty($member->photo)):
                $member->setAttribute('photo', 'member_photos/'.$member->photo);
            else:
                $member->setAttribute('photo', 'img.png');
            endif;

            if (!empty($member->workout_date)):
                $diff_day = Carbon::today()->diffInDays(Carbon::parse($member->workout_date));
                $member->setAttribute('days', $diff_day);
            else:
                $member->setAttribute('days', 999);
            endif;

            if($member->lossamount == null){
                $member->lossamount = '99999999';
                $members->forget($index);
            } else {
                $member->setAttribute('lossamount', number_format($member->lossamount, 2));
            }

            if($member->diffdays == null){
                $member->diffdays = '-';
            }

        endforeach;

        $members = $members->sortBy('lossamount')->values();

        foreach ($members as $member) {
            $member->setAttribute('rank',$counter);
            $counter++;
        }

        return response()->json( $members, $this->successStatus);
    }

    public function get_top_weight_losers_v4(){
        $user = Auth::user();

        $members = User::selectRaw('users.id, member_personals.photo, users.name, users.id as memberid, member_personals.cellphone as memberphone, (SELECT workout_date FROM workouts WHERE user_id = users.id AND workout_date <= \''.Carbon::today()->toDateString().' 23:59:59\' ORDER BY workout_date DESC LIMIT 1) as workout_date')->where(function ($q) {
                $q->where('member_status.status', '=', 'Active');
            })->where(function ($q) use($user) {
                $q->where('coach_members.coach_id','=',$user->id);
            })->whereHas('member_weighmeasure',function($s){
                $s->where('wm_date','>=', new Carbon('first day of this month'));
                $s->where('wm_date','<=', new Carbon('last day of this month'));
            })->leftJoin('workouts', function ($join) {
            $join->on('users.id', '=', 'workouts.user_id');
        })->join('member_personals', function($join){
            $join->on('users.id','=','member_personals.user_id');
        })->join('member_status', function($join){
            $join->on('users.id','=','member_status.user_id');
        })->join('coach_members', function($join){
            $join->on('users.id','=','coach_members.member_id');
        })->groupBy('users.id','users.name','member_personals.photo','member_personals.phone',
            'member_personals.cellphone','member_status.last_wm','member_status.wm_schedule','users.created_at')->get();

        $counter = 1;
        foreach ($members as $index => $member):
            $wm_rank = WMRank::where('user_id','=',$member->id)->where('type','=','monthly')->first();

            if(empty($wm_rank)){
                $member->setAttribute('lossamount',null);
                $member->setAttribute('diffdays', 999);
                $member->setAttribute('wo_count',0);
            }else{
                $member->setAttribute('lossamount',$wm_rank->wm_one == 0 ? null : $wm_rank->weigh_diff);
                $member->setAttribute('diffdays',$wm_rank->wm_one == 0 ? 999 : $wm_rank->day_diff);
                $member->setAttribute('wo_count',$wm_rank->wo_count);
            }

            if (!empty($member->photo)):
                $member->setAttribute('photo', 'member_photos/'.$member->photo);
            else:
                $member->setAttribute('photo', 'img.png');
            endif;

            if (!empty($member->workout_date)):
                $diff_day = Carbon::today()->diffInDays(Carbon::parse($member->workout_date));
                $member->setAttribute('days', $diff_day);
            else:
                $member->setAttribute('days', 999);
            endif;

            if($member->lossamount == null){
                $member->lossamount = '99999999';
                $members->forget($index);
            } else {
                $member->setAttribute('lossamount', number_format($member->lossamount, 2));
            }

            if($member->diffdays == null){
                $member->diffdays = '-';
            }

        endforeach;

        $members = $members->sortBy('lossamount')->values();

        foreach ($members as $member) {
            $member->setAttribute('rank',$counter);
            $counter++;
        }

        return response()->json( $members, $this->successStatus);
    }

    public function get_top_weight_losers_overall(){
        $user = Auth::user();

        $members = $members = User::selectRaw('users.id, member_personals.photo, users.name, users.id as memberid, member_personals.cellphone as memberphone, (SELECT workout_date FROM workouts WHERE user_id = users.id AND workout_date <= \''.Carbon::today()->toDateString().' 23:59:59\' ORDER BY workout_date DESC LIMIT 1) as workout_date,
            (SELECT weight -
            (SELECT weight FROM weigh_measures mi WHERE mi.wm_date < m.wm_date AND user_id = users.id
            ORDER BY wm_date ASC LIMIT 1)
            FROM  weigh_measures m WHERE user_id = users.id ORDER BY wm_date DESC LIMIT 1) as lossamount,
            EXTRACT( EPOCH FROM (SELECT wm_date -
            (SELECT wm_date FROM weigh_measures mi WHERE mi.wm_date < m.wm_date AND user_id = users.id
            ORDER BY wm_date ASC LIMIT 1)
            FROM  weigh_measures m WHERE user_id = users.id ORDER BY wm_date DESC LIMIT 1))/86400 as diffdays, 
            (SELECT count(workout_date) FROM workouts WHERE user_id = users.id AND workout_date >= (SELECT mi.wm_date FROM weigh_measures mi WHERE mi.wm_date < (SELECT m.wm_date FROM  weigh_measures m WHERE user_id = users.id ORDER BY wm_date DESC LIMIT 1) AND user_id = users.id
            ORDER BY wm_date ASC LIMIT 1) AND workout_date <= (SELECT m.wm_date
            FROM  weigh_measures m WHERE user_id = users.id ORDER BY wm_date DESC LIMIT 1)) as wo_count')
            ->where(function ($q) {
                $q->where('member_status.status', '=', 'Active');
            })->where(function ($q) use($user) {
                $q->where('coach_members.coach_id','=',$user->id);
            });

        $members = $members->leftJoin('workouts', function ($join) {
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

        $members = $members->join('coach_members', function($join){
            $join->on('users.id','=','coach_members.member_id');
        });

        $members = $members->groupBy('users.id','users.name','member_personals.photo','member_personals.phone',
            'member_personals.cellphone','member_status.last_wm','member_status.wm_schedule','users.created_at',
            'club_infos.club_code')
            ->orderBy('lossamount', 'asc')
            ->orderBy('diffdays', 'asc')
            ->get();

        $counter = 1;
        foreach ($members as $member):
            if (!empty($member->photo)):
                $member->setAttribute('photo', 'member_photos/'.$member->photo);
            else:
                $member->setAttribute('photo', 'img.png');
            endif;

            if (!empty($member->workout_date)):
                $diff_day = Carbon::today()->diffInDays(Carbon::parse($member->workout_date));
                $member->setAttribute('days', $diff_day);
            else:
                $member->setAttribute('days', 999);
            endif;

            if($member->lossamount == null){
                $member->lossamount = '99999999';
            } else {
                $member->setAttribute('lossamount', number_format($member->lossamount, 2));
            }

            if($member->diffdays == null){
                $member->diffdays = '-';
            }

        endforeach;

        $members = $members->sortBy('lossamount')->values();

        foreach ($members as $member) {
            $member->setAttribute('rank',$counter);
            $counter++;
        }

        return response()->json( $members, $this->successStatus);
    }

    public function get_top_weight_losers_overall_v2(){
        $user = Auth::user();

        $members = $members = User::selectRaw('users.id, member_personals.photo, users.name, users.id as memberid, member_personals.cellphone as memberphone, (SELECT workout_date FROM workouts WHERE user_id = users.id AND workout_date <= \''.Carbon::today()->toDateString().' 23:59:59\' ORDER BY workout_date DESC LIMIT 1) as workout_date')
            ->where(function ($q) {
                $q->where('member_status.status', '=', 'Active');
            })->where(function ($q) use($user) {
                $q->where('coach_members.coach_id','=',$user->id);
            });

        $members = $members->leftJoin('workouts', function ($join) {
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

        $members = $members->join('coach_members', function($join){
            $join->on('users.id','=','coach_members.member_id');
        });

        $members = $members->groupBy('users.id','users.name','member_personals.photo','member_personals.phone',
            'member_personals.cellphone','member_status.last_wm','member_status.wm_schedule','users.created_at',
            'club_infos.club_code')
            ->get();

        $counter = 1;
        foreach ($members as $member):
            $wm_rank = WMRank::where('user_id','=',$member->id)->where('type','=','overall')->first();

            if(empty($wm_rank)){
                $member->setAttribute('lossamount',null);
                $member->setAttribute('diffdays', 999);
                $member->setAttribute('wo_count',0);
            }else{
                $member->setAttribute('lossamount',$wm_rank->wm_one == 0 ? null : $wm_rank->weigh_diff);
                $member->setAttribute('diffdays',$wm_rank->wm_one == 0 ? 999 : $wm_rank->day_diff);
                $member->setAttribute('wo_count',$wm_rank->wo_count);
            }

            if (!empty($member->photo)):
                $member->setAttribute('photo', 'member_photos/'.$member->photo);
            else:
                $member->setAttribute('photo', 'img.png');
            endif;

            if (!empty($member->workout_date)):
                $diff_day = Carbon::today()->diffInDays(Carbon::parse($member->workout_date));
                $member->setAttribute('days', $diff_day);
            else:
                $member->setAttribute('days', 999);
            endif;

            if($member->lossamount == null){
                $member->lossamount = '99999999';
            } else {
                $member->setAttribute('lossamount', number_format($member->lossamount, 2));
            }

            if($member->diffdays == null){
                $member->diffdays = '-';
            }

        endforeach;

        $members = $members->sortBy('lossamount')->values();

        foreach ($members as $member) {
            $member->setAttribute('rank',$counter);
            $counter++;
        }

        return response()->json( $members, $this->successStatus);
    }

    public function get_top_weight_losers_by_club(){
        $user = Auth::user();

        $members = User::selectRaw('users.id, member_personals.photo, users.name, users.id as memberid, member_personals.cellphone as memberphone, (SELECT workout_date FROM workouts WHERE user_id = users.id AND workout_date <= \''.Carbon::today()->toDateString().' 23:59:59\' ORDER BY workout_date DESC LIMIT 1) as workout_date,
                (SELECT weight -
                (SELECT weight FROM weigh_measures mi WHERE mi.wm_date < m.wm_date AND user_id = users.id
                ORDER BY wm_date DESC LIMIT 1)
                FROM  weigh_measures m WHERE user_id = users.id AND m.wm_date > \''.Carbon::now()->startOfMonth()->format('Y-m-d').'\' AND m.wm_date < \''.Carbon::now()->endOfMonth()->format('Y-m-d').'\' ORDER BY wm_date DESC LIMIT 1) as lossamount,
                EXTRACT( EPOCH FROM (SELECT wm_date -
                (SELECT wm_date FROM weigh_measures mi WHERE mi.wm_date < m.wm_date AND user_id = users.id
                ORDER BY wm_date DESC LIMIT 1)
                FROM  weigh_measures m WHERE user_id = users.id ORDER BY wm_date DESC LIMIT 1))/86400 as diffdays, 
                (SELECT count(workout_date) FROM workouts WHERE user_id = users.id AND workout_date >= (SELECT (SELECT mi.wm_date FROM weigh_measures mi WHERE mi.wm_date < m.wm_date AND user_id = users.id
                ORDER BY wm_date DESC LIMIT 1)
                FROM  weigh_measures m WHERE user_id = users.id AND m.wm_date > \''.Carbon::now()->startOfMonth()->format('Y-m-d').'\' AND m.wm_date < \''.Carbon::now()->endOfMonth()->format('Y-m-d').'\' ORDER BY wm_date DESC LIMIT 1) AND workout_date <= (SELECT m.wm_date
                FROM  weigh_measures m WHERE user_id = users.id ORDER BY wm_date DESC LIMIT 1)) as wo_count')
                ->where(function ($q) {
                    $q->where('member_status.status', '=', 'Active');
                })->where(function($r) use($user){
                    $r->where('users.club_id','=',$user->club_id);
                })->where(function($s){
                    $s->where('users.role_id','=',7);
                });

            $members = $members->leftJoin('workouts', function ($join) {
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
                ->orderBy('lossamount', 'asc')
                ->orderBy('diffdays', 'asc')
                ->get();

        $counter = 1;
        foreach ($members as $index => $member):
            if (!empty($member->photo)):
                $member->setAttribute('photo', 'member_photos/'.$member->photo);
            else:
                $member->setAttribute('photo', 'img.png');
            endif;

            if (!empty($member->workout_date)):
                $diff_day = Carbon::today()->diffInDays(Carbon::parse($member->workout_date));
                $member->setAttribute('days', $diff_day);
            else:
                $member->setAttribute('days', 999);
            endif;

            if($member->lossamount == null){
                $member->lossamount = '99999999';
                $members->forget($index);
            } else {
                $member->setAttribute('lossamount', number_format($member->lossamount, 2));
            }

            if($member->diffdays == null){
                $member->diffdays = '-';
            }

        endforeach;

        $members = $members->sortBy('lossamount')->values();

        foreach ($members as $member) {
            $member->setAttribute('rank',$counter);
            $counter++;
        }

        return response()->json( $members, $this->successStatus);
    }

    public function get_top_weight_losers_by_club_v2(){
        $user = Auth::user();

        $members = User::selectRaw('users.id, member_personals.photo, users.name, users.id as memberid, member_personals.cellphone as memberphone, (SELECT workout_date FROM workouts WHERE user_id = users.id AND workout_date <= \''.Carbon::today()->toDateString().' 23:59:59\' ORDER BY workout_date DESC LIMIT 1) as workout_date')
                ->where(function ($q) {
                    $q->where('member_status.status', '=', 'Active');
                })->where(function($r) use($user){
                    $r->where('users.club_id','=',$user->club_id);
                })->where(function($s){
                    $s->where('users.role_id','=',7);
                })->whereHas('member_weighmeasure',function($s){
                    $s->where('wm_date','>=', new Carbon('first day of this month'));
                    $s->where('wm_date','<=', new Carbon('last day of this month'));
                });

            $members = $members->leftJoin('workouts', function ($join) {
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
                ->get();

        $counter = 1;
        foreach ($members as $index => $member):
            $wm_rank = WMRank::where('user_id','=',$member->id)->where('type','=','monthly')->first();

            if(empty($wm_rank)){
                $member->setAttribute('lossamount',null);
                $member->setAttribute('diffdays', 999);
                $member->setAttribute('wo_count',0);
            }else{
                $member->setAttribute('lossamount',$wm_rank->wm_one == 0 ? null : $wm_rank->weigh_diff);
                $member->setAttribute('diffdays',$wm_rank->wm_one == 0 ? 999 : $wm_rank->day_diff);
                $member->setAttribute('wo_count',$wm_rank->wo_count);
            }

            if (!empty($member->photo)):
                $member->setAttribute('photo', 'member_photos/'.$member->photo);
            else:
                $member->setAttribute('photo', 'img.png');
            endif;

            if (!empty($member->workout_date)):
                $diff_day = Carbon::today()->diffInDays(Carbon::parse($member->workout_date));
                $member->setAttribute('days', $diff_day);
            else:
                $member->setAttribute('days', 999);
            endif;

            if($member->lossamount == null){
                $member->lossamount = '99999999';
                $members->forget($index);
            } else {
                $member->setAttribute('lossamount', number_format($member->lossamount, 2));
            }

            if($member->diffdays == null){
                $member->diffdays = '-';
            }

        endforeach;

        $members = $members->sortBy('lossamount')->values();

        foreach ($members as $member) {
            $member->setAttribute('rank',$counter);
            $counter++;
        }

        return response()->json( $members, $this->successStatus);
    }

    public function get_top_weight_losers_by_club_overall(){
        $user = Auth::user();

        $members = $members = User::selectRaw('users.id, member_personals.photo, users.name, users.id as memberid, member_personals.cellphone as memberphone, (SELECT workout_date FROM workouts WHERE user_id = users.id AND workout_date <= \''.Carbon::today()->toDateString().' 23:59:59\' ORDER BY workout_date DESC LIMIT 1) as workout_date,
                (SELECT weight -
                (SELECT weight FROM weigh_measures mi WHERE mi.wm_date < m.wm_date AND user_id = users.id
                ORDER BY wm_date ASC LIMIT 1)
                FROM  weigh_measures m WHERE user_id = users.id ORDER BY wm_date DESC LIMIT 1) as lossamount,
                EXTRACT( EPOCH FROM (SELECT wm_date -
                (SELECT wm_date FROM weigh_measures mi WHERE mi.wm_date < m.wm_date AND user_id = users.id
                ORDER BY wm_date ASC LIMIT 1)
                FROM  weigh_measures m WHERE user_id = users.id ORDER BY wm_date DESC LIMIT 1))/86400 as diffdays, 
                (SELECT count(workout_date) FROM workouts WHERE user_id = users.id AND workout_date >= (SELECT mi.wm_date FROM weigh_measures mi WHERE mi.wm_date < (SELECT m.wm_date FROM  weigh_measures m WHERE user_id = users.id ORDER BY wm_date DESC LIMIT 1) AND user_id = users.id
                ORDER BY wm_date ASC LIMIT 1) AND workout_date <= (SELECT m.wm_date
                FROM  weigh_measures m WHERE user_id = users.id ORDER BY wm_date DESC LIMIT 1)) as wo_count')
                ->where(function ($q) {
                    $q->where('member_status.status', '=', 'Active');
                })->where(function($r) use($user){
                    $r->where('users.club_id','=',$user->club_id);
                })->where(function($s){
                    $s->where('users.role_id','=',7);
                });

            $members = $members->leftJoin('workouts', function ($join) {
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
                ->orderBy('lossamount', 'asc')
                ->orderBy('diffdays', 'asc')
                ->get();

        $counter = 1;
        foreach ($members as $member):
            if (!empty($member->photo)):
                $member->setAttribute('photo', 'member_photos/'.$member->photo);
            else:
                $member->setAttribute('photo', 'img.png');
            endif;

            if (!empty($member->workout_date)):
                $diff_day = Carbon::today()->diffInDays(Carbon::parse($member->workout_date));
                $member->setAttribute('days', $diff_day);
            else:
                $member->setAttribute('days', 999);
            endif;

            if($member->lossamount == null){
                $member->lossamount = '99999999';
            } else {
                $member->setAttribute('lossamount', number_format($member->lossamount, 2));
            }

            if($member->diffdays == null){
                $member->diffdays = '-';
            }

        endforeach;

        $members = $members->sortBy('lossamount')->values();

        foreach ($members as $member) {
            $member->setAttribute('rank',$counter);
            $counter++;
        }

        return response()->json( $members, $this->successStatus);
    }

    public function get_top_weight_losers_by_club_overall_v2(){
        $user = Auth::user();

        $members = $members = User::selectRaw('users.id, member_personals.photo, users.name, users.id as memberid, member_personals.cellphone as memberphone, (SELECT workout_date FROM workouts WHERE user_id = users.id AND workout_date <= \''.Carbon::today()->toDateString().' 23:59:59\' ORDER BY workout_date DESC LIMIT 1) as workout_date')
                ->where(function ($q) {
                    $q->where('member_status.status', '=', 'Active');
                })->where(function($r) use($user){
                    $r->where('users.club_id','=',$user->club_id);
                })->where(function($s){
                    $s->where('users.role_id','=',7);
                });

            $members = $members->leftJoin('workouts', function ($join) {
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
                ->get();

        $counter = 1;
        foreach ($members as $member):
            $wm_rank = WMRank::where('user_id','=',$member->id)->where('type','=','overall')->first();

            if(empty($wm_rank)){
                $member->setAttribute('lossamount',null);
                $member->setAttribute('diffdays', 999);
                $member->setAttribute('wo_count',0);
            }else{
                $member->setAttribute('lossamount',$wm_rank->wm_one == 0 ? null : $wm_rank->weigh_diff);
                $member->setAttribute('diffdays',$wm_rank->wm_one == 0 ? 999 : $wm_rank->day_diff);
                $member->setAttribute('wo_count',$wm_rank->wo_count);
            }

            if (!empty($member->photo)):
                $member->setAttribute('photo', 'member_photos/'.$member->photo);
            else:
                $member->setAttribute('photo', 'img.png');
            endif;

            if (!empty($member->workout_date)):
                $diff_day = Carbon::today()->diffInDays(Carbon::parse($member->workout_date));
                $member->setAttribute('days', $diff_day);
            else:
                $member->setAttribute('days', 999);
            endif;

            if($member->lossamount == null){
                $member->lossamount = '99999999';
            } else {
                $member->setAttribute('lossamount', number_format($member->lossamount, 2));
            }

            if($member->diffdays == null){
                $member->diffdays = '-';
            }

        endforeach;

        $members = $members->sortBy('lossamount')->values();

        foreach ($members as $member) {
            $member->setAttribute('rank',$counter);
            $counter++;
        }

        return response()->json( $members, $this->successStatus);
    }

    // TOP MEASUREMENT LOSER
    public function get_top_measurement_losers(){
        $user = Auth::user();

        $members = $members = User::selectRaw('users.id, member_personals.photo, users.name,
                (SELECT total_measurement -
                (SELECT total_measurement FROM weigh_measures mi WHERE mi.wm_date < m.wm_date AND user_id = users.id 
                ORDER BY wm_date DESC LIMIT 1)
                FROM  weigh_measures m WHERE user_id = users.id ORDER BY wm_date DESC LIMIT 1) as lossamount,
                EXTRACT( EPOCH FROM (SELECT wm_date -
                (SELECT wm_date FROM weigh_measures mi WHERE mi.wm_date < m.wm_date AND user_id = users.id
                ORDER BY wm_date DESC LIMIT 1)
                FROM  weigh_measures m WHERE user_id = users.id ORDER BY wm_date DESC LIMIT 1))/86400 as diffdays')
                ->where(function ($q) {
                    $q->where('member_status.status', '=', 'Active')
                        ->orWhere('member_status.status', '=', 'Hold');
                })->where(function ($q) use($user) {
                    $q->where('coach_members.coach_id','=',$user->id);
                });

            $members = $members->leftJoin('workouts', function ($join) {
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

            $members = $members->join('coach_members', function($join){
                $join->on('users.id','=','coach_members.member_id');
            });

            $members = $members->groupBy('users.id','users.name','member_personals.photo','member_personals.phone',
                'member_personals.cellphone','member_status.last_wm','member_status.wm_schedule','users.created_at',
                'club_infos.club_code')
                ->orderBy('lossamount', 'asc')
                ->orderBy('diffdays', 'asc')
                ->get();

        $counter = 1;
        foreach ($members as $member):
            if (!empty($member->photo)):
                $member->setAttribute('photo', 'member_photos/'.$member->photo);
            else:
                $member->setAttribute('photo', 'img.png');
            endif;

            if($member->lossamount == null){
                $member->lossamount = '-';
            } else {
                $member->setAttribute('lossamount', number_format($member->lossamount, 2));
            }

            if($member->diffdays == null){
                $member->diffdays = '-';
            }

            $member->setAttribute('rank',$counter);
            $counter++;
        endforeach;

        return response()->json( $members, $this->successStatus);
    }

    public function get_top_measurement_losers_v2(){
        $user = Auth::user();

        $members = $members = User::selectRaw('users.id, member_personals.photo, users.name,
                (SELECT total_measurement -
                (SELECT total_measurement FROM weigh_measures mi WHERE mi.wm_date < m.wm_date AND user_id = users.id 
                ORDER BY wm_date DESC LIMIT 1)
                FROM  weigh_measures m WHERE user_id = users.id ORDER BY wm_date DESC LIMIT 1) as lossamount,
                EXTRACT( EPOCH FROM (SELECT wm_date -
                (SELECT wm_date FROM weigh_measures mi WHERE mi.wm_date < m.wm_date AND user_id = users.id
                ORDER BY wm_date DESC LIMIT 1)
                FROM  weigh_measures m WHERE user_id = users.id ORDER BY wm_date DESC LIMIT 1))/86400 as diffdays')
                ->where(function ($q) {
                    $q->where('member_status.status', '=', 'Active')
                        ->orWhere('member_status.status', '=', 'Hold');
                })->where(function ($q) use($user) {
                    $q->where('coach_members.coach_id','=',$user->id);
                });

            $members = $members->leftJoin('workouts', function ($join) {
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

            $members = $members->join('coach_members', function($join){
                $join->on('users.id','=','coach_members.member_id');
            });

            $members = $members->groupBy('users.id','users.name','member_personals.photo','member_personals.phone',
                'member_personals.cellphone','member_status.last_wm','member_status.wm_schedule','users.created_at',
                'club_infos.club_code')
                ->orderBy('lossamount', 'asc')
                ->orderBy('diffdays', 'asc')
                ->get();

        $counter = 1;
        foreach ($members as $member):
            if (!empty($member->photo)):
                $member->setAttribute('photo', 'member_photos/'.$member->photo);
            else:
                $member->setAttribute('photo', 'img.png');
            endif;

            if($member->lossamount == null){
                $member->lossamount = '-';
            } else {
                $member->setAttribute('lossamount', number_format($member->lossamount, 2));
            }

            if($member->diffdays == null){
                $member->diffdays = '-';
            }

            if($member->lossamount != '-' && $member->diffdays != '-' ){
                if($member->lossamount != 0 && $member->diffdays != 0){
                    $member->setAttribute('avg', $member->lossamount / $member->diffdays);
                }else if($member->lossamount != 0 && $member->diffdays == 0){
                    $member->setAttribute('avg',$member->lossamount);
                }else {
                    $member->setAttribute('avg', 0);
                } 
            }else{
                $member->setAttribute('avg', 99999999);
            }

        endforeach;

        $members = $members->sortBy('avg')->values();

        foreach ($members as $member) {
            $member->setAttribute('rank',$counter);
            $counter++;
        }
        return response()->json( $members, $this->successStatus);
    }

    public function get_top_measurement_losers_v3(){
        $user = Auth::user();

        $members = $members = User::selectRaw('users.id, member_personals.photo, users.name, users.id as memberid, member_personals.cellphone as memberphone, (SELECT workout_date FROM workouts WHERE user_id = users.id AND workout_date <= \''.Carbon::today()->toDateString().' 23:59:59\' ORDER BY workout_date DESC LIMIT 1) as workout_date,
                (SELECT total_measurement -
                (SELECT total_measurement FROM weigh_measures mi WHERE mi.wm_date < m.wm_date AND user_id = users.id 
                ORDER BY wm_date DESC LIMIT 1)
                FROM  weigh_measures m WHERE user_id = users.id AND m.wm_date > \''.Carbon::now()->startOfMonth()->format('Y-m-d').'\' AND m.wm_date < \''.Carbon::now()->endOfMonth()->format('Y-m-d').'\' ORDER BY wm_date DESC LIMIT 1) as lossamount,
                EXTRACT( EPOCH FROM (SELECT wm_date -
                (SELECT wm_date FROM weigh_measures mi WHERE mi.wm_date < m.wm_date AND user_id = users.id
                ORDER BY wm_date DESC LIMIT 1)
                FROM  weigh_measures m WHERE user_id = users.id ORDER BY wm_date DESC LIMIT 1))/86400 as diffdays, 
                (SELECT count(workout_date) FROM workouts WHERE user_id = users.id AND workout_date >= (SELECT mi.wm_date FROM weigh_measures mi WHERE mi.wm_date < (SELECT m.wm_date FROM  weigh_measures m WHERE user_id = users.id AND m.wm_date > \''.Carbon::now()->startOfMonth()->format('Y-m-d').'\' AND m.wm_date < \''.Carbon::now()->endOfMonth()->format('Y-m-d').'\' ORDER BY wm_date DESC LIMIT 1) AND user_id = users.id ORDER BY wm_date DESC LIMIT 1)
                AND workout_date <= (SELECT m.wm_date
                    FROM  weigh_measures m WHERE user_id = users.id AND m.wm_date > \''.Carbon::now()->startOfMonth()->format('Y-m-d').'\' AND m.wm_date < \''.Carbon::now()->endOfMonth()->format('Y-m-d').'\' ORDER BY wm_date DESC LIMIT 1)) as wo_count')
                ->where(function ($q) {
                    $q->where('member_status.status', '=', 'Active');
                })->where(function ($q) use($user) {
                    $q->where('coach_members.coach_id','=',$user->id);
                });

            $members = $members->leftJoin('workouts', function ($join) {
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

            $members = $members->join('coach_members', function($join){
                $join->on('users.id','=','coach_members.member_id');
            });

            $members = $members->groupBy('users.id','users.name','member_personals.photo','member_personals.phone',
                'member_personals.cellphone','member_status.last_wm','member_status.wm_schedule','users.created_at',
                'club_infos.club_code')
                ->orderBy('lossamount', 'asc')
                ->orderBy('diffdays', 'asc')
                ->get();

        $counter = 1;
        foreach ($members as $index => $member):
            if (!empty($member->photo)):
                $member->setAttribute('photo', 'member_photos/'.$member->photo);
            else:
                $member->setAttribute('photo', 'img.png');
            endif;

            if (!empty($member->workout_date)):
                $diff_day = Carbon::today()->diffInDays(Carbon::parse($member->workout_date));
                $member->setAttribute('days', $diff_day);
            else:
                $member->setAttribute('days', 999);
            endif;

            if($member->lossamount == null){
                $member->lossamount = '99999999';
                $members->forget($index);
            } else {
                $member->setAttribute('lossamount', number_format($member->lossamount, 2));
            }

            if($member->diffdays == null){
                $member->diffdays = '-';
            }

        endforeach;

        $members = $members->sortBy('lossamount')->values();

        foreach ($members as $member) {
            $member->setAttribute('rank',$counter);
            $counter++;
        }
        return response()->json( $members, $this->successStatus);
    }

    public function get_top_measurement_losers_v4(){
        $user = Auth::user();

        $members = $members = User::selectRaw('users.id, member_personals.photo, users.name, users.id as memberid, member_personals.cellphone as memberphone, (SELECT workout_date FROM workouts WHERE user_id = users.id AND workout_date <= \''.Carbon::today()->toDateString().' 23:59:59\' ORDER BY workout_date DESC LIMIT 1) as workout_date')
                ->where(function ($q) {
                    $q->where('member_status.status', '=', 'Active');
                })->where(function ($q) use($user) {
                    $q->where('coach_members.coach_id','=',$user->id);
                })->whereHas('member_weighmeasure',function($s){
                    $s->where('wm_date','>=', new Carbon('first day of this month'));
                    $s->where('wm_date','<=', new Carbon('last day of this month'));
                });

        $members = $members->leftJoin('workouts', function ($join) {
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

        $members = $members->join('coach_members', function($join){
            $join->on('users.id','=','coach_members.member_id');
        });

        $members = $members->groupBy('users.id','users.name','member_personals.photo','member_personals.phone',
            'member_personals.cellphone','member_status.last_wm','member_status.wm_schedule','users.created_at',
            'club_infos.club_code')
            ->get();

        $counter = 1;
        foreach ($members as $index => $member):
            $wm_rank = WMRank::where('user_id','=',$member->id)->where('type','=','monthly')->first();

            if(empty($wm_rank)){
                $member->setAttribute('lossamount',null);
                $member->setAttribute('diffdays', 999);
                $member->setAttribute('wo_count',0);
            }else{
                $member->setAttribute('lossamount',$wm_rank->wm_one == 0 ? null : $wm_rank->size_diff);
                $member->setAttribute('diffdays',$wm_rank->wm_one == 0 ? 999 : $wm_rank->day_diff);
                $member->setAttribute('wo_count',$wm_rank->wo_count);
            }

            if (!empty($member->photo)):
                $member->setAttribute('photo', 'member_photos/'.$member->photo);
            else:
                $member->setAttribute('photo', 'img.png');
            endif;

            if (!empty($member->workout_date)):
                $diff_day = Carbon::today()->diffInDays(Carbon::parse($member->workout_date));
                $member->setAttribute('days', $diff_day);
            else:
                $member->setAttribute('days', 999);
            endif;

            if($member->lossamount == null){
                $member->lossamount = '99999999';
                $members->forget($index);
            } else {
                $member->setAttribute('lossamount', number_format($member->lossamount, 2));
            }

            if($member->diffdays == null){
                $member->diffdays = '-';
            }

        endforeach;

        $members = $members->sortBy('lossamount')->values();

        foreach ($members as $member) {
            $member->setAttribute('rank',$counter);
            $counter++;
        }
        return response()->json( $members, $this->successStatus);
    }

    public function get_top_measurement_losers_overall(){
        $user = Auth::user();

        $members = $members = User::selectRaw('users.id, member_personals.photo, users.name, users.id as memberid, member_personals.cellphone as memberphone, (SELECT workout_date FROM workouts WHERE user_id = users.id AND workout_date <= \''.Carbon::today()->toDateString().' 23:59:59\' ORDER BY workout_date DESC LIMIT 1) as workout_date,
                (SELECT total_measurement -
                (SELECT total_measurement FROM weigh_measures mi WHERE mi.wm_date < m.wm_date AND user_id = users.id 
                ORDER BY wm_date ASC LIMIT 1)
                FROM  weigh_measures m WHERE user_id = users.id ORDER BY wm_date DESC LIMIT 1) as lossamount,
                EXTRACT( EPOCH FROM (SELECT wm_date -
                (SELECT wm_date FROM weigh_measures mi WHERE mi.wm_date < m.wm_date AND user_id = users.id
                ORDER BY wm_date ASC LIMIT 1)
                FROM  weigh_measures m WHERE user_id = users.id ORDER BY wm_date DESC LIMIT 1))/86400 as diffdays, 
                (SELECT count(workout_date) FROM workouts WHERE user_id = users.id AND workout_date >= (SELECT mi.wm_date FROM weigh_measures mi WHERE mi.wm_date < (SELECT m.wm_date FROM weigh_measures m WHERE user_id = users.id ORDER BY wm_date DESC LIMIT 1) AND user_id = users.id
                ORDER BY wm_date ASC LIMIT 1) AND workout_date <= (SELECT m.wm_date
                FROM  weigh_measures m WHERE user_id = users.id ORDER BY wm_date DESC LIMIT 1)) as wo_count')
                ->where(function ($q) {
                    $q->where('member_status.status', '=', 'Active');
                })->where(function ($q) use($user) {
                    $q->where('coach_members.coach_id','=',$user->id);
                })->where(function($s){
                    $s->where('users.role_id','=',7);
                });

            $members = $members->leftJoin('workouts', function ($join) {
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

            $members = $members->join('coach_members', function($join){
                $join->on('users.id','=','coach_members.member_id');
            });

            $members = $members->groupBy('users.id','users.name','member_personals.photo','member_personals.phone',
                'member_personals.cellphone','member_status.last_wm','member_status.wm_schedule','users.created_at',
                'club_infos.club_code')
                ->orderBy('lossamount', 'asc')
                ->orderBy('diffdays', 'asc')
                ->get();

        $counter = 1;
        foreach ($members as $member):
            if (!empty($member->photo)):
                $member->setAttribute('photo', 'member_photos/'.$member->photo);
            else:
                $member->setAttribute('photo', 'img.png');
            endif;

            if (!empty($member->workout_date)):
                $diff_day = Carbon::today()->diffInDays(Carbon::parse($member->workout_date));
                $member->setAttribute('days', $diff_day);
            else:
                $member->setAttribute('days', 999);
            endif;

            if($member->lossamount == null){
                $member->lossamount = '99999999';
            } else {
                $member->setAttribute('lossamount', number_format($member->lossamount, 2));
            }

            if($member->diffdays == null){
                $member->diffdays = '-';
            }

        endforeach;

        $members = $members->sortBy('lossamount')->values();

        foreach ($members as $member) {
            $member->setAttribute('rank',$counter);
            $counter++;
        }
        return response()->json( $members, $this->successStatus);
    }

    public function get_top_measurement_losers_overall_v2(){
        $user = Auth::user();

        $members = $members = User::selectRaw('users.id, member_personals.photo, users.name, users.id as memberid, member_personals.cellphone as memberphone, (SELECT workout_date FROM workouts WHERE user_id = users.id AND workout_date <= \''.Carbon::today()->toDateString().' 23:59:59\' ORDER BY workout_date DESC LIMIT 1) as workout_date')
                ->where(function ($q) {
                    $q->where('member_status.status', '=', 'Active');
                })->where(function ($q) use($user) {
                    $q->where('coach_members.coach_id','=',$user->id);
                })->where(function($s){
                    $s->where('users.role_id','=',7);
                });

            $members = $members->leftJoin('workouts', function ($join) {
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

            $members = $members->join('coach_members', function($join){
                $join->on('users.id','=','coach_members.member_id');
            });

            $members = $members->groupBy('users.id','users.name','member_personals.photo','member_personals.phone',
                'member_personals.cellphone','member_status.last_wm','member_status.wm_schedule','users.created_at',
                'club_infos.club_code')
                ->get();

        $counter = 1;
        foreach ($members as $member):
            $wm_rank = WMRank::where('user_id','=',$member->id)->where('type','=','overall')->first();

            if(empty($wm_rank)){
                $member->setAttribute('lossamount',null);
                $member->setAttribute('diffdays', 999);
                $member->setAttribute('wo_count',0);
            }else{
                $member->setAttribute('lossamount',$wm_rank->wm_one == 0 ? null : $wm_rank->size_diff);
                $member->setAttribute('diffdays',$wm_rank->wm_one == 0 ? 999 : $wm_rank->day_diff);
                $member->setAttribute('wo_count',$wm_rank->wo_count);
            }

            if (!empty($member->photo)):
                $member->setAttribute('photo', 'member_photos/'.$member->photo);
            else:
                $member->setAttribute('photo', 'img.png');
            endif;

            if (!empty($member->workout_date)):
                $diff_day = Carbon::today()->diffInDays(Carbon::parse($member->workout_date));
                $member->setAttribute('days', $diff_day);
            else:
                $member->setAttribute('days', 999);
            endif;

            if($member->lossamount == null){
                $member->lossamount = '99999999';
            } else {
                $member->setAttribute('lossamount', number_format($member->lossamount, 2));
            }

            if($member->diffdays == null){
                $member->diffdays = '-';
            }

        endforeach;

        $members = $members->sortBy('lossamount')->values();

        foreach ($members as $member) {
            $member->setAttribute('rank',$counter);
            $counter++;
        }
        return response()->json( $members, $this->successStatus);
    }

    public function get_top_measurement_losers_by_club(){
        $user = Auth::user();

        $members = $members = User::selectRaw('users.id, member_personals.photo, users.name, users.id as memberid, member_personals.cellphone as memberphone, (SELECT workout_date FROM workouts WHERE user_id = users.id AND workout_date <= \''.Carbon::today()->toDateString().' 23:59:59\' ORDER BY workout_date DESC LIMIT 1) as workout_date,
                (SELECT total_measurement -
                (SELECT total_measurement FROM weigh_measures mi WHERE mi.wm_date < m.wm_date AND user_id = users.id 
                ORDER BY wm_date DESC LIMIT 1)
                FROM  weigh_measures m WHERE user_id = users.id AND m.wm_date > \''.Carbon::now()->startOfMonth()->format('Y-m-d').'\' AND m.wm_date < \''.Carbon::now()->endOfMonth()->format('Y-m-d').'\' ORDER BY wm_date DESC LIMIT 1) as lossamount,
                EXTRACT( EPOCH FROM (SELECT wm_date -
                (SELECT wm_date FROM weigh_measures mi WHERE mi.wm_date < m.wm_date AND user_id = users.id
                ORDER BY wm_date DESC LIMIT 1)
                FROM  weigh_measures m WHERE user_id = users.id ORDER BY wm_date DESC LIMIT 1))/86400 as diffdays, 
                (SELECT count(workout_date) FROM workouts WHERE user_id = users.id AND workout_date >= (SELECT mi.wm_date FROM weigh_measures mi WHERE mi.wm_date < (SELECT m.wm_date FROM weigh_measures m WHERE user_id = users.id AND m.wm_date > \''.Carbon::now()->startOfMonth()->format('Y-m-d').'\' AND m.wm_date < \''.Carbon::now()->endOfMonth()->format('Y-m-d').'\' ORDER BY wm_date DESC LIMIT 1) AND user_id = users.id
                ORDER BY wm_date ASC LIMIT 1) AND workout_date <= (SELECT m.wm_date
                FROM  weigh_measures m WHERE user_id = users.id AND m.wm_date > \''.Carbon::now()->startOfMonth()->format('Y-m-d').'\' AND m.wm_date < \''.Carbon::now()->endOfMonth()->format('Y-m-d').'\' ORDER BY wm_date DESC LIMIT 1)) as wo_count')
                ->where(function ($q) {
                    $q->where('member_status.status', '=', 'Active');
                })->where(function($r)use($user){
                    $r->where('users.club_id','=',$user->club_id);
                })->where(function($s){
                    $s->where('users.role_id','=',7);
                });

            $members = $members->leftJoin('workouts', function ($join) {
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
                ->orderBy('lossamount', 'asc')
                ->orderBy('diffdays', 'asc')
                ->get();

        $counter = 1;
        foreach ($members as $index => $member):
            if (!empty($member->photo)):
                $member->setAttribute('photo', 'member_photos/'.$member->photo);
            else:
                $member->setAttribute('photo', 'img.png');
            endif;

            if (!empty($member->workout_date)):
                $diff_day = Carbon::today()->diffInDays(Carbon::parse($member->workout_date));
                $member->setAttribute('days', $diff_day);
            else:
                $member->setAttribute('days', 999);
            endif;

            if($member->lossamount == null){
                $member->lossamount = '99999999';
                $members->forget($index);
            } else {
                $member->setAttribute('lossamount', number_format($member->lossamount, 2));
            }

            if($member->diffdays == null){
                $member->diffdays = '-';
            }

        endforeach;

        $members = $members->sortBy('lossamount')->values();

        foreach ($members as $member) {
            $member->setAttribute('rank',$counter);
            $counter++;
        }
        return response()->json( $members, $this->successStatus);
    }

    public function get_top_measurement_losers_by_club_v2(){
        $user = Auth::user();

        $members = $members = User::selectRaw('users.id, member_personals.photo, users.name, users.id as memberid, member_personals.cellphone as memberphone, (SELECT workout_date FROM workouts WHERE user_id = users.id AND workout_date <= \''.Carbon::today()->toDateString().' 23:59:59\' ORDER BY workout_date DESC LIMIT 1) as workout_date')
                ->where(function ($q) {
                    $q->where('member_status.status', '=', 'Active');
                })->where(function($r)use($user){
                    $r->where('users.club_id','=',$user->club_id);
                })->where(function($s){
                    $s->where('users.role_id','=',7);
                })->whereHas('member_weighmeasure',function($s){
                    $s->where('wm_date','>=', new Carbon('first day of this month'));
                    $s->where('wm_date','<=', new Carbon('last day of this month'));
                });

            $members = $members->leftJoin('workouts', function ($join) {
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
                ->get();

        $counter = 1;
        foreach ($members as $index => $member):
            $wm_rank = WMRank::where('user_id','=',$member->id)->where('type','=','monthly')->first();

            if(empty($wm_rank)){
                $member->setAttribute('lossamount',null);
                $member->setAttribute('diffdays', 999);
                $member->setAttribute('wo_count',0);
            }else{
                $member->setAttribute('lossamount',$wm_rank->wm_one == 0 ? null : $wm_rank->size_diff);
                $member->setAttribute('diffdays',$wm_rank->wm_one == 0 ? 999 : $wm_rank->day_diff);
                $member->setAttribute('wo_count',$wm_rank->wo_count);
            }

            if (!empty($member->photo)):
                $member->setAttribute('photo', 'member_photos/'.$member->photo);
            else:
                $member->setAttribute('photo', 'img.png');
            endif;

            if (!empty($member->workout_date)):
                $diff_day = Carbon::today()->diffInDays(Carbon::parse($member->workout_date));
                $member->setAttribute('days', $diff_day);
            else:
                $member->setAttribute('days', 999);
            endif;

            if($member->lossamount == null){
                $member->lossamount = '99999999';
                $members->forget($index);
            } else {
                $member->setAttribute('lossamount', number_format($member->lossamount, 2));
            }

            if($member->diffdays == null){
                $member->diffdays = '-';
            }

        endforeach;

        $members = $members->sortBy('lossamount')->values();

        foreach ($members as $member) {
            $member->setAttribute('rank',$counter);
            $counter++;
        }
        return response()->json( $members, $this->successStatus);
    }

    public function get_top_measurement_losers_by_club_overall(){
        $user = Auth::user();

        $members = $members = User::selectRaw('users.id, member_personals.photo, users.name, users.id as memberid, member_personals.cellphone as memberphone, (SELECT workout_date FROM workouts WHERE user_id = users.id AND workout_date <= \''.Carbon::today()->toDateString().' 23:59:59\' ORDER BY workout_date DESC LIMIT 1) as workout_date,
                (SELECT total_measurement -
                (SELECT total_measurement FROM weigh_measures mi WHERE mi.wm_date < m.wm_date AND user_id = users.id 
                ORDER BY wm_date ASC LIMIT 1)
                FROM  weigh_measures m WHERE user_id = users.id ORDER BY wm_date DESC LIMIT 1) as lossamount,
                EXTRACT( EPOCH FROM (SELECT wm_date -
                (SELECT wm_date FROM weigh_measures mi WHERE mi.wm_date < m.wm_date AND user_id = users.id
                ORDER BY wm_date ASC LIMIT 1)
                FROM  weigh_measures m WHERE user_id = users.id ORDER BY wm_date DESC LIMIT 1))/86400 as diffdays, 
                (SELECT count(workout_date) FROM workouts WHERE user_id = users.id AND workout_date >= (SELECT mi.wm_date FROM weigh_measures mi WHERE mi.wm_date < (SELECT m.wm_date FROM weigh_measures m WHERE user_id = users.id ORDER BY wm_date DESC LIMIT 1) AND user_id = users.id
                ORDER BY wm_date ASC LIMIT 1) AND workout_date <= (SELECT m.wm_date
                FROM  weigh_measures m WHERE user_id = users.id ORDER BY wm_date DESC LIMIT 1)) as wo_count')
                ->where(function ($q) {
                    $q->where('member_status.status', '=', 'Active');
                })->where(function($r)use($user){
                    $r->where('users.club_id','=',$user->club_id);
                })->where(function($s){
                    $s->where('users.role_id','=',7);
                });

            $members = $members->leftJoin('workouts', function ($join) {
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

            $members = $members->join('coach_members', function($join){
                $join->on('users.id','=','coach_members.member_id');
            });

            $members = $members->groupBy('users.id','users.name','member_personals.photo','member_personals.phone',
                'member_personals.cellphone','member_status.last_wm','member_status.wm_schedule','users.created_at',
                'club_infos.club_code')
                ->orderBy('lossamount', 'asc')
                ->orderBy('diffdays', 'asc')
                ->get();

        $counter = 1;
        foreach ($members as $member):
            if (!empty($member->photo)):
                $member->setAttribute('photo', 'member_photos/'.$member->photo);
            else:
                $member->setAttribute('photo', 'img.png');
            endif;

            if (!empty($member->workout_date)):
                $diff_day = Carbon::today()->diffInDays(Carbon::parse($member->workout_date));
                $member->setAttribute('days', $diff_day);
            else:
                $member->setAttribute('days', 999);
            endif;

            if($member->lossamount == null){
                $member->lossamount = '99999999';
            } else {
                $member->setAttribute('lossamount', number_format($member->lossamount, 2));
            }

            if($member->diffdays == null){
                $member->diffdays = '-';
            }

        endforeach;

        $members = $members->sortBy('lossamount')->values();

        foreach ($members as $member) {
            $member->setAttribute('rank',$counter);
            $counter++;
        }
        return response()->json( $members, $this->successStatus);
    }

    public function get_top_measurement_losers_by_club_overall_v2(){
        $user = Auth::user();

        $members = $members = User::selectRaw('users.id, member_personals.photo, users.name, users.id as memberid, member_personals.cellphone as memberphone, (SELECT workout_date FROM workouts WHERE user_id = users.id AND workout_date <= \''.Carbon::today()->toDateString().' 23:59:59\' ORDER BY workout_date DESC LIMIT 1) as workout_date')
                ->where(function ($q) {
                    $q->where('member_status.status', '=', 'Active');
                })->where(function($r)use($user){
                    $r->where('users.club_id','=',$user->club_id);
                })->where(function($s){
                    $s->where('users.role_id','=',7);
                });

            $members = $members->leftJoin('workouts', function ($join) {
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

            $members = $members->join('coach_members', function($join){
                $join->on('users.id','=','coach_members.member_id');
            });

            $members = $members->groupBy('users.id','users.name','member_personals.photo','member_personals.phone',
                'member_personals.cellphone','member_status.last_wm','member_status.wm_schedule','users.created_at',
                'club_infos.club_code')
                ->get();

        $counter = 1;
        foreach ($members as $member):
            $wm_rank = WMRank::where('user_id','=',$member->id)->where('type','=','overall')->first();

            if(empty($wm_rank)){
                $member->setAttribute('lossamount',null);
                $member->setAttribute('diffdays', 999);
                $member->setAttribute('wo_count',0);
            }else{
                $member->setAttribute('lossamount',$wm_rank->wm_one == 0 ? null : $wm_rank->size_diff);
                $member->setAttribute('diffdays',$wm_rank->wm_one == 0 ? 999 : $wm_rank->day_diff);
                $member->setAttribute('wo_count',$wm_rank->wo_count);
            }

            if (!empty($member->photo)):
                $member->setAttribute('photo', 'member_photos/'.$member->photo);
            else:
                $member->setAttribute('photo', 'img.png');
            endif;

            if (!empty($member->workout_date)):
                $diff_day = Carbon::today()->diffInDays(Carbon::parse($member->workout_date));
                $member->setAttribute('days', $diff_day);
            else:
                $member->setAttribute('days', 999);
            endif;

            if($member->lossamount == null){
                $member->lossamount = '99999999';
            } else {
                $member->setAttribute('lossamount', number_format($member->lossamount, 2));
            }

            if($member->diffdays == null){
                $member->diffdays = '-';
            }

        endforeach;

        $members = $members->sortBy('lossamount')->values();

        foreach ($members as $member) {
            $member->setAttribute('rank',$counter);
            $counter++;
        }
        return response()->json( $members, $this->successStatus);
    }

    //TOP BODY FAT LOSER
    public function get_top_body_fat_losers(){
        $user = Auth::user();

        $members = $members = User::selectRaw('users.id, member_personals.photo, users.name,
                (SELECT total_body_fat -
                (SELECT total_body_fat FROM weigh_measures mi WHERE mi.wm_date < m.wm_date AND user_id = users.id 
                ORDER BY wm_date DESC LIMIT 1)
                FROM  weigh_measures m WHERE user_id = users.id ORDER BY wm_date DESC LIMIT 1) as lossamount,
                EXTRACT( EPOCH FROM (SELECT wm_date -
                (SELECT wm_date FROM weigh_measures mi WHERE mi.wm_date < m.wm_date AND user_id = users.id
                ORDER BY wm_date DESC LIMIT 1)
                FROM  weigh_measures m WHERE user_id = users.id ORDER BY wm_date DESC LIMIT 1))/86400 as diffdays')
                ->where(function ($q) {
                    $q->where('member_status.status', '=', 'Active')
                        ->orWhere('member_status.status', '=', 'Hold');
                })->where(function ($q) use($user) {
                    $q->where('coach_members.coach_id','=',$user->id);
                });

            $members = $members->leftJoin('workouts', function ($join) {
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

            $members = $members->join('coach_members', function($join){
                $join->on('users.id','=','coach_members.member_id');
            });

            $members = $members->groupBy('users.id','users.name','member_personals.photo','member_personals.phone',
                'member_personals.cellphone','member_status.last_wm','member_status.wm_schedule','users.created_at',
                'club_infos.club_code')
                ->orderBy('lossamount', 'asc')
                ->orderBy('diffdays', 'asc')
                ->get();

        $counter = 1;
        foreach ($members as $member):
            if (!empty($member->photo)):
                $member->setAttribute('photo', 'member_photos/'.$member->photo);
            else:
                $member->setAttribute('photo', 'img.png');
            endif;

            if($member->lossamount == null){
                $member->lossamount = '-';
            } else {
                $member->setAttribute('lossamount', number_format($member->lossamount, 2));
            }

            if($member->diffdays == null){
                $member->diffdays = '-';
            }

            $member->setAttribute('rank',$counter);
            $counter++;
        endforeach;

        return response()->json( $members, $this->successStatus);
    }

    public function get_top_body_fat_losers_v2(){
        $user = Auth::user();

        $members = $members = User::selectRaw('users.id, member_personals.photo, users.name,
                (SELECT total_body_fat -
                (SELECT total_body_fat FROM weigh_measures mi WHERE mi.wm_date < m.wm_date AND user_id = users.id 
                ORDER BY wm_date DESC LIMIT 1)
                FROM  weigh_measures m WHERE user_id = users.id ORDER BY wm_date DESC LIMIT 1) as lossamount,
                EXTRACT( EPOCH FROM (SELECT wm_date -
                (SELECT wm_date FROM weigh_measures mi WHERE mi.wm_date < m.wm_date AND user_id = users.id
                ORDER BY wm_date DESC LIMIT 1)
                FROM  weigh_measures m WHERE user_id = users.id ORDER BY wm_date DESC LIMIT 1))/86400 as diffdays')
                ->where(function ($q) {
                    $q->where('member_status.status', '=', 'Active')
                        ->orWhere('member_status.status', '=', 'Hold');
                })->where(function ($q) use($user) {
                    $q->where('coach_members.coach_id','=',$user->id);
                });

            $members = $members->leftJoin('workouts', function ($join) {
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

            $members = $members->join('coach_members', function($join){
                $join->on('users.id','=','coach_members.member_id');
            });

            $members = $members->groupBy('users.id','users.name','member_personals.photo','member_personals.phone',
                'member_personals.cellphone','member_status.last_wm','member_status.wm_schedule','users.created_at',
                'club_infos.club_code')
                ->orderBy('lossamount', 'asc')
                ->orderBy('diffdays', 'asc')
                ->get();

        $counter = 1;
        foreach ($members as $member):
            if (!empty($member->photo)):
                $member->setAttribute('photo', 'member_photos/'.$member->photo);
            else:
                $member->setAttribute('photo', 'img.png');
            endif;

            if($member->lossamount == null){
                $member->lossamount = '-';
            } else {
                $member->setAttribute('lossamount', number_format($member->lossamount, 2));
            }

            if($member->diffdays == null){
                $member->diffdays = '-';
            }

            if($member->lossamount != '-' && $member->diffdays != '-' ){
                if($member->lossamount != 0 && $member->diffdays != 0){
                    $member->setAttribute('avg', $member->lossamount / $member->diffdays);
                }else if($member->lossamount != 0 && $member->diffdays == 0){
                    $member->setAttribute('avg',$member->lossamount);
                }else {
                    $member->setAttribute('avg', 0);
                } 
            }else{
                $member->setAttribute('avg', 99999999);
            }

        endforeach;

        $members = $members->sortBy('avg')->values();

        foreach ($members as $member) {
            $member->setAttribute('rank',$counter);
            $counter++;
        }

        return response()->json( $members, $this->successStatus);
    }

    public function get_top_body_fat_losers_v3(){
        $user = Auth::user();

        $members = $members = User::selectRaw('users.id, member_personals.photo, users.name, users.id as memberid, member_personals.cellphone as memberphone, (SELECT workout_date FROM workouts WHERE user_id = users.id AND workout_date <= \''.Carbon::today()->toDateString().' 23:59:59\' ORDER BY workout_date DESC LIMIT 1) as workout_date,
                (SELECT total_body_fat -
                (SELECT total_body_fat FROM weigh_measures mi WHERE mi.wm_date < m.wm_date AND user_id = users.id 
                ORDER BY wm_date DESC LIMIT 1)
                FROM  weigh_measures m WHERE user_id = users.id AND m.wm_date > \''.Carbon::now()->startOfMonth()->format('Y-m-d').'\' AND m.wm_date < \''.Carbon::now()->endOfMonth()->format('Y-m-d').'\' ORDER BY wm_date DESC LIMIT 1) as lossamount,
                EXTRACT( EPOCH FROM (SELECT wm_date -
                (SELECT wm_date FROM weigh_measures mi WHERE mi.wm_date < m.wm_date AND user_id = users.id
                ORDER BY wm_date DESC LIMIT 1)
                FROM  weigh_measures m WHERE user_id = users.id ORDER BY wm_date DESC LIMIT 1))/86400 as diffdays,
                (SELECT count(workout_date) FROM workouts WHERE user_id = users.id AND workout_date >= (SELECT mi.wm_date FROM weigh_measures mi WHERE mi.wm_date < (SELECT m.wm_date FROM  weigh_measures m WHERE user_id = users.id AND m.wm_date > \''.Carbon::now()->startOfMonth()->format('Y-m-d').'\' AND m.wm_date < \''.Carbon::now()->endOfMonth()->format('Y-m-d').'\' ORDER BY wm_date DESC LIMIT 1) AND user_id = users.id ORDER BY wm_date DESC LIMIT 1)
                AND workout_date <= (SELECT m.wm_date
                    FROM  weigh_measures m WHERE user_id = users.id AND m.wm_date > \''.Carbon::now()->startOfMonth()->format('Y-m-d').'\' AND m.wm_date < \''.Carbon::now()->endOfMonth()->format('Y-m-d').'\' ORDER BY wm_date DESC LIMIT 1)) as wo_count')
                ->where(function ($q) {
                    $q->where('member_status.status', '=', 'Active');
                })->where(function ($q) use($user) {
                    $q->where('coach_members.coach_id','=',$user->id);
                });

            $members = $members->leftJoin('workouts', function ($join) {
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

            $members = $members->join('coach_members', function($join){
                $join->on('users.id','=','coach_members.member_id');
            });

            $members = $members->groupBy('users.id','users.name','member_personals.photo','member_personals.phone',
                'member_personals.cellphone','member_status.last_wm','member_status.wm_schedule','users.created_at',
                'club_infos.club_code')
                ->orderBy('lossamount', 'asc')
                ->orderBy('diffdays', 'asc')
                ->get();

        $counter = 1;
        foreach ($members as $index => $member):
            if (!empty($member->photo)):
                $member->setAttribute('photo', 'member_photos/'.$member->photo);
            else:
                $member->setAttribute('photo', 'img.png');
            endif;

            if (!empty($member->workout_date)):
                $diff_day = Carbon::today()->diffInDays(Carbon::parse($member->workout_date));
                $member->setAttribute('days', $diff_day);
            else:
                $member->setAttribute('days', 999);
            endif;

            if($member->lossamount == null){
                $member->lossamount = '99999999';
                $members->forget($index);
            } else {
                $member->setAttribute('lossamount', number_format($member->lossamount, 2));
            }

            if($member->diffdays == null){
                $member->diffdays = '-';
            }

        endforeach;

        $members = $members->sortBy('lossamount')->values();

        foreach ($members as $member) {
            $member->setAttribute('rank',$counter);
            $counter++;
        }

        return response()->json( $members, $this->successStatus);
    }

    public function get_top_body_fat_losers_v4(){
        $user = Auth::user();

        $members = $members = User::selectRaw('users.id, member_personals.photo, users.name, users.id as memberid, member_personals.cellphone as memberphone, (SELECT workout_date FROM workouts WHERE user_id = users.id AND workout_date <= \''.Carbon::today()->toDateString().' 23:59:59\' ORDER BY workout_date DESC LIMIT 1) as workout_date')
                ->where(function ($q) {
                    $q->where('member_status.status', '=', 'Active');
                })->where(function ($q) use($user) {
                    $q->where('coach_members.coach_id','=',$user->id);
                })->whereHas('member_weighmeasure',function($s){
                    $s->where('wm_date','>=', new Carbon('first day of this month'));
                    $s->where('wm_date','<=', new Carbon('last day of this month'));
                });

            $members = $members->leftJoin('workouts', function ($join) {
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

            $members = $members->join('coach_members', function($join){
                $join->on('users.id','=','coach_members.member_id');
            });

            $members = $members->groupBy('users.id','users.name','member_personals.photo','member_personals.phone',
                'member_personals.cellphone','member_status.last_wm','member_status.wm_schedule','users.created_at',
                'club_infos.club_code')
                ->get();

        $counter = 1;
        foreach ($members as $index => $member):
            $wm_rank = WMRank::where('user_id','=',$member->id)->where('type','=','monthly')->first();

            if(empty($wm_rank)){
                $member->setAttribute('lossamount',null);
                $member->setAttribute('diffdays', 999);
                $member->setAttribute('wo_count',0);
            }else{
                $member->setAttribute('lossamount',$wm_rank->wm_one == 0 ? null : $wm_rank->body_fat_diff);
                $member->setAttribute('diffdays',$wm_rank->wm_one == 0 ? 999 : $wm_rank->day_diff);
                $member->setAttribute('wo_count',$wm_rank->wo_count);
            }
            if (!empty($member->photo)):
                $member->setAttribute('photo', 'member_photos/'.$member->photo);
            else:
                $member->setAttribute('photo', 'img.png');
            endif;

            if (!empty($member->workout_date)):
                $diff_day = Carbon::today()->diffInDays(Carbon::parse($member->workout_date));
                $member->setAttribute('days', $diff_day);
            else:
                $member->setAttribute('days', 999);
            endif;

            if($member->lossamount == null){
                $member->lossamount = '99999999';
                $members->forget($index);
            } else {
                $member->setAttribute('lossamount', number_format($member->lossamount, 2));
            }

            if($member->diffdays == null){
                $member->diffdays = '-';
            }

        endforeach;

        $members = $members->sortBy('lossamount')->values();

        foreach ($members as $member) {
            $member->setAttribute('rank',$counter);
            $counter++;
        }

        return response()->json( $members, $this->successStatus);
    }

    public function get_top_body_fat_losers_overall(){
        $user = Auth::user();

        $members = $members = User::selectRaw('users.id, member_personals.photo, users.name, users.id as memberid, member_personals.cellphone as memberphone, (SELECT workout_date FROM workouts WHERE user_id = users.id AND workout_date <= \''.Carbon::today()->toDateString().' 23:59:59\' ORDER BY workout_date DESC LIMIT 1) as workout_date,
                (SELECT total_body_fat -
                (SELECT total_body_fat FROM weigh_measures mi WHERE mi.wm_date < m.wm_date AND user_id = users.id 
                ORDER BY wm_date ASC LIMIT 1)
                FROM  weigh_measures m WHERE user_id = users.id ORDER BY wm_date DESC LIMIT 1) as lossamount,
                EXTRACT( EPOCH FROM (SELECT wm_date -
                (SELECT wm_date FROM weigh_measures mi WHERE mi.wm_date < m.wm_date AND user_id = users.id
                ORDER BY wm_date ASC LIMIT 1)
                FROM  weigh_measures m WHERE user_id = users.id ORDER BY wm_date DESC LIMIT 1))/86400 as diffdays, 
                (SELECT count(workout_date) FROM workouts WHERE user_id = users.id AND workout_date >= (SELECT mi.wm_date FROM weigh_measures mi WHERE mi.wm_date < (SELECT m.wm_date FROM  weigh_measures m WHERE user_id = users.id ORDER BY wm_date DESC LIMIT 1) AND user_id = users.id
                ORDER BY wm_date ASC LIMIT 1) AND workout_date <= (SELECT m.wm_date
                FROM  weigh_measures m WHERE user_id = users.id ORDER BY wm_date DESC LIMIT 1)) as wo_count')
                ->where(function ($q) {
                    $q->where('member_status.status', '=', 'Active');
                })->where(function ($q) use($user) {
                    $q->where('coach_members.coach_id','=',$user->id);
                });

            $members = $members->leftJoin('workouts', function ($join) {
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

            $members = $members->join('coach_members', function($join){
                $join->on('users.id','=','coach_members.member_id');
            });

            $members = $members->groupBy('users.id','users.name','member_personals.photo','member_personals.phone',
                'member_personals.cellphone','member_status.last_wm','member_status.wm_schedule','users.created_at',
                'club_infos.club_code')
                ->orderBy('lossamount', 'asc')
                ->orderBy('diffdays', 'asc')
                ->get();

        $counter = 1;
        foreach ($members as $member):
            if (!empty($member->photo)):
                $member->setAttribute('photo', 'member_photos/'.$member->photo);
            else:
                $member->setAttribute('photo', 'img.png');
            endif;

            if (!empty($member->workout_date)):
                $diff_day = Carbon::today()->diffInDays(Carbon::parse($member->workout_date));
                $member->setAttribute('days', $diff_day);
            else:
                $member->setAttribute('days', 999);
            endif;

            if($member->lossamount == null){
                $member->lossamount = '99999999';
            } else {
                $member->setAttribute('lossamount', number_format($member->lossamount, 2));
            }

            if($member->diffdays == null){
                $member->diffdays = '-';
            }

        endforeach;

        $members = $members->sortBy('lossamount')->values();

        foreach ($members as $member) {
            $member->setAttribute('rank',$counter);
            $counter++;
        }

        return response()->json( $members, $this->successStatus);
    }

    public function get_top_body_fat_losers_overall_v2(){
        $user = Auth::user();

        $members = $members = User::selectRaw('users.id, member_personals.photo, users.name, users.id as memberid, member_personals.cellphone as memberphone, (SELECT workout_date FROM workouts WHERE user_id = users.id AND workout_date <= \''.Carbon::today()->toDateString().' 23:59:59\' ORDER BY workout_date DESC LIMIT 1) as workout_date')
                ->where(function ($q) {
                    $q->where('member_status.status', '=', 'Active');
                })->where(function ($q) use($user) {
                    $q->where('coach_members.coach_id','=',$user->id);
                });

            $members = $members->leftJoin('workouts', function ($join) {
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

            $members = $members->join('coach_members', function($join){
                $join->on('users.id','=','coach_members.member_id');
            });

            $members = $members->groupBy('users.id','users.name','member_personals.photo','member_personals.phone',
                'member_personals.cellphone','member_status.last_wm','member_status.wm_schedule','users.created_at',
                'club_infos.club_code')
                ->get();

        $counter = 1;
        foreach ($members as $member):
            $wm_rank = WMRank::where('user_id','=',$member->id)->where('type','=','overall')->first();

            if(empty($wm_rank)){
                $member->setAttribute('lossamount',null);
                $member->setAttribute('diffdays', 999);
                $member->setAttribute('wo_count',0);
            }else{
                $member->setAttribute('lossamount',$wm_rank->wm_one == 0 ? null : $wm_rank->body_fat_diff);
                $member->setAttribute('diffdays',$wm_rank->wm_one == 0 ? 999 : $wm_rank->day_diff);
                $member->setAttribute('wo_count',$wm_rank->wo_count);
            }

            if (!empty($member->photo)):
                $member->setAttribute('photo', 'member_photos/'.$member->photo);
            else:
                $member->setAttribute('photo', 'img.png');
            endif;

            if (!empty($member->workout_date)):
                $diff_day = Carbon::today()->diffInDays(Carbon::parse($member->workout_date));
                $member->setAttribute('days', $diff_day);
            else:
                $member->setAttribute('days', 999);
            endif;

            if($member->lossamount == null){
                $member->lossamount = '99999999';
            } else {
                $member->setAttribute('lossamount', number_format($member->lossamount, 2));
            }

            if($member->diffdays == null){
                $member->diffdays = '-';
            }

        endforeach;

        $members = $members->sortBy('lossamount')->values();

        foreach ($members as $member) {
            $member->setAttribute('rank',$counter);
            $counter++;
        }

        return response()->json( $members, $this->successStatus);
    }

    public function get_top_body_fat_losers_by_club(){
        $user = Auth::user();

        $members = $members = User::selectRaw('users.id, member_personals.photo, users.name, users.id as memberid, member_personals.cellphone as memberphone, (SELECT workout_date FROM workouts WHERE user_id = users.id AND workout_date <= \''.Carbon::today()->toDateString().' 23:59:59\' ORDER BY workout_date DESC LIMIT 1) as workout_date,
                (SELECT total_body_fat -
                (SELECT total_body_fat FROM weigh_measures mi WHERE mi.wm_date < m.wm_date AND user_id = users.id 
                ORDER BY wm_date DESC LIMIT 1)
                FROM  weigh_measures m WHERE user_id = users.id AND m.wm_date > \''.Carbon::now()->startOfMonth()->format('Y-m-d').'\' AND m.wm_date < \''.Carbon::now()->endOfMonth()->format('Y-m-d').'\' ORDER BY wm_date DESC LIMIT 1) as lossamount,
                EXTRACT( EPOCH FROM (SELECT wm_date -
                (SELECT wm_date FROM weigh_measures mi WHERE mi.wm_date < m.wm_date AND user_id = users.id
                ORDER BY wm_date DESC LIMIT 1)
                FROM  weigh_measures m WHERE user_id = users.id ORDER BY wm_date DESC LIMIT 1))/86400 as diffdays, 
                (SELECT count(workout_date) FROM workouts WHERE user_id = users.id AND workout_date >= (SELECT mi.wm_date FROM weigh_measures mi WHERE mi.wm_date < (SELECT m.wm_date FROM weigh_measures m WHERE user_id = users.id AND m.wm_date > \''.Carbon::now()->startOfMonth()->format('Y-m-d').'\' AND m.wm_date < \''.Carbon::now()->endOfMonth()->format('Y-m-d').'\' ORDER BY wm_date DESC LIMIT 1) AND user_id = users.id
                ORDER BY wm_date ASC LIMIT 1) AND workout_date <= (SELECT m.wm_date
                FROM  weigh_measures m WHERE user_id = users.id AND m.wm_date > \''.Carbon::now()->startOfMonth()->format('Y-m-d').'\' AND m.wm_date < \''.Carbon::now()->endOfMonth()->format('Y-m-d').'\' ORDER BY wm_date DESC LIMIT 1)) as wo_count')
                ->where(function ($q) {
                    $q->where('member_status.status', '=', 'Active');
                })->where(function($r)use($user){
                    $r->where('users.club_id','=',$user->club_id);
                })->where(function($s){
                    $s->where('users.role_id','=',7);
                });

            $members = $members->leftJoin('workouts', function ($join) {
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
                ->orderBy('lossamount', 'asc')
                ->orderBy('diffdays', 'asc')
                ->get();

        $counter = 1;
        foreach ($members as $index => $member):
            if (!empty($member->photo)):
                $member->setAttribute('photo', 'member_photos/'.$member->photo);
            else:
                $member->setAttribute('photo', 'img.png');
            endif;

            if (!empty($member->workout_date)):
                $diff_day = Carbon::today()->diffInDays(Carbon::parse($member->workout_date));
                $member->setAttribute('days', $diff_day);
            else:
                $member->setAttribute('days', 999);
            endif;

            if($member->lossamount == null){
                $member->lossamount = '99999999';
                $members->forget($index);
            } else {
                $member->setAttribute('lossamount', number_format($member->lossamount, 2));
            }

            if($member->diffdays == null){
                $member->diffdays = '-';
            }

        endforeach;

        $members = $members->sortBy('lossamount')->values();

        foreach ($members as $member) {
            $member->setAttribute('rank',$counter);
            $counter++;
        }

        return response()->json( $members, $this->successStatus);
    }

    public function get_top_body_fat_losers_by_club_v2(){
        $user = Auth::user();

        $members = $members = User::selectRaw('users.id, member_personals.photo, users.name, users.id as memberid, member_personals.cellphone as memberphone, (SELECT workout_date FROM workouts WHERE user_id = users.id AND workout_date <= \''.Carbon::today()->toDateString().' 23:59:59\' ORDER BY workout_date DESC LIMIT 1) as workout_date')
                ->where(function ($q) {
                    $q->where('member_status.status', '=', 'Active');
                })->where(function($r)use($user){
                    $r->where('users.club_id','=',$user->club_id);
                })->where(function($s){
                    $s->where('users.role_id','=',7);
                })->whereHas('member_weighmeasure',function($s){
                    $s->where('wm_date','>=', new Carbon('first day of this month'));
                    $s->where('wm_date','<=', new Carbon('last day of this month'));
                });

            $members = $members->leftJoin('workouts', function ($join) {
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
                ->get();

        $counter = 1;
        foreach ($members as $index => $member):
            $wm_rank = WMRank::where('user_id','=',$member->id)->where('type','=','monthly')->first();

            if(empty($wm_rank)){
                $member->setAttribute('lossamount',null);
                $member->setAttribute('diffdays', 999);
                $member->setAttribute('wo_count',0);
            }else{
                $member->setAttribute('lossamount',$wm_rank->wm_one == 0 ? null : $wm_rank->body_fat_diff);
                $member->setAttribute('diffdays',$wm_rank->wm_one == 0 ? 999 : $wm_rank->day_diff);
                $member->setAttribute('wo_count',$wm_rank->wo_count);
            }

            if (!empty($member->photo)):
                $member->setAttribute('photo', 'member_photos/'.$member->photo);
            else:
                $member->setAttribute('photo', 'img.png');
            endif;

            if (!empty($member->workout_date)):
                $diff_day = Carbon::today()->diffInDays(Carbon::parse($member->workout_date));
                $member->setAttribute('days', $diff_day);
            else:
                $member->setAttribute('days', 999);
            endif;

            if($member->lossamount == null){
                $member->lossamount = '99999999';
                $members->forget($index);
            } else {
                $member->setAttribute('lossamount', number_format($member->lossamount, 2));
            }

            if($member->diffdays == null){
                $member->diffdays = '-';
            }

        endforeach;

        $members = $members->sortBy('lossamount')->values();

        foreach ($members as $member) {
            $member->setAttribute('rank',$counter);
            $counter++;
        }

        return response()->json( $members, $this->successStatus);
    }

    public function get_top_body_fat_losers_by_club_overall(){
        $user = Auth::user();

        $members = $members = User::selectRaw('users.id, member_personals.photo, users.name, users.id as memberid, member_personals.cellphone as memberphone, (SELECT workout_date FROM workouts WHERE user_id = users.id AND workout_date <= \''.Carbon::today()->toDateString().' 23:59:59\' ORDER BY workout_date DESC LIMIT 1) as workout_date,
                (SELECT total_body_fat -
                (SELECT total_body_fat FROM weigh_measures mi WHERE mi.wm_date < m.wm_date AND user_id = users.id 
                ORDER BY wm_date ASC LIMIT 1)
                FROM  weigh_measures m WHERE user_id = users.id ORDER BY wm_date DESC LIMIT 1) as lossamount,
                EXTRACT( EPOCH FROM (SELECT wm_date -
                (SELECT wm_date FROM weigh_measures mi WHERE mi.wm_date < m.wm_date AND user_id = users.id
                ORDER BY wm_date ASC LIMIT 1)
                FROM  weigh_measures m WHERE user_id = users.id ORDER BY wm_date DESC LIMIT 1))/86400 as diffdays, 
                (SELECT count(workout_date) FROM workouts WHERE user_id = users.id AND workout_date >= (SELECT mi.wm_date FROM weigh_measures mi WHERE mi.wm_date < (SELECT m.wm_date FROM  weigh_measures m WHERE user_id = users.id ORDER BY wm_date DESC LIMIT 1) AND user_id = users.id
                ORDER BY wm_date ASC LIMIT 1) AND workout_date <= (SELECT m.wm_date
                FROM  weigh_measures m WHERE user_id = users.id ORDER BY wm_date DESC LIMIT 1)) as wo_count')
                ->where(function ($q) {
                    $q->where('member_status.status', '=', 'Active');
                })->where(function($r)use($user){
                    $r->where('users.club_id','=',$user->club_id);
                });

            $members = $members->leftJoin('workouts', function ($join) {
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
                ->orderBy('lossamount', 'asc')
                ->orderBy('diffdays', 'asc')
                ->get();

        $counter = 1;
        foreach ($members as $member):
            if (!empty($member->photo)):
                $member->setAttribute('photo', 'member_photos/'.$member->photo);
            else:
                $member->setAttribute('photo', 'img.png');
            endif;

            if (!empty($member->workout_date)):
                $diff_day = Carbon::today()->diffInDays(Carbon::parse($member->workout_date));
                $member->setAttribute('days', $diff_day);
            else:
                $member->setAttribute('days', 999);
            endif;

            if($member->lossamount == null){
                $member->lossamount = '99999999';
            } else {
                $member->setAttribute('lossamount', number_format($member->lossamount, 2));
            }

            if($member->diffdays == null){
                $member->diffdays = '-';
            }

        endforeach;

        $members = $members->sortBy('lossamount')->values();

        foreach ($members as $member) {
            $member->setAttribute('rank',$counter);
            $counter++;
        }

        return response()->json( $members, $this->successStatus);
    }

    public function get_top_body_fat_losers_by_club_overall_v2(){
        $user = Auth::user();

        $members = $members = User::selectRaw('users.id, member_personals.photo, users.name, users.id as memberid, member_personals.cellphone as memberphone, (SELECT workout_date FROM workouts WHERE user_id = users.id AND workout_date <= \''.Carbon::today()->toDateString().' 23:59:59\' ORDER BY workout_date DESC LIMIT 1) as workout_date')
                ->where(function ($q) {
                    $q->where('member_status.status', '=', 'Active');
                })->where(function($r)use($user){
                    $r->where('users.club_id','=',$user->club_id);
                });

            $members = $members->leftJoin('workouts', function ($join) {
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
                ->get();

        $counter = 1;
        foreach ($members as $member):
            $wm_rank = WMRank::where('user_id','=',$member->id)->where('type','=','overall')->first();

            if(empty($wm_rank)){
                $member->setAttribute('lossamount',null);
                $member->setAttribute('diffdays', 999);
                $member->setAttribute('wo_count',0);
            }else{
                $member->setAttribute('lossamount',$wm_rank->wm_one == 0 ? null : $wm_rank->body_fat_diff);
                $member->setAttribute('diffdays',$wm_rank->wm_one == 0 ? 999 : $wm_rank->day_diff);
                $member->setAttribute('wo_count',$wm_rank->wo_count);
            }

            if (!empty($member->photo)):
                $member->setAttribute('photo', 'member_photos/'.$member->photo);
            else:
                $member->setAttribute('photo', 'img.png');
            endif;

            if (!empty($member->workout_date)):
                $diff_day = Carbon::today()->diffInDays(Carbon::parse($member->workout_date));
                $member->setAttribute('days', $diff_day);
            else:
                $member->setAttribute('days', 999);
            endif;

            if($member->lossamount == null){
                $member->lossamount = '99999999';
            } else {
                $member->setAttribute('lossamount', number_format($member->lossamount, 2));
            }

            if($member->diffdays == null){
                $member->diffdays = '-';
            }

        endforeach;

        $members = $members->sortBy('lossamount')->values();

        foreach ($members as $member) {
            $member->setAttribute('rank',$counter);
            $counter++;
        }

        return response()->json( $members, $this->successStatus);
    }

    public function club_today_event() {
        $user = Auth::user();

        $events = GlobalEvent::select('title', 'description', 'from_date', 'thru_date')
            ->whereDate('from_date', '<=', Carbon::now()->toDateString())
            ->whereDate('thru_date', '>=', Carbon::now()->toDateString())
            ->whereHas('globalevent_club', function ($q) use ($user) {
                $q->where('club_id', '=', $user->club_id);
            })
            ->get();

        return response()->json($events, $this->successStatus);
    }

    public function club_today_reconfirmation() {
        $user = Auth::user();

        $reconfirms = GuestAppointment::selectRaw('buddy_referral_leads.fullname, buddy_referral_leads.phone, 
            guest_appointments.fa_date, guest_appointments.fa_hour')
            ->join('buddy_referral_leads', 'buddy_referral_leads.id', '=', 'guest_appointments.guest_id')
            ->where('guest_appointments.staff_id', '=', $user->id)
            ->whereDate('guest_appointments.fa_date', '=', Carbon::tomorrow()->toDateString())
            ->where('guest_appointments.guest_status_after', '=', 'Lead')
            ->get();

        return response()->json($reconfirms, $this->successStatus);
    }

    public function club_today_appointment() {
        $user = Auth::user();

        $apos = GuestAppointment::selectRaw('buddy_referral_leads.fullname, buddy_referral_leads.phone, 
            guest_appointments.fa_date, guest_appointments.fa_hour')
            ->join('buddy_referral_leads', 'buddy_referral_leads.id', '=', 'guest_appointments.guest_id')
            ->where('guest_appointments.staff_id', '=', $user->id)
            ->whereDate('guest_appointments.fa_date', '=', Carbon::today()->toDateString())
            ->where('guest_appointments.guest_status_after', '=', 'Lead')
            ->get();

        return response()->json($apos, $this->successStatus);
    }

    public function buddy_referral_lead(Request $request){
        $user = Auth::user();

        if ($request->date != ''):
            $datas = BuddyReferralLead::where(function($q){
                $q->whereDoesntHave('buddyreferrallead_guestappointment')
                  ->orWhereHas('buddyreferrallead_guestappointment', function($r){
                    $r->where('guest_status_after','<>','Member')
                      ->whereHas('guestappointment_gastatus',function($s){
                        $s->where('status_id','>',3);
                      });
                  });
                })->join('member_sources',function($q){
                    $q->on('buddy_referral_leads.source_id','=','member_sources.id');
                })->leftJoin('referrers',function($q){
                    $q->on('buddy_referral_leads.id','=','referrers.referral_id');
                })->leftJoin('users',function($q){
                    $q->on('referrers.user_id','=','users.id');
                })->leftJoin('guest_monitorings',function($q){
                    $q->on('guest_monitorings.guest_id','=','buddy_referral_leads.id');
                })->where('status_gm','=','Guest')
                    ->where('no_more_call', '=', false)
                    ->whereDate('buddy_referral_leads.created_at', '=', $request->date)
                    ->selectRaw('buddy_referral_leads.id as this_id, fullname, phone,source_id, source, name, buddy_referral_leads.staff_id as coach, 
                    guest_monitorings.call_count as call, guest_monitorings.last_call as last_call, 
                    guest_monitorings.last_call_status as last_call_status')
//                    ->orderBy('guest_monitorings.call_count', 'ASC')
                    ->orderBy('buddy_referral_leads.id', 'DESC')
                    ->get();
        else:
            $datas = BuddyReferralLead::where(function($q){
                $q->whereDoesntHave('buddyreferrallead_guestappointment')
                  ->orWhereHas('buddyreferrallead_guestappointment', function($r){
                    $r->where('guest_status_after','<>','Member')
                      ->whereHas('guestappointment_gastatus',function($s){
                        $s->where('status_id','>',3);
                      });
                  });
                })->join('member_sources',function($q){
                    $q->on('buddy_referral_leads.source_id','=','member_sources.id');
                })->leftJoin('referrers',function($q){
                    $q->on('buddy_referral_leads.id','=','referrers.referral_id');
                })->leftJoin('users',function($q){
                    $q->on('referrers.user_id','=','users.id');
                })->leftJoin('guest_monitorings',function($q){
                    $q->on('guest_monitorings.guest_id','=','buddy_referral_leads.id');
                })->where('status_gm','=','Guest')
                    ->where('no_more_call', '=', false)
                    ->selectRaw('buddy_referral_leads.id as this_id, fullname, phone,source_id, source, name, buddy_referral_leads.staff_id as coach, 
                    guest_monitorings.call_count as call, guest_monitorings.last_call as last_call, 
                    guest_monitorings.last_call_status as last_call_status')
//                    ->orderBy('guest_monitorings.call_count', 'ASC')
                    ->orderBy('buddy_referral_leads.id', 'DESC')
                    ->get();

//            foreach($datas as $data):
//                $data->setAttribute('date', Carbon::parse($data->created_at)->format('Y-m-d'));
//            endforeach;
        endif;
        

        return response()->json($datas, $this->successStatus);
    }

    // public function buddy_referral_time_availability(Request $request){
    //     $user = Auth::user();

    //     $arrays = DB::select('SELECT (SELECT count(fa_date) FROM guest_appointments WHERE fa_date = \''.$request->sdate.'\' AND fa_hour >= \'10:00:00 +07:00\' AND fa_hour < \'11:00:00 +07:00\' AND club_id = '.$user->club_id.') as sepuluh,
    //           (SELECT count(fa_date) FROM guest_appointments WHERE fa_date = \''.$request->sdate.'\' AND fa_hour >= \'11:00:00 +07:00\' AND fa_hour < \'12:00:00 +07:00\' AND club_id = '.$user->club_id.') as sebelas,
    //           (SELECT count(fa_date) FROM guest_appointments WHERE fa_date = \''.$request->sdate.'\' AND fa_hour >= \'12:00:00 +07:00\' AND fa_hour < \'13:00:00 +07:00\' AND club_id = '.$user->club_id.') as duabelas,
    //           (SELECT count(fa_date) FROM guest_appointments WHERE fa_date = \''.$request->sdate.'\' AND fa_hour >= \'13:00:00 +07:00\' AND fa_hour < \'14:00:00 +07:00\' AND club_id = '.$user->club_id.') as satu,
    //           (SELECT count(fa_date) FROM guest_appointments WHERE fa_date = \''.$request->sdate.'\' AND fa_hour >= \'14:00:00 +07:00\' AND fa_hour < \'15:00:00 +07:00\' AND club_id = '.$user->club_id.') as dua,
    //           (SELECT count(fa_date) FROM guest_appointments WHERE fa_date = \''.$request->sdate.'\' AND fa_hour >= \'15:00:00 +07:00\' AND fa_hour < \'16:00:00 +07:00\' AND club_id = '.$user->club_id.') as tiga,
    //           (SELECT count(fa_date) FROM guest_appointments WHERE fa_date = \''.$request->sdate.'\' AND fa_hour >= \'16:00:00 +07:00\' AND fa_hour < \'17:00:00 +07:00\' AND club_id = '.$user->club_id.') as empat,
    //           (SELECT count(fa_date) FROM guest_appointments WHERE fa_date = \''.$request->sdate.'\' AND fa_hour >= \'17:00:00 +07:00\' AND fa_hour < \'18:00:00 +07:00\' AND club_id = '.$user->club_id.') as lima,
    //           (SELECT count(fa_date) FROM guest_appointments WHERE fa_date = \''.$request->sdate.'\' AND fa_hour >= \'18:00:00 +07:00\' AND fa_hour <= \'19:00:00 +07:00\' AND club_id = '.$user->club_id.') as enam');

    //     $datas = collect($arrays);

    //     $datas = $datas->values()->all();

    //     return response()->json($datas,$this->successStatus);
    // }

    public function buddy_referral_time_availability_v2(Request $request){
        $user = Auth::user();

        if(!empty($request->sdate)){

            // return $this->check_date_num($request->sdate);

            $clubOH = ClubOpenHour::where('club_id','=',$user->club_id)->get();
            $sdatenum = $this->check_date_num($request->sdate);
            $queryMade = '';
            // foreach ($clubOH as $open) {
            //     $fdatenum = $this->check_date_num($open->from_day);
            //     $tdatenum = $this->check_date_num($open->thru_day);
            //     if($sdatenum > $fdatenum && $fdatenum > $tdatenum){
            //         $sdatenum -= 7;
            //         $fdatenum -= 7;
            //     }else if($sdatenum < $fdatenum && $fdatenum > $tdatenum){
            //         $fdatenum -= 7;
            //     }

            //     if($fdatenum <= $sdatenum && $tdatenum >= $sdatenum){
            //         $openHour = $open->from_hour;
            //         $nextOpenHour = Carbon::parse(Carbon::parse($request->sdate)->format('Y-m-d').' '.$openHour)->addHour()->format('H:i:s +07:00');
            //         while($nextOpenHour < $open->thru_hour){
            //             $queryMade .= ' (SELECT count(fa_date) FROM guest_appointments WHERE fa_date = \''.$request->sdate.'\' AND fa_hour >= \''.$openHour.'\' AND fa_hour < \''.Carbon::parse(Carbon::parse($request->sdate)->format('Y-m-d').' '.$openHour)->addHour()->format('H:i:s +07:00').'\' AND club_id = '.$user->club_id.') as j'.Carbon::parse($openHour)->format('H').', ';
            //             $openHour = Carbon::parse(Carbon::parse($request->sdate)->format('Y-m-d').' '.$openHour)->addHour()->format('H:i:s +07:00');
            //             $nextOpenHour = Carbon::parse(Carbon::parse($request->sdate)->format('Y-m-d').' '.$openHour)->addHour()->format('H:i:s +07:00');
            //         }
            //     }
            // }

            // $queryMade = 'SELECT '.$queryMade;

            // $arrays = DB::select(substr($queryMade,0,-2));

            // $datas = collect($arrays);

            // $datas = $datas->values()->all();

            // if(!isset($datas[0]->j01)){
            //     $datas[0]->j01 = "99999999";
            // }
            // if(!isset($datas[0]->j02)){
            //     $datas[0]->j02 = "99999999";
            // }
            // if(!isset($datas[0]->j03)){
            //     $datas[0]->j03 = "99999999";
            // }
            // if(!isset($datas[0]->j04)){
            //     $datas[0]->j04 = "99999999";
            // }
            // if(!isset($datas[0]->j05)){
            //     $datas[0]->j05 = "99999999";
            // }
            // if(!isset($datas[0]->j06)){
            //     $datas[0]->j06 = "99999999";
            // }
            // if(!isset($datas[0]->j07)){
            //     $datas[0]->j07 = "99999999";
            // }
            // if(!isset($datas[0]->j08)){
            //     $datas[0]->j08 = "99999999";
            // }
            // if(!isset($datas[0]->j09)){
            //     $datas[0]->j09 = "99999999";
            // }
            // if(!isset($datas[0]->j10)){
            //     $datas[0]->j10 = "99999999";
            // }
            // if(!isset($datas[0]->j11)){
            //     $datas[0]->j11 = "99999999";
            // }
            // if(!isset($datas[0]->j12)){
            //     $datas[0]->j12 = "99999999";
            // }
            // if(!isset($datas[0]->j13)){
            //     $datas[0]->j13 = "99999999";
            // }
            // if(!isset($datas[0]->j14)){
            //     $datas[0]->j14 = "99999999";
            // }
            // if(!isset($datas[0]->j15)){
            //     $datas[0]->j15 = "99999999";
            // }
            // if(!isset($datas[0]->j16)){
            //     $datas[0]->j16 = "99999999";
            // }
            // if(!isset($datas[0]->j17)){
            //     $datas[0]->j17 = "99999999";
            // }
            // if(!isset($datas[0]->j18)){
            //     $datas[0]->j18 = "99999999";
            // }
            // if(!isset($datas[0]->j19)){
            //     $datas[0]->j19 = "99999999";
            // }
            // if(!isset($datas[0]->j20)){
            //     $datas[0]->j20 = "99999999";
            // }
            // if(!isset($datas[0]->j21)){
            //     $datas[0]->j21 = "99999999";
            // }
            // if(!isset($datas[0]->j22)){
            //     $datas[0]->j22 = "99999999";
            // }
            // if(!isset($datas[0]->j23)){
            //     $datas[0]->j23 = "99999999";
            // }
            // if(!isset($datas[0]->j00)){
            //     $datas[0]->j00 = "99999999";
            // }

            foreach ($clubOH as $open) {
                $fdatenum = $this->check_date_num($open->from_day);
                $tdatenum = $this->check_date_num($open->thru_day);
                if($sdatenum > $fdatenum && $fdatenum > $tdatenum){
                    $sdatenum -= 7;
                    $fdatenum -= 7;
                }else if($sdatenum < $fdatenum && $fdatenum > $tdatenum){
                    $fdatenum -= 7;
                }

                if($fdatenum <= $sdatenum && $tdatenum >= $sdatenum){
                    $openHour = $open->from_hour;
                    $nextOpenHour = Carbon::parse(Carbon::parse($request->sdate)->format('Y-m-d').' '.$openHour)->addMinute(30)->format('H:i:s +07:00');
                    while($nextOpenHour < $open->thru_hour){
                        $queryMade .= ' (SELECT count(fa_date) FROM guest_appointments WHERE fa_date = \''.$request->sdate.'\' AND fa_hour >= \''.$openHour.'\' AND fa_hour < \''.Carbon::parse(Carbon::parse($request->sdate)->format('Y-m-d').' '.$openHour)->addMinute(30)->format('H:i:s +07:00').'\' AND club_id = '.$user->club_id.') as j'.Carbon::parse($openHour)->format('Hi').', ';
                        $openHour = Carbon::parse(Carbon::parse($request->sdate)->format('Y-m-d').' '.$openHour)->addMinute(30)->format('H:i:s +07:00');
                        $nextOpenHour = Carbon::parse(Carbon::parse($request->sdate)->format('Y-m-d').' '.$openHour)->addMinute(30)->format('H:i:s +07:00');
                    }
                }
            }

            $queryMade = 'SELECT '.$queryMade;

            $arrays = DB::select(substr($queryMade,0,-2));

            $datas = collect($arrays);

            $datas = $datas->values()->all();

            if(!isset($datas[0]->j0100)){
                $datas[0]->j0100 = "99999999";
            }
            if(!isset($datas[0]->j0200)){
                $datas[0]->j0200 = "99999999";
            }
            if(!isset($datas[0]->j0300)){
                $datas[0]->j0300 = "99999999";
            }
            if(!isset($datas[0]->j0400)){
                $datas[0]->j0400 = "99999999";
            }
            if(!isset($datas[0]->j0500)){
                $datas[0]->j0500 = "99999999";
            }
            if(!isset($datas[0]->j0600)){
                $datas[0]->j0600 = "99999999";
            }
            if(!isset($datas[0]->j0700)){
                $datas[0]->j0700 = "99999999";
            }
            if(!isset($datas[0]->j0800)){
                $datas[0]->j0800 = "99999999";
            }
            if(!isset($datas[0]->j0900)){
                $datas[0]->j0900 = "99999999";
            }
            if(!isset($datas[0]->j1000)){
                $datas[0]->j1000 = "99999999";
            }
            if(!isset($datas[0]->j1100)){
                $datas[0]->j1100 = "99999999";
            }
            if(!isset($datas[0]->j1200)){
                $datas[0]->j1200 = "99999999";
            }
            if(!isset($datas[0]->j1300)){
                $datas[0]->j1300 = "99999999";
            }
            if(!isset($datas[0]->j1400)){
                $datas[0]->j1400 = "99999999";
            }
            if(!isset($datas[0]->j1500)){
                $datas[0]->j1500 = "99999999";
            }
            if(!isset($datas[0]->j1600)){
                $datas[0]->j1600 = "99999999";
            }
            if(!isset($datas[0]->j1700)){
                $datas[0]->j1700 = "99999999";
            }
            if(!isset($datas[0]->j1800)){
                $datas[0]->j1800 = "99999999";
            }
            if(!isset($datas[0]->j1900)){
                $datas[0]->j1900 = "99999999";
            }
            if(!isset($datas[0]->j2000)){
                $datas[0]->j2000 = "99999999";
            }
            if(!isset($datas[0]->j2100)){
                $datas[0]->j2100 = "99999999";
            }
            if(!isset($datas[0]->j2200)){
                $datas[0]->j2200 = "99999999";
            }
            if(!isset($datas[0]->j2300)){
                $datas[0]->j2300 = "99999999";
            }
            if(!isset($datas[0]->j0000)){
                $datas[0]->j0000 = "99999999";
            }

            if(!isset($datas[0]->j0130)){
                $datas[0]->j0130 = "99999999";
            }
            if(!isset($datas[0]->j0230)){
                $datas[0]->j0230 = "99999999";
            }
            if(!isset($datas[0]->j0330)){
                $datas[0]->j0330 = "99999999";
            }
            if(!isset($datas[0]->j0430)){
                $datas[0]->j0430 = "99999999";
            }
            if(!isset($datas[0]->j0530)){
                $datas[0]->j0530 = "99999999";
            }
            if(!isset($datas[0]->j0630)){
                $datas[0]->j0630 = "99999999";
            }
            if(!isset($datas[0]->j0730)){
                $datas[0]->j0730 = "99999999";
            }
            if(!isset($datas[0]->j0830)){
                $datas[0]->j0830 = "99999999";
            }
            if(!isset($datas[0]->j0930)){
                $datas[0]->j0930 = "99999999";
            }
            if(!isset($datas[0]->j1030)){
                $datas[0]->j1030 = "99999999";
            }
            if(!isset($datas[0]->j1130)){
                $datas[0]->j1130 = "99999999";
            }
            if(!isset($datas[0]->j1230)){
                $datas[0]->j1230 = "99999999";
            }
            if(!isset($datas[0]->j1330)){
                $datas[0]->j1330 = "99999999";
            }
            if(!isset($datas[0]->j1430)){
                $datas[0]->j1430 = "99999999";
            }
            if(!isset($datas[0]->j1530)){
                $datas[0]->j1530 = "99999999";
            }
            if(!isset($datas[0]->j1630)){
                $datas[0]->j1630 = "99999999";
            }
            if(!isset($datas[0]->j1730)){
                $datas[0]->j1730 = "99999999";
            }
            if(!isset($datas[0]->j1830)){
                $datas[0]->j1830 = "99999999";
            }
            if(!isset($datas[0]->j1930)){
                $datas[0]->j1930 = "99999999";
            }
            if(!isset($datas[0]->j2030)){
                $datas[0]->j2030 = "99999999";
            }
            if(!isset($datas[0]->j2130)){
                $datas[0]->j2130 = "99999999";
            }
            if(!isset($datas[0]->j2230)){
                $datas[0]->j2230 = "99999999";
            }
            if(!isset($datas[0]->j2330)){
                $datas[0]->j2330 = "99999999";
            }
            if(!isset($datas[0]->j0030)){
                $datas[0]->j0030 = "99999999";
            }
        }

        return response()->json($datas,$this->successStatus);
    }

    private function check_date_num($date){
        if($date == 'Monday' || $date == 'Tuesday' ||$date == 'Wednesday' ||$date == 'Thursday' ||$date == 'Friday' ||$date == 'Saturday' ||$date == 'Sunday'){
            switch($date){
                case 'Monday':
                    return 1;
                case 'Tuesday':
                    return 2;
                case 'Wednesday':
                    return 3;
                case 'Thursday':
                    return 4;
                case 'Friday':
                    return 5;
                case 'Saturday':
                    return 6;
                case 'Sunday':
                    return 7;
                default:
                    return 0;
            }
        }else{
            switch(Carbon::parse($date)->format('l')){
                case 'Monday':
                    return 1;
                case 'Tuesday':
                    return 2;
                case 'Wednesday':
                    return 3;
                case 'Thursday':
                    return 4;
                case 'Friday':
                    return 5;
                case 'Saturday':
                    return 6;
                case 'Sunday':
                    return 7;
                default:
                    return 0;
            }
        }
    }

    public function store_new_appo(){
        $user = Auth::user();

        $guest_appo = new GuestAppointment;
        $guest_appo->guest_id = (int)request('brl_id');
        $guest_appo->staff_id = $user->id;
        $guest_appo->fa_status = 'FA';
        $guest_appo->fa_date = Carbon::parse(request('fa_date'))->format('Y-m-d');
        $guest_appo->fa_hour = Carbon::parse(request('fa_date'))->format('H:i');
        $guest_appo->guest_status_after = 'Lead';
        $guest_appo->club_id = $user->club_id;
        $guest_appo->save();

        $guest_status = new GuestAppointmentStatus;
        $guest_status->status_id = 1;
        $guest_status->c_date = Carbon::today();
        $guest_appo->guestappointment_gastatus()->save($guest_status);

        if(!empty(request('notes'))){
            $guest_notes = new GuestAppointmentNote;
            $guest_notes->note = request('notes');
            $guest_appo->guestappointment_ganotes()->save($guest_notes);
        }

        app('App\Http\Controllers\PointStaffController')->staff_point_apo(Auth::id(), $guest_appo->fa_date);

        return response()->json(['success' => true], $this->successStatus);
    }

    public function set_appo_status(){
        $user = Auth::user();
        $appostatusid = (int)request('appo_id');
        $gastatus = GuestAppointmentStatus::findOrFail($appostatusid);
        if($gastatus){
            if(request('status') == '4'){
                $gastatus->status_id = 4;
                $gastatus->s_date = Carbon::now();
            }else if(request('status') == '5'){
                $gastatus->status_id = 5;
            }else if(request('status') == '6'){
                $gastatus->status_id = 6;
                $gastatus->s_date = Carbon::now();
                $gastatus->sa_date = Carbon::now();
            }

            $gastatus->save();

            if(request('status') == '6'):
                app('App\Http\Controllers\PointStaffController')->staff_point_apo_showup(Auth::id(), $gastatus->sa_date, 'showup');
            endif;
        }

        return response()->json(['success' => true], $this->successStatus);
    }

    public function workout_appointment($member_id) {
        $user = Auth::user();
        $from_date = new Carbon('first day of last month');
        $thru_date = new Carbon('last day of next month');

        $apos = MemberAppointment::selectRaw('member_appointments.app_date, member_appointment_status.status_id')
            ->where('staff_id', '=', $user->id)
            ->where('user_id', '=', $member_id)
            ->where('ap_type_id', '=', 2)
            ->whereDate('app_date', '>=', Carbon::parse($from_date)->toDateString())
            ->whereDate('app_date', '<=', Carbon::parse($thru_date)->toDateString())
            ->join('member_appointment_status', 'member_appointment_status.ma_id', '=', 'member_appointments.id')
            ->get();

        return response()->json($apos, $this->successStatus);
    }

    public function update_workout_appointment($member_id, Request $request) {
        $picked = json_decode($request->picked);
        $removed = json_decode($request->removed);

        if ((count($picked) > 0) && ($picked != null)):
            foreach ($picked as $pick):
                if ($pick != null):
                    $member_appointment = new MemberAppointment;
                    $member_appointment->user_id = $member_id;
                    $member_appointment->staff_id = Auth::id();
                    $member_appointment->ap_type_id = 2;
                    $member_appointment->app_date = Carbon::parse($pick);
                    $member_appointment->app_hour = '09:00';
                    $member_appointment->save();

                    $ma_status = new MemberAppointmentStatus;
                    $ma_status->status_id = 1;
                    $ma_status->c_date = Carbon::now();
                    $member_appointment->memberappointment_mastatus()->save($ma_status);
                endif;
            endforeach;
        endif;

        if ((count($removed) > 0) && ($removed != null)):
            foreach ($removed as $remove):
                if ($remove != null):
                    $member_appointments = MemberAppointment::whereDate('app_date', '=', Carbon::parse($remove)->toDateString())
                        ->first();

                    $member_appointment = MemberAppointment::findOrFail($member_appointments->id);
                    $member_appointment->delete();
                endif;
            endforeach;
        endif;

        return response()->json('done', $this->successStatus);
    }

    public function coach_rank(Request $request) {
        $from_date = new Carbon('first day of last month');
        $thru_date = new Carbon('last day of last month');

        $sales_rates = GuestAppointment::where('club_id', '=', Auth::user()->club_id)
            ->whereDate('fa_date', '>=', Carbon::parse($from_date)->toDateString())
            ->whereDate('fa_date', '<=', Carbon::parse($thru_date)->toDateString())
            ->groupBy('staff_id');

        $sales_rate_all = $sales_rates->selectRaw('COUNT(id) as all, staff_id')->orderBy('staff_id', 'asc')->get();
        $sales_rated = $sales_rates->groupBy('guest_status_after')->selectRaw('COUNT(id) as sale, guest_status_after, staff_id')->orderBy('staff_id', 'asc')->get();

        foreach ($sales_rate_all as $sale_all):
            $staff_id = $sale_all->staff_id;
            $member = 0;
            foreach ($sales_rated->where('staff_id', $staff_id) as $sale_rate):
                if ($sale_rate->guest_status_after == 'Member'):
                    $sales_rate = round($sale_rate->sale / $sale_all->all * 100);

                    $sales_cancelled = GuestAppointment::selectRaw('')
                        ->where('staff_id', '=', Auth::id())
                        ->where('guest_status_after', '=', 'Member')
                        ->whereDate('fa_date', '>=', Carbon::parse($from_date)->toDateString())
                        ->whereDate('fa_date', '<=', Carbon::parse($thru_date)->toDateString())
                        ->whereHas('guestappointment_buddyreferrallead', function ($q) {
                            $q->whereHas('buddyreferrallead_memberpersonal', function ($r) {
                                $r->whereHas('memberpersonal_user', function ($s) {
                                    $s->whereHas('user_refundsubmission', function ($t) {
                                        $t->where('refund_diff_days', '<=', 3);
                                    });
                                });
                            });
                        })
                        ->get()
                        ->count();

                    if (!empty($sales_cancelled)):
                        $sales_rate = round($sales_rate - ($sales_cancelled / $sale_all->all * 100));
                    endif;

                    $sale_all->setAttribute('sale', $sales_rate);
                    $member++;
                endif;
            endforeach;

            if ($member == 0):
                $sale_all->setAttribute('sale', 0);
            endif;
        endforeach;

        $sales_rate_all = $sales_rate_all->sortByDesc('sale')->values();

        return response()->json($sales_rate_all, $this->successStatus);
    }

    public function cl_category_list_index(Request $request){
        $clcategory = ClCategory::get();

        return response()->json($clcategory, $this->successStatus);
    }

    public function cl_category_list_main_index(Request $request){
        $clheader = ClMaterialHeader::where('category_id','=',$request->category_id)->orderBy('sequences','ASC')->get();

        return response()->json($clheader, $this->successStatus);
    }

    public function cl_material_content_index(Request $request){
        $clcontent = ClMaterialContent::where('material_id','=',$request->material_id)->orderBy('sequence','ASC')->get();

        return response()->json($clcontent, $this->successStatus);
    }

    public function cl_material_support_index(Request $request){
        $clsupport = ClMaterialSupport::where('material_content_id','=',$request->material_content_id)->get();

        $supportedVideo = array("mp4");
        $supportedImage = array("jpg","jpeg","png");

        foreach ($clsupport as $cls) {
            $tempArr = explode('.',$cls->file_name);
            $fileExt = end($tempArr);
            if(in_array($fileExt, $supportedImage)){
                $cls->setAttribute('file_type',1);
            }else if(in_array($fileExt, $supportedVideo)){
                $cls->setAttribute('file_type',2);
            }else{
                $cls->setAttribute('file_type',0);
            }
        }

        return response()->json($clsupport, $this->successStatus);
    }

    public function best_coach_club(Request $request){
        $user = Auth::user();

        $staffs = User::selectRaw('users.id, member_personals.photo, users.name, staff_points.current_total_point as currentpoint, club_infos.club_code as club_code')
            ->where('club_id','=',$user->club_id)
            ->where('role_id','<>',7)
            ->whereHas('user_memberstatus', function ($stat) {
                $stat->where('status', '=', 'Active');
            })
//            ->where('staff_points.current_total_point', '>', 0)
            ->where(function ($q) {
                $q->whereDoesntHave('user_staffinactive')
                    ->orWhere(function ($r) {
                        $r->whereHas('user_staffinactive', function ($s) {
                            $s->where('status', '=', 'Reactivated')
                                ->where('thru_date', null);
                        });
                    });
            });

            $staffs = $staffs->leftJoin('staff_points', function ($join) {
                $join->on('users.id', '=', 'staff_points.user_id');
            });

            $staffs = $staffs->join('member_personals', function($join){
                $join->on('users.id','=','member_personals.user_id');
            });

            $staffs = $staffs->join('club_infos', function($join){
                $join->on('users.club_id','=','club_infos.id');
            });

            $staffs = $staffs->orderBy('staff_points.current_total_point', 'desc')->get();

        $counter = 1;
        foreach ($staffs as $staff):
            if (!empty($staff->photo)):
                $staff->setAttribute('photo', 'member_photos/'.$staff->photo);
            else:
                $staff->setAttribute('photo', 'img.png');
            endif;

            if($staff->currentpoint == null){
                $staff->currentpoint = 0;
            } else {
//                $staff->setAttribute('currentpoint', number_format($staff->currentpoint, 2));
                $staff->setAttribute('currentpoint', $staff->currentpoint);
            }

        endforeach;

        $staffs = $staffs->sortByDesc('currentpoint')->values();
//        $staffs = $staffs->values();

        foreach ($staffs as $staff) {
            $staff->setAttribute('rank',$counter);
            $counter++;
        }

        return response()->json( $staffs, $this->successStatus);
    }

    public function best_coach_overall(Request $request){
        $user = Auth::user();

        $staffs = User::selectRaw('users.id, member_personals.photo, users.name, staff_points.current_total_point as currentpoint, club_infos.club_code as club_code')
            ->where('role_id','<>',7)
            ->whereHas('user_memberstatus', function ($stat) {
                $stat->where('status', '=', 'Active');
            })
//            ->where('staff_points.current_total_point', '>', 0)
            ->where(function ($q) {
                $q->whereDoesntHave('user_staffinactive')
                    ->orWhere(function ($r) {
                        $r->whereHas('user_staffinactive', function ($s) {
                            $s->where('status', '=', 'Reactivated')
                                ->where('thru_date', null);
                        });
                    });
            });

            $staffs = $staffs->leftJoin('staff_points', function ($join) {
                $join->on('users.id', '=', 'staff_points.user_id');
            });

            $staffs = $staffs->join('member_personals', function($join){
                $join->on('users.id','=','member_personals.user_id');
            });

            $staffs = $staffs->join('club_infos', function($join){
                $join->on('users.club_id','=','club_infos.id');
            });

            $staffs = $staffs->orderBy('staff_points.current_total_point', 'desc')->get();

        $counter = 1;
        foreach ($staffs as $staff):
            if (!empty($staff->photo)):
                $staff->setAttribute('photo', 'member_photos/'.$staff->photo);
            else:
                $staff->setAttribute('photo', 'img.png');
            endif;

            if($staff->currentpoint == null){
                $staff->currentpoint = 0;
            } else {
                $staff->setAttribute('currentpoint', $staff->currentpoint);
            }

        endforeach;

        $staffs = $staffs->sortByDesc('currentpoint')->values();

        foreach ($staffs as $staff) {
            $staff->setAttribute('rank',$counter);
            $counter++;
        }

        return response()->json( $staffs, $this->successStatus);
    }

    public function best_coach_club_v2(Request $request){
        $user = Auth::user();

        if ($request->category == 'daily'):
            $staffs = User::selectRaw('users.id, member_personals.photo, users.name, SUM(log_staff_point_acquisitions.amount) as currentpoint, club_infos.club_code as club_code');
        else:
            $staffs = User::selectRaw('users.id, member_personals.photo, users.name, SUM(staff_points_histories.total_point) as currentpoint, club_infos.club_code as club_code');
        endif;

        $staffs = $staffs->where('club_id','=',$user->club_id)
            ->where('role_id','<>',7)
            ->whereHas('user_memberstatus', function ($stat) {
                $stat->where('status', '=', 'Active');
            })
//            ->where('staff_points.current_total_point', '>', 0)
            ->where(function ($q) {
                $q->whereDoesntHave('user_staffinactive')
                    ->orWhere(function ($r) {
                        $r->whereHas('user_staffinactive', function ($s) {
                            $s->where('status', '=', 'Reactivated')
                                ->where('thru_date', null);
                        });
                    });
            });

        if ($request->category == 'daily'):
            $staffs = $staffs->leftJoin('log_staff_point_acquisitions', function ($join) use ($request) {
                $join->on('users.id', '=', 'log_staff_point_acquisitions.user_id');
                $join->whereDate('log_staff_point_acquisitions.acquire_date', '=', Carbon::parse($request->chosenDate)->toDateString());
            });
        else:
            $staffs = $staffs->leftJoin('staff_points_histories', function ($join) use ($request) {
                $join->on('users.id', '=', 'staff_points_histories.user_id');

                if ($request->category == 'weekly'):
                    $join->where('staff_points_histories.category', '=', 'Weekly');
                elseif ($request->category == 'monthly'):
                    $join->where('staff_points_histories.category', '=', 'Monthly');
                endif;
                $join->whereDate('staff_points_histories.from_date', '<=', Carbon::parse($request->chosenDate)->toDateString());
                $join->whereDate('staff_points_histories.thru_date', '>=', Carbon::parse($request->chosenDate)->toDateString());
            });
        endif;

        $staffs = $staffs->join('member_personals', function($join){
            $join->on('users.id','=','member_personals.user_id');
        });

        $staffs = $staffs->join('club_infos', function($join){
            $join->on('users.club_id','=','club_infos.id');
        });

        $staffs = $staffs->groupBy('users.id', 'member_personals.user_id', 'club_infos.id')->get();

        $counter = 1;
        foreach ($staffs as $staff):
            if (!empty($staff->photo)):
                $staff->setAttribute('photo', 'member_photos/'.$staff->photo);
            else:
                $staff->setAttribute('photo', 'img.png');
            endif;

            if($staff->currentpoint == null){
                $staff->currentpoint = 0;
            } else {
//                $staff->setAttribute('currentpoint', number_format($staff->currentpoint, 2));
                $staff->setAttribute('currentpoint', $staff->currentpoint);
            }

        endforeach;

        $staffs = $staffs->sortByDesc('currentpoint')->values();
//        $staffs = $staffs->values();

        foreach ($staffs as $staff) {
            $staff->setAttribute('rank',$counter);
            $counter++;
        }

        return response()->json( $staffs, $this->successStatus);
    }

    public function best_coach_overall_v2(Request $request){
        $user = Auth::user();

        if ($request->category == 'daily'):
            $staffs = User::selectRaw('users.id, member_personals.photo, users.name, SUM(log_staff_point_acquisitions.amount) as currentpoint, club_infos.club_code as club_code');
        else:
            $staffs = User::selectRaw('users.id, member_personals.photo, users.name, SUM(staff_points_histories.total_point) as currentpoint, club_infos.club_code as club_code');
        endif;

        $staffs = $staffs->where('role_id','<>',7)
            ->whereHas('user_memberstatus', function ($stat) {
                $stat->where('status', '=', 'Active');
            })
//            ->where('staff_points.current_total_point', '>', 0)
            ->where(function ($q) {
                $q->whereDoesntHave('user_staffinactive')
                    ->orWhere(function ($r) {
                        $r->whereHas('user_staffinactive', function ($s) {
                            $s->where('status', '=', 'Reactivated')
                                ->where('thru_date', null);
                        });
                    });
            });

        if ($request->category == 'daily'):
            $staffs = $staffs->leftJoin('log_staff_point_acquisitions', function ($join) use ($request) {
                $join->on('users.id', '=', 'log_staff_point_acquisitions.user_id');
                $join->whereDate('log_staff_point_acquisitions.acquire_date', '=', Carbon::parse($request->chosenDate)->toDateString());
            });
        else:
            $staffs = $staffs->leftJoin('staff_points_histories', function ($join) use ($request) {
                $join->on('users.id', '=', 'staff_points_histories.user_id');

                if ($request->category == 'weekly'):
                    $join->where('staff_points_histories.category', '=', 'Weekly');
                elseif ($request->category == 'monthly'):
                    $join->where('staff_points_histories.category', '=', 'Monthly');
                endif;
                $join->whereDate('staff_points_histories.from_date', '<=', Carbon::parse($request->chosenDate)->toDateString());
                $join->whereDate('staff_points_histories.thru_date', '>=', Carbon::parse($request->chosenDate)->toDateString());
            });
        endif;

        $staffs = $staffs->join('member_personals', function($join){
            $join->on('users.id','=','member_personals.user_id');
        });

        $staffs = $staffs->join('club_infos', function($join){
            $join->on('users.club_id','=','club_infos.id');
        });

        $staffs = $staffs->groupBy('users.id', 'member_personals.user_id', 'club_infos.id')->get();

        $counter = 1;
        foreach ($staffs as $staff):
            if (!empty($staff->photo)):
                $staff->setAttribute('photo', 'member_photos/'.$staff->photo);
            else:
                $staff->setAttribute('photo', 'img.png');
            endif;

            if($staff->currentpoint == null){
                $staff->currentpoint = 0;
            } else {
                $staff->setAttribute('currentpoint', $staff->currentpoint);
            }

        endforeach;

        $staffs = $staffs->sortByDesc('currentpoint')->values();

        foreach ($staffs as $staff) {
            $staff->setAttribute('rank',$counter);
            $counter++;
        }

        return response()->json( $staffs, $this->successStatus);
    }
	
	public function store_new_wm($id, Request $request) {
        $member = User::findOrFail($id);

        $wm = new WeighMeasure;
        $wm->user_id = $member->id;
        $wm->wm_date = $request->date;
        $wm->height = $request->height;
        $wm->bp_high = $request->bph;
        $wm->bp_low = $request->bpl;
        $wm->rhr = $request->rhr;
        $wm->chest = $request->chest;
        $wm->waist = $request->waist;
        $wm->abdomen = $request->abdomen;
        $wm->hip = $request->hip;
        $wm->thigh = $request->thigh;
        $wm->arm = $request->arm;
        $wm->weight = $request->weight;
        $wm->body_fat = $request->bodyfat;
        $wm->muscle_mass = $request->muscleweight;
        $wm->bone_mass = $request->bonemass;
        $wm->bmi = $request->bmi;
        $wm->dci = $request->dci;
        $wm->metabolic = $request->metabolic;
        $wm->body_water = $request->bodywater;
        $wm->visceral = $request->visceral;
        $wm->total_measurement = $request->chest + $request->waist + $request->abdomen + $request->hip + (2 * $request->thigh) + (2 * $request->arm);
        $wm->total_body_fat = $request->bodyfat;
        $wm->total_hydration = $request->bodywater;
        $wm->measured_by = Auth::id();

        //Check age
        if (!empty($member->user_memberpersonal->birth)):
            $wm->age = Carbon::today()->diffInYears(Carbon::parse($member->user_memberpersonal->birth));
        else:
            $wm->age = 0;
        endif;

        //Check 3x a week & Total WO per Month
        $total_wo_monthly = Workout::where('user_id', '=', $member->id)
            ->whereDate('workout_date', '<=', Carbon::now()->toDateString())
            ->whereDate('workout_date', '>=', Carbon::now()->subDays(30)->toDateString())
            ->get()
            ->count();

        if ($total_wo_monthly < 12):
            $wm->three_times_a_week = 'No';
        else:
            $wm->three_times_a_week = 'Yes';
        endif;

        $wm->total_wo_per_month = $total_wo_monthly;

        //Check Pro Workout
        if ($request->proworkout == 'true'):
            $wm->pro_workout = 'Yes';
        else:
            $wm->pro_workout = 'No';
        endif;

        if (!empty($request->notes) || ($request->notes != '')):
            $wm->recommendation = $request->notes;
        endif;

        $wm->save();

        // Add To WMRank Added 23/08/2019
        $wmrank_available_m = WMRank::where('user_id','=',$member->id)->where('type','=','monthly')->first();
        $wmrank_available_o = WMRank::where('user_id','=',$member->id)->where('type','=','overall')->first();

        // Check if there're another newer WM in the database
        $check_wm = WeighMeasure::where('user_id','=',$member->id)->where('wm_date','>',$request->date)->first();

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

        return response()->json($this->successStatus);
    }

    public function store_call_log_apo($id, Request $request) {
        $call_log = new LogContactedLead;
        $call_log->guest_appointment_id = $id;
        $call_log->staff_id = Auth::id();
        $call_log->status_id = 1;
        $call_log->save();

        return response()->json($this->successStatus);
    }

    public function store_call_log_new_lead($id, Request $request) {
        $call_log = new LogContactedNewLead;
        $call_log->guest_id = $id;
        $call_log->staff_id = Auth::id();
        $call_log->status = $request->call_status;
        $call_log->save();

        return response()->json($this->successStatus);
    }

    public function xfactor($id) {
        $member = User::findOrFail($id);

        $xfactors = Xfactor::where('user_id', '=', $member->id)->orderBy('id', 'asc')->get();

        return response()->json($xfactors, $this->successStatus);
    }

    public function update_xfactor($id, Request $request) {
        $counter = 0;
        $the_x = null;
        $member = User::findOrFail($id);
        $xfactors = Xfactor::where('user_id', '=', $member->id)->orderBy('id', 'asc')->get();

        if (count($xfactors) > 0):
            if (count($xfactors) == 2):
                foreach ($xfactors as $xfactor):
                    $gate = false;
                    if ($counter == 0):
                        if (!empty($request->xfactor1)):
                            $the_x = $request->xfactor1;
                            $gate = true;
                        endif;
                    elseif ($counter == 1):
                        if (!empty($request->xfactor2)):
                            $the_x = $request->xfactor2;
                            $gate = true;
                        endif;
                    endif;

                    if ($gate && ($the_x != null)):
                        $new_xfactor = Xfactor::findOrFail($xfactor->id);
                        $new_xfactor->xfactor = $the_x;
                        $new_xfactor->save();
                    endif;
                    $counter++;
                endforeach;
            elseif (count($xfactors) == 1):
                foreach ($xfactors as $xfactor):
                    if ($request->xfactor1 != null):
                        $upd_xfactor = Xfactor::findOrFail($xfactor->id);
                        $upd_xfactor->xfactor = $request->xfactor1;
                        $upd_xfactor->save();
                    endif;
                endforeach;

                if ($request->xfactor2 != null):
                    $new_xfactor = new Xfactor;
                    $new_xfactor->user_id = $member->id;
                    $new_xfactor->xfactor = $request->xfactor2;
                    $new_xfactor->save();
                endif;
            endif;
        else:
            for ($i = 0; $i < 2; $i++):
                if (!empty($request->input('xfactor'.($i + 1)))):
                    $new_xfactor = new Xfactor;
                    $new_xfactor->user_id = $member->id;
                    $new_xfactor->xfactor = $request->input('xfactor'.($i + 1));
                    $new_xfactor->save();
                endif;
            endfor;
        endif;

        return response()->json($this->successStatus);
    }

    public function get_member_payment($member_id){
        $user = Auth::user();

        $memberpayment = MemberPayment::where('user_id','=',$member_id)->get();

        foreach ($memberpayment as $mp) {
            $mp->setAttribute('tgl_bayar',Carbon::parse($mp->payment_date)->format('d-m-Y'));
            $mp->setAttribute('amount_paid_in_rp','Rp. '.number_format($mp->amount_paid));
            $mp->setAttribute('rest_of_bill_in_rp','Rp. '.number_format($mp->rest_of_bill));
        }

        return response()->json(['member_payment' => $memberpayment], $this->successStatus);
    }

    public function ble_get_member() {
        $members = User::whereHas('user_memberstatus', function ($q) {
            $q->where('status', '=', 'Active')
                ->orWhere('status', '=', 'Hold');
        })
            ->with('user_memberpersonal')
            ->orderBy('name', 'asc')
            ->get();

        return response()->json($members, $this->successStatus);
    }

    public function staff_point_acquisition($staff_id){
        $staff = User::findOrFail($staff_id);

        $fromdate = new Carbon('first day of this month');
        $thrudate = new Carbon('last day of this month');

        $fromdate = $fromdate->format('Y-m-d');
        $thrudate = $thrudate->format('Y-m-d');

        // $points = LogStaffPointAcquisition::selectRaw('SUM(amount) as totalamount, SUBSTRING(mst_points.name,1,4) AS abv, user_id, mst_points.type_detail as type_detail')->join('mst_points','log_staff_point_acquisitions.point_id','=','mst_points.id')->where('mst_points.type','=','Membership')->where('user_id','=',$staff_id)->groupBy('abv','user_id','mst_points.type_detail')->get();

        $points = LogStaffPointAcquisition::selectRaw('user_id, 
            (SELECT COALESCE(SUM(amount),0) FROM log_staff_point_acquisitions JOIN mst_points ON log_staff_point_acquisitions.point_id = mst_points.id WHERE SUBSTRING(mst_points.name,1,4) = \'ST6 \' AND mst_points.type_detail = \'Lunas\' AND user_id = '.$staff_id.' AND acquire_date >= \''.$fromdate.'\' AND acquire_date <= \''.$thrudate.'\') as stenamlunas,
            (SELECT COALESCE(SUM(amount),0) FROM log_staff_point_acquisitions JOIN mst_points ON log_staff_point_acquisitions.point_id = mst_points.id WHERE SUBSTRING(mst_points.name,1,4) = \'PP6 \' AND mst_points.type_detail = \'Lunas\' AND user_id = '.$staff_id.' AND acquire_date >= \''.$fromdate.'\' AND acquire_date <= \''.$thrudate.'\') as ppenamlunas,
            (SELECT COALESCE(SUM(amount),0) FROM log_staff_point_acquisitions JOIN mst_points ON log_staff_point_acquisitions.point_id = mst_points.id WHERE SUBSTRING(mst_points.name,1,4) = \'ST12\' AND mst_points.type_detail = \'Lunas\' AND user_id = '.$staff_id.' AND acquire_date >= \''.$fromdate.'\' AND acquire_date <= \''.$thrudate.'\') as stduablunas,
            (SELECT COALESCE(SUM(amount),0) FROM log_staff_point_acquisitions JOIN mst_points ON log_staff_point_acquisitions.point_id = mst_points.id WHERE SUBSTRING(mst_points.name,1,4) = \'PP12\' AND mst_points.type_detail = \'Lunas\' AND user_id = '.$staff_id.' AND acquire_date >= \''.$fromdate.'\' AND acquire_date <= \''.$thrudate.'\') as ppduablunas,
            (SELECT COALESCE(SUM(amount),0) FROM log_staff_point_acquisitions JOIN mst_points ON log_staff_point_acquisitions.point_id = mst_points.id WHERE SUBSTRING(mst_points.name,1,4) = \'PP18\' AND mst_points.type_detail = \'Lunas\' AND user_id = '.$staff_id.' AND acquire_date >= \''.$fromdate.'\' AND acquire_date <= \''.$thrudate.'\') as ppdelblunas,
            (SELECT COALESCE(SUM(amount),0) FROM log_staff_point_acquisitions JOIN mst_points ON log_staff_point_acquisitions.point_id = mst_points.id WHERE SUBSTRING(mst_points.name,1,4) = \'PP24\' AND mst_points.type_detail = \'Lunas\' AND user_id = '.$staff_id.' AND acquire_date >= \''.$fromdate.'\' AND acquire_date <= \''.$thrudate.'\') as ppduaelunas,
            (SELECT COALESCE(SUM(amount),0) FROM log_staff_point_acquisitions JOIN mst_points ON log_staff_point_acquisitions.point_id = mst_points.id WHERE SUBSTRING(mst_points.name,1,4) = \'FS L\' AND mst_points.type_detail = \'Lunas\' AND user_id = '.$staff_id.' AND acquire_date >= \''.$fromdate.'\' AND acquire_date <= \''.$thrudate.'\') as fslunas,
            (SELECT COALESCE(SUM(amount),0) FROM log_staff_point_acquisitions JOIN mst_points ON log_staff_point_acquisitions.point_id = mst_points.id WHERE SUBSTRING(mst_points.name,1,4) = \'ST6 \' AND mst_points.type_detail = \'DP+Cicilan1\' AND user_id = '.$staff_id.' AND acquire_date >= \''.$fromdate.'\' AND acquire_date <= \''.$thrudate.'\') as stenamdpccs,
            (SELECT COALESCE(SUM(amount),0) FROM log_staff_point_acquisitions JOIN mst_points ON log_staff_point_acquisitions.point_id = mst_points.id WHERE SUBSTRING(mst_points.name,1,4) = \'PP6 \' AND mst_points.type_detail = \'DP+Cicilan1\' AND user_id = '.$staff_id.' AND acquire_date >= \''.$fromdate.'\' AND acquire_date <= \''.$thrudate.'\') as ppenamdpccs,
            (SELECT COALESCE(SUM(amount),0) FROM log_staff_point_acquisitions JOIN mst_points ON log_staff_point_acquisitions.point_id = mst_points.id WHERE SUBSTRING(mst_points.name,1,4) = \'ST12\' AND mst_points.type_detail = \'DP+Cicilan1\' AND user_id = '.$staff_id.' AND acquire_date >= \''.$fromdate.'\' AND acquire_date <= \''.$thrudate.'\') as stduabdpccs,
            (SELECT COALESCE(SUM(amount),0) FROM log_staff_point_acquisitions JOIN mst_points ON log_staff_point_acquisitions.point_id = mst_points.id WHERE SUBSTRING(mst_points.name,1,4) = \'PP12\' AND mst_points.type_detail = \'DP+Cicilan1\' AND user_id = '.$staff_id.' AND acquire_date >= \''.$fromdate.'\' AND acquire_date <= \''.$thrudate.'\') as ppduabdpccs,
            (SELECT COALESCE(SUM(amount),0) FROM log_staff_point_acquisitions JOIN mst_points ON log_staff_point_acquisitions.point_id = mst_points.id WHERE SUBSTRING(mst_points.name,1,4) = \'PP18\' AND mst_points.type_detail = \'DP+Cicilan1\' AND user_id = '.$staff_id.' AND acquire_date >= \''.$fromdate.'\' AND acquire_date <= \''.$thrudate.'\') as ppdelbdpccs,
            (SELECT COALESCE(SUM(amount),0) FROM log_staff_point_acquisitions JOIN mst_points ON log_staff_point_acquisitions.point_id = mst_points.id WHERE SUBSTRING(mst_points.name,1,4) = \'PP24\' AND mst_points.type_detail = \'DP+Cicilan1\' AND user_id = '.$staff_id.' AND acquire_date >= \''.$fromdate.'\' AND acquire_date <= \''.$thrudate.'\') as ppduaedpccs,
            (SELECT COALESCE(SUM(amount),0) FROM log_staff_point_acquisitions JOIN mst_points ON log_staff_point_acquisitions.point_id = mst_points.id WHERE SUBSTRING(mst_points.name,1,4) = \'ST6 \' AND mst_points.type_detail = \'DP\' AND user_id = '.$staff_id.' AND acquire_date >= \''.$fromdate.'\' AND acquire_date <= \''.$thrudate.'\') as stenamdp,
            (SELECT COALESCE(SUM(amount),0) FROM log_staff_point_acquisitions JOIN mst_points ON log_staff_point_acquisitions.point_id = mst_points.id WHERE SUBSTRING(mst_points.name,1,4) = \'PP6 \' AND mst_points.type_detail = \'DP\' AND user_id = '.$staff_id.' AND acquire_date >= \''.$fromdate.'\' AND acquire_date <= \''.$thrudate.'\') as ppenamdp,
            (SELECT COALESCE(SUM(amount),0) FROM log_staff_point_acquisitions JOIN mst_points ON log_staff_point_acquisitions.point_id = mst_points.id WHERE SUBSTRING(mst_points.name,1,4) = \'ST12\' AND mst_points.type_detail = \'DP\' AND user_id = '.$staff_id.' AND acquire_date >= \''.$fromdate.'\' AND acquire_date <= \''.$thrudate.'\') as stduabdp,
            (SELECT COALESCE(SUM(amount),0) FROM log_staff_point_acquisitions JOIN mst_points ON log_staff_point_acquisitions.point_id = mst_points.id WHERE SUBSTRING(mst_points.name,1,4) = \'PP12\' AND mst_points.type_detail = \'DP\' AND user_id = '.$staff_id.' AND acquire_date >= \''.$fromdate.'\' AND acquire_date <= \''.$thrudate.'\') as ppduabdp,
            (SELECT COALESCE(SUM(amount),0) FROM log_staff_point_acquisitions JOIN mst_points ON log_staff_point_acquisitions.point_id = mst_points.id WHERE SUBSTRING(mst_points.name,1,4) = \'PP18\' AND mst_points.type_detail = \'DP\' AND user_id = '.$staff_id.' AND acquire_date >= \''.$fromdate.'\' AND acquire_date <= \''.$thrudate.'\') as ppdelbdp,
            (SELECT COALESCE(SUM(amount),0) FROM log_staff_point_acquisitions JOIN mst_points ON log_staff_point_acquisitions.point_id = mst_points.id WHERE SUBSTRING(mst_points.name,1,4) = \'PP24\' AND mst_points.type_detail = \'DP\' AND user_id = '.$staff_id.' AND acquire_date >= \''.$fromdate.'\' AND acquire_date <= \''.$thrudate.'\') as ppduaedp,
            (SELECT COALESCE(SUM(amount),0) FROM log_staff_point_acquisitions JOIN mst_points ON log_staff_point_acquisitions.point_id = mst_points.id WHERE SUBSTRING(mst_points.name,1,4) = \'ST6 \' AND mst_points.type_detail = \'<DP\' AND user_id = '.$staff_id.' AND acquire_date >= \''.$fromdate.'\' AND acquire_date <= \''.$thrudate.'\') as stenamkdp,
            (SELECT COALESCE(SUM(amount),0) FROM log_staff_point_acquisitions JOIN mst_points ON log_staff_point_acquisitions.point_id = mst_points.id WHERE SUBSTRING(mst_points.name,1,4) = \'PP6 \' AND mst_points.type_detail = \'<DP\' AND user_id = '.$staff_id.' AND acquire_date >= \''.$fromdate.'\' AND acquire_date <= \''.$thrudate.'\') as ppenamkdp,
            (SELECT COALESCE(SUM(amount),0) FROM log_staff_point_acquisitions JOIN mst_points ON log_staff_point_acquisitions.point_id = mst_points.id WHERE SUBSTRING(mst_points.name,1,4) = \'ST12\' AND mst_points.type_detail = \'<DP\' AND user_id = '.$staff_id.' AND acquire_date >= \''.$fromdate.'\' AND acquire_date <= \''.$thrudate.'\') as stduabkdp,
            (SELECT COALESCE(SUM(amount),0) FROM log_staff_point_acquisitions JOIN mst_points ON log_staff_point_acquisitions.point_id = mst_points.id WHERE SUBSTRING(mst_points.name,1,4) = \'PP12\' AND mst_points.type_detail = \'<DP\' AND user_id = '.$staff_id.' AND acquire_date >= \''.$fromdate.'\' AND acquire_date <= \''.$thrudate.'\') as ppduabkdp,
            (SELECT COALESCE(SUM(amount),0) FROM log_staff_point_acquisitions JOIN mst_points ON log_staff_point_acquisitions.point_id = mst_points.id WHERE SUBSTRING(mst_points.name,1,4) = \'PP18\' AND mst_points.type_detail = \'<DP\' AND user_id = '.$staff_id.' AND acquire_date >= \''.$fromdate.'\' AND acquire_date <= \''.$thrudate.'\') as ppdelbkdp,
            (SELECT COALESCE(SUM(amount),0) FROM log_staff_point_acquisitions JOIN mst_points ON log_staff_point_acquisitions.point_id = mst_points.id WHERE SUBSTRING(mst_points.name,1,4) = \'PP24\' AND mst_points.type_detail = \'<DP\' AND user_id = '.$staff_id.' AND acquire_date >= \''.$fromdate.'\' AND acquire_date <= \''.$thrudate.'\') as ppduaekdp,
            (SELECT COALESCE(SUM(amount),0) FROM log_staff_point_acquisitions JOIN mst_points ON log_staff_point_acquisitions.point_id = mst_points.id WHERE mst_points.type = \'Membership\' AND user_id = '.$staff_id.' AND acquire_date >= \''.$fromdate.'\' AND acquire_date <= \''.$thrudate.'\') as totalall
            ')->groupBy('user_id')->where('user_id','=',$staff_id)->first();

        return response()->json($points, $this->successStatus);
    }

    public function get_source_list() {
        $user = Auth::user();
        $sources = MemberSource::select('id', 'source')->orderBy('source', 'asc')->get();

        return response()->json($sources, $this->successStatus);
    }

    public function add_new_lead(Request $request) {
        $lead = new BuddyReferralLead;
        $lead->fullname = $request->name;
        $lead->phone = $request->phone;
        $lead->wa_number = $request->phone;
        $lead->source_id = $request->source;
        $lead->source_detail = $request->source_detail;
        $lead->location = $request->location ?? '';

        // Check Source
        $source = MemberSource::findOrFail($request->source);

        if ($source->source == 'BR'):
            $lead->is_br = 'BR';
        else:
            $lead->is_br = 'NotBR';
        endif;

        $lead->staff_id = Auth::id();
        $lead->club_id = Auth::user()->club_id;
        $lead->status_gm = 'Guest';

        if ($lead->save()) {
            // Added 28102019
            app('App\Http\Controllers\PointStaffController')->add_lead_point(Auth::id());
            return response()->json($this->successStatus);
        } else {
            return response()->json($this->failedStatus);
        }
    }

    public function guest_monitoring($id, Request $request) {
        $guest = BuddyReferralLead::findOrFail($id);

        // Guest Monitoring
        if (!empty($guest->lead_guestmonitoring)):
            $guest_monitoring = $guest->lead_guestmonitoring;
            $guest_monitoring->call_count += 1;

            if (($request->call_status == 'Success') || ($request->call_status == 'APO')):
                $guest_monitoring->success += 1;
                $guest_monitoring->last_call_status = 'Success';
            elseif ($request->call_status == 'Invalid'):
                $guest_monitoring->invalid += 1;
                $guest_monitoring->last_call_status = 'Invalid';
            elseif ($request->call_status == 'Hangout'):
                $guest_monitoring->hanging_out += 1;
                $guest_monitoring->last_call_status = 'Hanging';
            elseif ($request->call_status == 'Rejected'):
                $guest_monitoring->rejected = +1;
                $guest_monitoring->last_call_status = 'Rejected';
            endif;
        else:
            $guest_monitoring = new GuestMonitoring;
            $guest_monitoring->guest_id = $id;
            $guest_monitoring->call_count = 1;

            if (($request->call_status == 'Success') || ($request->call_status == 'APO')):
                $guest_monitoring->success = 1;
                $guest_monitoring->last_call_status = 'Success';
            elseif ($request->call_status == 'Invalid'):
                $guest_monitoring->invalid = 1;
                $guest_monitoring->last_call_status = 'Invalid';
            elseif ($request->call_status == 'Hangout'):
                $guest_monitoring->hanging_out = 1;
                $guest_monitoring->last_call_status = 'Hanging';
            elseif ($request->call_status == 'Rejected'):
                $guest_monitoring->rejected = 1;
                $guest_monitoring->last_call_status = 'Rejected';
            endif;
        endif;

        $guest_monitoring->last_call = Carbon::parse($request->open_call)->toDateTimeString();
        $guest->lead_guestmonitoring()->save($guest_monitoring);

        // If No More Call
        if ($request->call_status == 'NoMoreCall'):
            $guest->no_more_call = true;
            $guest->save();
        endif;

        // Detail
        $start_call = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::parse($request->open_call)->format('Y-m-d H:i:s'), 'Asia/Jakarta')->setTimezone('UTC');
        $end_call = Carbon::now();
        $duration = Carbon::parse($end_call)->diffInSeconds(Carbon::parse($start_call));

        $gm_detail = new GuestMonitoringDetail;
        $gm_detail->guest_monitoring_id = $guest_monitoring->guest_id;
        $gm_detail->staff_id = Auth::id();
        $gm_detail->start_call = $start_call;
        $gm_detail->end_call = $end_call;
        $gm_detail->duration = (int)$duration;
        $gm_detail->status = $guest_monitoring->last_call_status;
        $gm_detail->save();

        // Added 28102019
        app('App\Http\Controllers\PointStaffController')->call_lead_point(Auth::id(), $request->call_status);

        return response()->json($this->successStatus);
    }

    public function get_call_histories($id, Request $request){
        $guest = BuddyReferralLead::findOrFail($id);

        $gm_details = GuestMonitoringDetail::where('guest_monitoring_id','=',$guest->id)->orderBy('end_call','DESC')->get();

        if (!empty($gm_details)):
            foreach ($gm_details as $gm_detail) {
                $gm_detail->setAttribute('coach_name',$gm_detail->gmdetail_staff->name);
            }
        endif;

        return response()->json($gm_details, $this->successStatus);
    }

    public function update_guest_name_and_phone(Request $request){
        $guest = BuddyReferralLead::findOrFail($request->id);

        if(!empty($guest)){
            if(!empty($request->fullname)){
                $guest->fullname = $request->fullname;
            }
            if(!empty($request->phone)){
                $guest->phone = $request->phone;
            }
        }

        $guest->save();

        return response()->json($this->successStatus);
    }

    public function get_challenge_report($id){
        $user = Auth::user();
        $challenge_reports = StaffReport::where('staff_id', '=', $user->id)
            ->where('member_id', '=', $id)
            ->orderBy('contact_date', 'desc')
            ->get();

        foreach ($challenge_reports as $challenge_report):
            $date = Carbon::parse($challenge_report->contact_date)->format('d M Y');
            $challenge_report->setAttribute('date', $date);
        endforeach;

        return response()->json($challenge_reports, $this->successStatus);
    }

    public function store_or_update_challenge_report(Request $request) {
        $id = (int)$request->id;
        $memberId = (int)$request->memberId;
        $format_date = Carbon::parse($request->date)->format('Y-m-d h:i:s');

        if ($memberId == 0):
            $staff_report = StaffReport::find($id);
            $staff_report->report = $request->report;
            $staff_report->note = $request->note;
            $staff_report->contact_date = $format_date;
            $staff_report->save();
        elseif ($memberId != 0):
            $staff_report = new StaffReport;
            $staff_report->member_id = $memberId;
            $staff_report->staff_id = Auth::user()->id;
            $staff_report->report = $request->report;
            $staff_report->note = $request->note;
            $staff_report->contact_date = $format_date;
            $staff_report->save();
        endif;
        
        return response()->json($this->successStatus);
    }

    public function best_coach_performance($id, Request $request) {
        $user = User::findOrFail($id);

        if ($request->category == 'daily'):
            $categories = DB::select("SELECT SUM(x.total_point) as total, target_categories.id, target_categories.category_name FROM (
              SELECT SUM(amount) as total_point, point_id, mst_points.target_category_id as something_id 
              FROM log_staff_point_acquisitions 
              JOIN mst_points
              ON log_staff_point_acquisitions.point_id = mst_points.id
              WHERE log_staff_point_acquisitions.acquire_date = '".Carbon::parse($request->chosenDate)->toDateString()."'
              AND log_staff_point_acquisitions.user_id = '".$id."'
              GROUP BY mst_points.id, log_staff_point_acquisitions.point_id
            ) as x
            RIGHT JOIN target_categories
            ON something_id = target_categories.id 
            GROUP BY target_categories.id
            ORDER BY target_categories.id ASC");
        else:
            if ($request->category == 'weekly'):
                $category = 'Weekly';
            elseif ($request->category == 'monthly'):
                $category = 'Monthly';
            endif;

            $categories = DB::select("SELECT SUM(x.total_point) as total, target_categories.id, target_categories.category_name FROM (
              SELECT SUM(total_point) as total_point, point_id, mst_points.target_category_id as something_id 
              FROM staff_points_histories 
              JOIN mst_points
              ON staff_points_histories.point_id = mst_points.id
              WHERE staff_points_histories.from_date <= '".Carbon::parse($request->chosenDate)->toDateString()."'
              AND staff_points_histories.thru_date >= '".Carbon::parse($request->chosenDate)->toDateString()."'
              AND staff_points_histories.user_id = '".$id."'
              AND staff_points_histories.category = '".$category."'
              GROUP BY mst_points.id, staff_points_histories.point_id
            ) as x
            RIGHT JOIN target_categories
            ON something_id = target_categories.id 
            GROUP BY target_categories.id
            ORDER BY target_categories.id ASC");
        endif;

        return response()->json($categories, $this->successStatus);
    }

    public function best_coach_performance_detail($id, Request $request) {
        $user = User::findOrFail($id);

        if ($request->category == 'daily'):
            $points = Point::selectRaw('SUM(log_staff_point_acquisitions.amount) as total, mst_points.name')
                ->join('log_staff_point_acquisitions', function ($join) {
                    $join->on('log_staff_point_acquisitions.point_id','=','mst_points.id');
                })
                ->where('mst_points.target_category_id', '=', $request->detail)
                ->whereDate('log_staff_point_acquisitions.acquire_date', '=', Carbon::parse($request->chosenDate)->toDateString())
                ->where('log_staff_point_acquisitions.user_id', '=', $id)
                ->groupBy('mst_points.id')
                ->get();
        else:
            if ($request->category == 'weekly'):
                $category = 'Weekly';
            elseif ($request->category == 'monthly'):
                $category = 'Monthly';
            endif;

            $points = Point::selectRaw('SUM(staff_points_histories.total_point) as total, mst_points.name')
                ->join('staff_points_histories', function ($join) {
                    $join->on('staff_points_histories.point_id','=','mst_points.id');
                })
                ->where('mst_points.target_category_id', '=', $request->detail)
                ->whereDate('staff_points_histories.from_date', '<=', Carbon::parse($request->chosenDate)->toDateString())
                ->whereDate('staff_points_histories.thru_date', '>=', Carbon::parse($request->chosenDate)->toDateString())
                ->where('staff_points_histories.user_id', '=', $id)
                ->where('staff_points_histories.category', '=', $category)
                ->groupBy('mst_points.id')
                ->get();
        endif;

        return response()->json($points, $this->successStatus);
    }

    public function my_target($id){
        $coach_targets = CoachTarget::where('coach_targets.coach_id', '=', $id)
            ->whereRaw('coach_targets.acquired < coach_targets.target')
            ->join('target_category_details', function($joinTargetDetail) {
                $joinTargetDetail->on('coach_targets.target_category_detail_id', '=', 'target_category_details.id');
            })
            ->join('target_categories', function($joinTargetCategory) {
                $joinTargetCategory->on('target_category_details.target_category_id', '=', 'target_categories.id');
            })
            ->orderBy('target_categories.category_name', 'asc')
            ->get();

        foreach ($coach_targets as $coach_target):
            $coach_target->setAttribute('interval', $coach_target->coachtarget_targetcategorydetail->target_interval);
            $coach_target->setAttribute('category_name', $coach_target->coachtarget_targetcategorydetail->targetcategorydetail_targetcategory->category_name);
        endforeach;

        return response()->json($coach_targets, $this->successStatus);
    }

    public function staff_daily_report(Request $request) {
        $user = Auth::user();

        $today = Carbon::today();
        if (!empty($request->date)):
            $today = $request->date;
        endif;

        // Daily Call & Leads
        $daily_reports = MemberSource::selectRaw('member_sources.id as source_id, 
            member_sources.source,
            COUNT(guest_monitoring_details.id) as subtotal, 
            guest_monitoring_details.status, 
            COUNT(buddy_referral_leads.id) as leads, 
            COUNT(guest_appointments.guest_id) as apo')
            ->leftJoin('buddy_referral_leads', function ($brl) use ($user, $today) {
                $brl->on('member_sources.id', '=', 'buddy_referral_leads.source_id');
                $brl->where('buddy_referral_leads.staff_id', '=', $user->id);
//                $brl->whereDate('buddy_referral_leads.created_at', '=', Carbon::parse($today)->toDateString());
            })
            ->leftJoin('guest_monitoring_details', function ($gmd) use ($user, $today) {
                $gmd->on('buddy_referral_leads.id', '=', 'guest_monitoring_details.guest_monitoring_id');
                $gmd->where('guest_monitoring_details.staff_id', '=', $user->id);
//                $gmd->whereDate('guest_monitoring_details.start_call', '=', Carbon::parse($today)->toDateString());
            })
            ->leftJoin('guest_appointments', function ($ga) use ($today) {
                $ga->on('guest_appointments.guest_id', '=', 'buddy_referral_leads.id');
//                $ga->whereDate('created_at', '=', Carbon::parse($today)->toDateString());
            })
            ->groupBy('guest_monitoring_details.status', 'member_sources.id')
            ->orderBy('member_sources.id', 'asc')
            ->get();

        // Total FA (Show Up)
        $total_fa = GuestAppointment::where('staff_id', '=', $user->id)
            ->whereHas('guestappointment_gastatus', function ($ga_status) use ($today) {
                $ga_status->where('s_date', '<>', null)
                    ->whereDate('s_date', '=', Carbon::parse($today)->toDateString());
            })
            ->count();

        // Sales => Mencakup Semua Membership + Promo Aktif
        // Membership yang diikutsertakan:
        // Regular + Student, Cash + Prepaid, general
        $memberships = MembershipType::selectRaw('membership_types.membership_type,
            membership_types.duration::INTEGER,
            membership_types.payment_type')
            ->where('membership_types.membership_type', '<>', 'Staff')
            ->where(function ($payment) {
                $payment->where('membership_types.payment_type', '=', 'Cash')
                    ->orWhere('membership_types.payment_type', '=', 'Prepaid');
            })
            ->where('is_vip', '=', 'general')
            ->groupBy('membership_types.membership_type', 'membership_types.duration', 'membership_types.payment_type')
            ->orderBy('membership_type', 'asc')
            ->orderBy('duration', 'asc')
            ->get();

        $sales = MembershipType::selectRaw('membership_types.membership_type,
            membership_types.duration::INTEGER,
            membership_types.payment_type,
            COUNT(member_payments.id) as total_member')
            ->where('membership_types.membership_type', '<>', 'Staff')
            ->where(function ($payment) {
                $payment->where('membership_types.payment_type', '=', 'Cash')
                    ->orWhere('membership_types.payment_type', '=', 'Prepaid');
            })
            ->where('is_vip', '=', 'general')
            ->leftJoin('log_member_memberships', function ($member) use ($today, $user) {
                $member->on('log_member_memberships.membership_type_id', '=', 'membership_types.id');
            })
            ->leftJoin('member_payments', function ($member_payment) use ($today, $user) {
                $member_payment->on('member_payments.user_id', '=', 'log_member_memberships.user_id');
                $member_payment->whereDate('member_payments.payment_date', '=', Carbon::parse($today)->toDateString());
                $member_payment->where('member_payments.payment_category', '=', 'First');
            })
            ->join('member_personals', function ($personal) use ($user) {
                $personal->on('member_personals.user_id', '=', 'member_payments.user_id');
                $personal->where('member_personals.sales_person_id', '=', $user->id);
            })
            ->groupBy('membership_types.membership_type', 'membership_types.duration', 'membership_types.payment_type')
            ->orderBy('membership_type', 'asc')
            ->orderBy('duration', 'asc')
            ->get();

        $total_sales = 0;
        foreach ($memberships as $membership):
            $gate = true;
            foreach ($sales as $sale):
                if ($gate):
                    if (
                        ($membership->membership_type == $sale->membership_type) &&
                        ($membership->duration == $sale->duration) &&
                        ($membership->payment_type == $sale->payment_type)
                    ):
                        $membership->setAttribute('total_member', $sale->total_member);
                        $total_sales += $sale->total_member;
                        $gate = false;
                    endif;
                endif;
            endforeach;

            if ($gate):
                $membership->setAttribute('total_member', 0);
            endif;
        endforeach;

        // Fitness Study => Membership Promo
        $promos = GuestMemberPayment::select('id')
//            ->whereDate('payment_date', '=', Carbon::parse($today)->toDateString())
            ->whereHas('gmpayment_gmmember', function($gmm) use ($user) {
                $gmm->whereHas('gmmember_guest', function ($guest) use ($user) {
                    $guest->where('staff_id', '=', $user->id);
                });
            })
            ->count();
        $total_sales += $promos;
        $total_fa += $promos;

        $the_promo = ["membership_type" => "FS", "duration" => 1, "payment_type" => "FS", "total_member" => $promos];
        $memberships->push($the_promo);

        // Total FWO
        $total_fwo = User::where('role_id', '=', '7')
            ->whereHas('user_logmemberstatus', function ($q) use ($today) {
                $q->whereDate('from_date', '<=', Carbon::parse($today)->toDateString());
            })
            ->whereHas('user_workout', function ($r) use ($today) {
                $r->whereDate('workout_date', '=', Carbon::parse($today)->toDateString())
                    ->whereNotIn('user_id', DB::table('workouts')
                        ->WhereDate('workout_date', '<', Carbon::parse($today))
                        ->pluck('user_id'));
            })
            ->whereHas('user_memberpersonal', function ($personal) {
                $personal->where('sales_person_id', '=', Auth::id());
            })
            ->count();

        // Penjualan Gloves
        $gloves = ProductPaymentDetail::whereHas('productpaymentdetail_productpayment', function ($payment) use ($user, $today) {
                $payment->where('cashier_id', '=', $user->id)
                    ->whereDate('payment_date', '=', Carbon::parse($today)->toDateString());
            })
            ->whereHas('productpaymentdetail_product', function ($product) {
                $product->where('item_name', 'ilike', '%glove%');
            })
            ->sum('qty');

        // Penjualan Loker
        $lockers = LockerPayment::where('staff_id', '=', $user->id)
            ->whereDate('payment_date', '=', Carbon::parse($today)->toDateString())
            ->count();

        // Total Leads
        $total_leads = $daily_reports->sum('leads');

        return response()->json([
            'call_reports' => $daily_reports,
            'sales' => $memberships,
            'fs' => $promos,
            'total_fa' => $total_fa,
            'total_sales' => $total_sales,
            'total_fwo' => $total_fwo,
            'total_leads' => $total_leads,
            'gloves' => $gloves,
            'lockers' => $lockers
        ], $this->successStatus);
    }

    public function best_coach_performance_v2($id, Request $request) {
        $categories = [];
        $date = Carbon::today();
        $staff = User::findOrFail($id);

        if (!empty($request->chosenDate)):
            $date = Carbon::parse($request->chosenDate);
        endif;

//        return response()->json(['total' => $total_sales, 'coach' => $coach_sales], $this->successStatus);

        $total_sales = 0;
        $total_fa = 0;

        if ($request->category == 'daily'):
            // HITUNG TOTAL SALES COACH
            // Penjualan Membership
            $club_sales = MemberPersonal::select('user_id', 'sales_person_id')
                ->whereHas('memberpersonal_user', function ($user) use ($date, $staff) {
                    $user->where('club_id', '=', $staff->club_id);
                    $user->whereHas('user_memberpayment', function ($payment) use ($date) {
                        $payment->where('payment_category', '=', 'First')
                            ->whereDate('payment_date', '=', Carbon::parse($date)->toDateString());
                    });
                })
                ->get();

            // Penjualan Promo
            $club_promos = BuddyReferralLead::select('id', 'staff_id')
                ->where('club_id', '=', $staff->club_id)
                ->whereHas('guest_gmmember', function ($gmmember) use ($date) {
                    $gmmember->whereHas('gmmember_gmpayment', function ($payment) use ($date) {
                        $payment->whereDate('payment_date', '=', Carbon::parse($date)->toDateString());
                    });
                })
                ->get();

            $total_sales += count($club_sales) + count($club_promos);
            $coach_sales = count($club_sales->where('sales_person_id', $staff->id)) + count($club_promos->where('staff_id', '=', $staff->id));
            array_push($categories, ['id' => 1, 'category_name' => 'Sales', 'total' => $total_sales, 'coach' => $coach_sales]);

            // HITUNG TOTAL FA (Show Up)
            $club_fa = GuestAppointment::select('guest_id', 'staff_id')
                ->where('club_id', '=', $staff->club_id)
                ->whereHas('guestappointment_gastatus', function ($ga_status) use ($date) {
                    $ga_status->where('s_date', '<>', null)
                        ->whereDate('s_date', '=', Carbon::parse($date)->toDateString());
                })
                ->get();

            $total_fa += count($club_fa);
            $coach_fa = count($club_fa->where('staff_id', $staff->id));
            array_push($categories, ['id' => 2, 'category_name' => 'FA', 'total' => $total_fa, 'coach' => $coach_fa]);

            // HITUNG TOTAL FWO
            $club_fwo = MemberPersonal::select('user_id', 'sales_person_id')
                ->whereHas('memberpersonal_user', function ($user) use ($date, $staff) {
                    $user->where('role_id', '=', '7')
                        ->where('club_id', '=', $staff->club_id);
                    $user->whereHas('user_logmemberstatus', function ($q) use ($date) {
                        $q->whereDate('from_date', '<=', Carbon::parse($date)->toDateString());
                    });
                    $user->whereHas('user_workout', function ($r) use ($date) {
                        $r->whereDate('workout_date', '=', Carbon::parse($date)->toDateString());
//                            ->whereNotIn('user_id', DB::table('workouts')
//                                ->whereDate('workout_date', '<', Carbon::parse($date)->toDateString())
//                                ->groupBy('user_id')
//                                ->pluck('user_id'));
                    });
                })
                ->get();

            $total_fwo = count($club_fwo);
            $coach_fwo = count($club_fwo->where('sales_person_id', $staff->id));
            array_push($categories, ['id' => 3, 'category_name' => 'FWO', 'total' => $total_fwo, 'coach' => $coach_fwo]);
//            array_push($categories, ['id' => 3, 'category_name' => 'FWO', 'total' => 0, 'coach' => 0]);

            // HITUNG PENJUALAN GLOVES
            $club_gloves = ProductPaymentDetail::whereHas('productpaymentdetail_productpayment', function ($payment) use ($date, $staff) {
                $payment->where('club_id', '=', $staff->club_id)
                    ->whereDate('payment_date', '=', Carbon::parse($date)->toDateString());
            })
                ->whereHas('productpaymentdetail_product', function ($product) {
                    $product->where('item_name', 'ilike', '%glove%');
                })
                ->sum('qty');

            $coach_gloves = ProductPaymentDetail::whereHas('productpaymentdetail_productpayment', function ($payment) use ($staff, $date) {
                $payment->where('cashier_id', '=', $staff->id)
                    ->where('club_id', '=', $staff->club_id)
                    ->whereDate('payment_date', '=', Carbon::parse($date)->toDateString());
            })
                ->whereHas('productpaymentdetail_product', function ($product) {
                    $product->where('item_name', 'ilike', '%glove%');
                })
                ->sum('qty');

            array_push($categories, ['id' => 4, 'category_name' => 'Gloves', 'total' => $club_gloves, 'coach' => $coach_gloves]);

            // HITUNG PENJUALAN LOKER
            $club_lockers = LockerPayment::select('id', 'staff_id')
                ->whereDate('payment_date', '=', Carbon::parse($date)->toDateString())
                ->whereHas('lockerpayment_locker', function ($locker) use ($staff) {
                    $locker->where('club_id', '=', $staff->club_id);
                })
                ->get();

            $total_lockers = count($club_lockers);
            $coach_lockers = count($club_lockers->where('staff_id', $staff->id));
            array_push($categories, ['id' => 5, 'category_name' => 'Locker', 'total' => $total_lockers, 'coach' => $coach_lockers]);

            // HITUNG TOTAL LEADS
            $club_leads = BuddyReferralLead::select('id', 'staff_id')
                ->where('club_id', '=', $staff->club_id)
                ->whereDate('created_at', '=', Carbon::parse($date)->toDateString())
                ->get();

            $total_leads = count($club_leads);
            $coach_leads = count($club_leads->where('staff_id', '=', $staff->id));
            array_push($categories, ['id' => 6, 'category_name' => 'Lead', 'total' => $total_leads, 'coach' => $coach_leads]);
        else:
            if ($request->category == 'weekly'):
                $from_date = $date;
                $thru_date = Carbon::parse($date)->addWeek();
            elseif ($request->category == 'monthly'):
                $from_date = Carbon::parse($date)->firstOfMonth();
                $thru_date = Carbon::parse($date)->lastOfMonth();
            endif;

            // HITUNG TOTAL SALES COACH
            // Penjualan Membership
            $club_sales = MemberPersonal::select('user_id', 'sales_person_id')
                ->whereHas('memberpersonal_user', function ($user) use ($from_date, $thru_date, $staff) {
                    $user->where('club_id', '=', $staff->club_id);
                    $user->whereHas('user_memberpayment', function ($payment) use ($from_date, $thru_date) {
                        $payment->where('payment_category', '=', 'First')
                            ->whereDate('payment_date', '>=', Carbon::parse($from_date)->toDateString())
                            ->whereDate('payment_date', '<', Carbon::parse($thru_date)->toDateString());
                    });
                })
                ->get();

            // Penjualan Promo
            $club_promos = BuddyReferralLead::select('id', 'staff_id')
                ->where('club_id', '=', $staff->club_id)
                ->whereHas('guest_gmmember', function ($gmmember) use ($from_date, $thru_date) {
                    $gmmember->whereHas('gmmember_gmpayment', function ($payment) use ($from_date, $thru_date) {
                        $payment->whereDate('payment_date', '>=', Carbon::parse($from_date)->toDateString())
                            ->whereDate('payment_date', '<', Carbon::parse($thru_date)->toDateString());
                    });
                })
                ->get();

            $total_sales += count($club_sales) + count($club_promos);
            $coach_sales = count($club_sales->where('sales_person_id', $staff->id)) + count($club_promos->where('staff_id', '=', $staff->id));
            array_push($categories, ['id' => 1, 'category_name' => 'Sales', 'total' => $total_sales, 'coach' => $coach_sales]);

            // HITUNG TOTAL FA (Show Up)
            $club_fa = GuestAppointment::select('guest_id', 'staff_id')
                ->where('club_id', '=', $staff->club_id)
                ->whereHas('guestappointment_gastatus', function ($ga_status) use ($from_date, $thru_date) {
                    $ga_status->where('s_date', '<>', null)
                        ->whereDate('s_date', '>=', Carbon::parse($from_date)->toDateString())
                        ->whereDate('s_date', '<', Carbon::parse($thru_date)->toDateString());
                })
                ->get();

            $total_fa += count($club_fa);
            $coach_fa = count($club_fa->where('staff_id', $staff->id));
            array_push($categories, ['id' => 2, 'category_name' => 'FA', 'total' => $total_fa, 'coach' => $coach_fa]);

            // HITUNG TOTAL FWO
            $club_fwo = MemberPersonal::select('user_id', 'sales_person_id')
                ->whereHas('memberpersonal_user', function ($user) use ($from_date, $thru_date, $staff) {
                    $user->where('role_id', '=', '7')
                        ->where('club_id', '=', $staff->club_id);
                    $user->whereHas('user_logmemberstatus', function ($q) use ($from_date, $thru_date) {
                        $q->whereDate('from_date', '<=', Carbon::parse($from_date)->toDateString())
                            ->whereDate('from_date', '<=', Carbon::parse($thru_date)->toDateString());
                    });
                    $user->whereHas('user_workout', function ($r) use ($from_date, $thru_date) {
                        $r->whereDate('workout_date', '>=', Carbon::parse($from_date)->toDateString())
                            ->whereDate('workout_date', '<', Carbon::parse($thru_date)->toDateString());
//                            ->whereNotIn('user_id', DB::table('workouts')
//                            ->WhereDate('workout_date', '<', Carbon::parse($from_date))
//                            ->WhereDate('workout_date', '<', Carbon::parse($thru_date))
//                            ->pluck('user_id'));
                    });
                })
                ->get();

            $total_fwo = count($club_fwo);
            $coach_fwo = count($club_fwo->where('sales_person_id', $staff->id));
            array_push($categories, ['id' => 3, 'category_name' => 'FWO', 'total' => $total_fwo, 'coach' => $coach_fwo]);

            // HITUNG PENJUALAN GLOVES
            $club_gloves = ProductPaymentDetail::whereHas('productpaymentdetail_productpayment', function ($payment) use ($from_date, $thru_date, $staff) {
                $payment->where('club_id', '=', $staff->club_id)
                    ->whereDate('payment_date', '>=', Carbon::parse($from_date)->toDateString())
                    ->whereDate('payment_date', '<', Carbon::parse($thru_date)->toDateString());
            })
                ->whereHas('productpaymentdetail_product', function ($product) {
                    $product->where('item_name', 'ilike', '%glove%');
                })
                ->sum('qty');

            $coach_gloves = ProductPaymentDetail::whereHas('productpaymentdetail_productpayment', function ($payment) use ($staff, $from_date, $thru_date) {
                $payment->where('cashier_id', '=', $staff->id)
                    ->where('club_id', '=', $staff->club_id)
                    ->whereDate('payment_date', '>=', Carbon::parse($from_date)->toDateString())
                    ->whereDate('payment_date', '<', Carbon::parse($thru_date)->toDateString());
            })
                ->whereHas('productpaymentdetail_product', function ($product) {
                    $product->where('item_name', 'ilike', '%glove%');
                })
                ->sum('qty');

            array_push($categories, ['id' => 4, 'category_name' => 'Gloves', 'total' => $club_gloves, 'coach' => $coach_gloves]);

            // HITUNG PENJUALAN LOKER
            $club_lockers = LockerPayment::select('id', 'staff_id')
                ->whereDate('payment_date', '>=', Carbon::parse($from_date)->toDateString())
                ->whereDate('payment_date', '<', Carbon::parse($thru_date)->toDateString())
                ->whereHas('lockerpayment_locker', function ($locker) use ($staff) {
                    $locker->where('club_id', '=', $staff->club_id);
                })
                ->get();

            $total_lockers = count($club_lockers);
            $coach_lockers = count($club_lockers->where('staff_id', $staff->id));
            array_push($categories, ['id' => 5, 'category_name' => 'Locker', 'total' => $total_lockers, 'coach' => $coach_lockers]);

            // HITUNG TOTAL LEADS
            $club_leads = BuddyReferralLead::select('id', 'staff_id')
                ->where('club_id', '=', $staff->club_id)
                ->whereDate('created_at', '>=', Carbon::parse($from_date)->toDateString())
                ->whereDate('created_at', '<', Carbon::parse($thru_date)->toDateString())
                ->get();

            $total_leads = count($club_leads);
            $coach_leads = count($club_leads->where('staff_id', '=', $staff->id));
            array_push($categories, ['id' => 6, 'category_name' => 'Lead', 'total' => $total_leads, 'coach' => $coach_leads]);
        endif;

        return response()->json($categories, $this->successStatus);
    }

    public function best_coach_performance_detail_v2($id, Request $request) {
        $staff = User::findOrFail($id);
        $categories = [];

        $date = Carbon::today();
        if (!empty($request->chosenDate)):
            $date = Carbon::parse($request->chosenDate);
        endif;

        // Pilah request
        // 1 = Sales
        // 2 = FA
        // 3 = FWO
        // 4 = Gloves
        // 5 = Locker
        // 6 = Leads
        if ($request->category == 'daily'):
            if ($request->detail == 1):
                // Penjualan Membership
                $coach_sales = User::selectRaw('COUNT(users.id) as total, 
                membership_types.membership_type, 
                membership_types.duration, 
                membership_types.payment_type')
                    ->whereHas('user_memberpersonal', function ($personal) use ($staff) {
                        $personal->where('sales_person_id', '=', $staff->id);
                    })
                    ->whereHas('user_memberpayment', function ($payment) use ($date) {
                        $payment->where('payment_category', '=', 'First')
                            ->whereDate('payment_date', '=', Carbon::parse($date)->toDateString());
                    })
                    ->join('member_status', function ($status) {
                        $status->on('users.id', '=', 'member_status.user_id');
                    })
                    ->join('membership_types', function ($membership) {
                        $membership->on('member_status.membership_type_id', '=', 'membership_types.id');
                    })
                    ->groupBy('membership_types.membership_type', 'membership_types.duration', 'membership_types.payment_type')
                    ->get();

                foreach ($coach_sales as $sale):
                    $type = '';
                    if ($sale->membership_type == 'Regular'):
                        if ($sale->payment_type == 'Cash'):
                            $type = 'MM';
                        elseif ($sale->payment_type == 'Prepaid'):
                            $type = 'PP';
                        endif;
                    elseif ($sale->membership_type == 'Student'):
                        $type = 'ST';
                    elseif ($sale->membership_type == 'Corporate'):
                        $type = 'CP';
                    endif;

                    if ($type != 'MM'):
                        $type .= $sale->duration;
                    endif;

                    array_push($categories, ['total' => $sale->total, 'name' => $type]);
                endforeach;

                // Penjualan Promo
                $coach_promos = BuddyReferralLead::selectRaw('COUNT(buddy_referral_leads.id) as total')
                    ->where('staff_id', '=', $staff->id)
                    ->whereHas('guest_gmmember', function ($gmmember) use ($date) {
                        $gmmember->whereHas('gmmember_gmpayment', function ($payment) use ($date) {
                            $payment->whereDate('payment_date', '=', Carbon::parse($date)->toDateString());
                        });
                    })
                    ->get();

                foreach ($coach_promos as $promo):
                    if ($promo->total > 0):
                        $promo->setAttribute('name', 'FS');
                        array_push($categories, $promo);
                    endif;
                endforeach;
            elseif ($request->detail == 2):
                $coach_fas = GuestAppointment::selectRaw('COUNT(guest_id) as total, mst_appointment_status.status as name')
                    ->where('staff_id', '=', $staff->id)
                    ->where('club_id', '=', $staff->club_id)
                    ->join('guest_appointment_status', function ($status) use($date) {
                        $status->on('guest_appointment_status.ga_id', '=', 'guest_appointments.id');
                        $status->whereDate('s_date', '=', Carbon::parse($date)->toDateString());
                    })
                    ->join('mst_appointment_status', function ($status) {
                        $status->on('mst_appointment_status.id', '=', 'guest_appointment_status.status_id');
                    })
                    ->groupBy('mst_appointment_status.status')
                    ->get();

//                $coach_promos = BuddyReferralLead::select('id')
//                    ->where('staff_id', '=', $staff->id)
//                    ->whereHas('guest_gmmember', function ($gmmember) use ($date) {
//                        $gmmember->whereHas('gmmember_gmpayment', function ($payment) use ($date) {
//                            $payment->whereDate('payment_date', '=', Carbon::parse($date)->toDateString());
//                        });
//                    })
//                    ->count();

//                if (!empty($coach_promos)):
//                    foreach ($coach_fas as $fa):
//                        array_push($categories, $fa);
//                    endforeach;
//
//                    array_push($categories, ['total' => $coach_promos, 'name' => 'FS']);
//                else:
//                    $categories = $coach_fas;
//                endif;

                $categories = $coach_fas;
            elseif ($request->detail == 3):
                $coach_fwo = MemberPersonal::where('sales_person_id', '=', $staff->id)
                    ->whereHas('memberpersonal_user', function ($user) use ($date, $staff) {
                        $user->where('role_id', '=', '7')
                            ->where('club_id', '=', $staff->club_id);
                        $user->whereHas('user_logmemberstatus', function ($q) use ($date) {
                            $q->whereDate('from_date', '<=', Carbon::parse($date)->toDateString());
                        });
                        $user->whereHas('user_workout', function ($r) use ($date) {
                            $r->whereNotIn('user_id', DB::table('workouts')
                                ->WhereDate('workout_date', '<', Carbon::parse($date))
                                ->pluck('user_id'));
                        });
                    })
                    ->count();

                array_push($categories, ['total' => $coach_fwo, 'name' => 'FWO']);
            elseif ($request->detail == 4):
                $coach_gloves = ProductPaymentDetail::whereHas('productpaymentdetail_productpayment', function ($payment) use ($staff, $date) {
                    $payment->where('cashier_id', '=', $staff->id)
                        ->where('club_id', '=', $staff->club_id)
                        ->whereDate('payment_date', '=', Carbon::parse($date)->toDateString());
                })
                    ->whereHas('productpaymentdetail_product', function ($product) {
                        $product->where('item_name', 'ilike', '%glove%');
                    })
                    ->sum('qty');

                array_push($categories, ['total' => $coach_gloves, 'name' => 'Gloves']);
            elseif ($request->detail == 5):
                $coach_lockers = LockerPayment::selectRaw('COUNT(locker_payments.id) as total, mst_lockers.name as name')
                    ->where('staff_id', '=', $staff->id)
                    ->whereDate('payment_date', '=', Carbon::parse($date)->toDateString())
                    ->whereHas('lockerpayment_locker', function ($locker) use ($staff) {
                        $locker->where('club_id', '=', $staff->club_id);
                    })
                    ->join('mst_lockers', function ($locker) {
                        $locker->on('mst_lockers.id', '=', 'locker_payments.locker_id');
                    })
                    ->groupBy('mst_lockers.name')
                    ->get();

                $categories = $coach_lockers;
            elseif ($request->detail == 6):
                $coach_leads = BuddyReferralLead::selectRaw('COUNT(buddy_referral_leads.id) as total, mst_appointment_status.status as name')
                    ->where('buddy_referral_leads.staff_id', '=', $staff->id)
                    ->whereDate('buddy_referral_leads.created_at', '=', Carbon::parse($date)->toDateString())
                    ->join('guest_appointments', function ($ga) {
                        $ga->on('guest_appointments.guest_id', '=', 'buddy_referral_leads.id');
                    })
                    ->join('guest_appointment_status', function ($ga_status) {
                        $ga_status->on('guest_appointment_status.ga_id', '=', 'guest_appointments.id');
                    })
                    ->join('mst_appointment_status', function ($status) {
                        $status->on('mst_appointment_status.id', '=', 'guest_appointment_status.status_id');
                    })
                    ->groupBy('mst_appointment_status.status')
                    ->get();

                $categories = $coach_leads;
            endif;
        else:
            if ($request->category == 'weekly'):
                $from_date = $date;
                $thru_date = Carbon::parse($date)->addWeek();
            elseif ($request->category == 'monthly'):
                $from_date = Carbon::parse($date)->firstOfMonth();
                $thru_date = Carbon::parse($date)->lastOfMonth();
            endif;

            if ($request->detail == 1):
                // Penjualan Membership
                $coach_sales = User::selectRaw('COUNT(users.id) as total, 
                membership_types.membership_type, 
                membership_types.duration, 
                membership_types.payment_type')
                    ->whereHas('user_memberpersonal', function ($personal) use ($staff) {
                        $personal->where('sales_person_id', '=', $staff->id);
                    })
                    ->whereHas('user_memberpayment', function ($payment) use ($from_date, $thru_date) {
                        $payment->where('payment_category', '=', 'First')
                            ->whereDate('payment_date', '>=', Carbon::parse($from_date)->toDateString())
                            ->whereDate('payment_date', '<', Carbon::parse($thru_date)->toDateString());
                    })
                    ->join('member_status', function ($status) {
                        $status->on('users.id', '=', 'member_status.user_id');
                    })
                    ->join('membership_types', function ($membership) {
                        $membership->on('member_status.membership_type_id', '=', 'membership_types.id');
                    })
                    ->groupBy('membership_types.membership_type', 'membership_types.duration', 'membership_types.payment_type')
                    ->get();

                foreach ($coach_sales as $sale):
                    $type = '';
                    if ($sale->membership_type == 'Regular'):
                        if ($sale->payment_type == 'Cash'):
                            $type = 'MM';
                        elseif ($sale->payment_type == 'Prepaid'):
                            $type = 'PP';
                        endif;
                    elseif ($sale->membership_type == 'Student'):
                        $type = 'ST';
                    elseif ($sale->membership_type == 'Corporate'):
                        $type = 'CP';
                    endif;

                    if ($type != 'MM'):
                        $type .= $sale->duration;
                    endif;

                    array_push($categories, ['total' => $sale->total, 'name' => $type]);
                endforeach;

                // Penjualan Promo
                $coach_promos = BuddyReferralLead::selectRaw('COUNT(buddy_referral_leads.id) as total')
                    ->where('staff_id', '=', $staff->id)
                    ->whereHas('guest_gmmember', function ($gmmember) use ($from_date, $thru_date) {
                        $gmmember->whereHas('gmmember_gmpayment', function ($payment) use ($from_date, $thru_date) {
                            $payment->whereDate('payment_date', '>=', Carbon::parse($from_date)->toDateString())
                                ->whereDate('payment_date', '<', Carbon::parse($thru_date)->toDateString());
                        });
                    })
                    ->get();

                foreach ($coach_promos as $promo):
                    if ($promo->total > 0):
                        $promo->setAttribute('name', 'FS');
                        array_push($categories, $promo);
                    endif;
                endforeach;
            elseif ($request->detail == 2):
                $coach_fas = GuestAppointment::selectRaw('COUNT(guest_id) as total, mst_appointment_status.status as name')
                    ->where('staff_id', '=', $staff->id)
                    ->where('club_id', '=', $staff->club_id)
                    ->join('guest_appointment_status', function ($status) use($from_date, $thru_date) {
                        $status->on('guest_appointment_status.ga_id', '=', 'guest_appointments.id');
                        $status->whereDate('s_date', '>=', Carbon::parse($from_date)->toDateString())
                            ->whereDate('s_date', '<', Carbon::parse($thru_date)->toDateString());
                    })
                    ->join('mst_appointment_status', function ($status) {
                        $status->on('mst_appointment_status.id', '=', 'guest_appointment_status.status_id');
                    })
                    ->groupBy('mst_appointment_status.status')
                    ->get();

                $categories = $coach_fas;
            elseif ($request->detail == 3):
                $coach_fwo = MemberPersonal::where('sales_person_id', '=', $staff->id)
                    ->whereHas('memberpersonal_user', function ($user) use ($from_date, $thru_date, $staff) {
                        $user->where('role_id', '=', '7')
                            ->where('club_id', '=', $staff->club_id);
                        $user->whereHas('user_logmemberstatus', function ($q) use ($from_date, $thru_date) {
                            $q->whereDate('from_date', '<=', Carbon::parse($from_date)->toDateString())
                                ->whereDate('from_date', '<=', Carbon::parse($thru_date)->toDateString());
                        });
                        $user->whereHas('user_workout', function ($r) use ($from_date, $thru_date) {
                            $r->whereNotIn('user_id', DB::table('workouts')
                                ->WhereDate('workout_date', '<', Carbon::parse($from_date))
                                ->WhereDate('workout_date', '<', Carbon::parse($thru_date))
                                ->pluck('user_id'));
                        });
                    })
                    ->count();

                array_push($categories, ['total' => $coach_fwo, 'name' => 'FWO']);
            elseif ($request->detail == 4):
                $coach_gloves = ProductPaymentDetail::whereHas('productpaymentdetail_productpayment', function ($payment) use ($staff, $from_date, $thru_date) {
                    $payment->where('cashier_id', '=', $staff->id)
                        ->where('club_id', '=', $staff->club_id)
                        ->whereDate('payment_date', '>=', Carbon::parse($from_date)->toDateString())
                        ->whereDate('payment_date', '>=', Carbon::parse($thru_date)->toDateString());
                })
                    ->whereHas('productpaymentdetail_product', function ($product) {
                        $product->where('item_name', 'ilike', '%glove%');
                    })
                    ->sum('qty');

                array_push($categories, ['total' => $coach_gloves, 'name' => 'Gloves']);
            elseif ($request->detail == 5):
                $coach_lockers = LockerPayment::selectRaw('COUNT(locker_payments.id) as total, mst_lockers.name as name')
                    ->where('staff_id', '=', $staff->id)
                    ->whereDate('payment_date', '>=', Carbon::parse($from_date)->toDateString())
                    ->whereDate('payment_date', '<', Carbon::parse($thru_date)->toDateString())
                    ->whereHas('lockerpayment_locker', function ($locker) use ($staff) {
                        $locker->where('club_id', '=', $staff->club_id);
                    })
                    ->join('mst_lockers', function ($locker) {
                        $locker->on('mst_lockers.id', '=', 'locker_payments.locker_id');
                    })
                    ->groupBy('mst_lockers.name')
                    ->get();

                $categories = $coach_lockers;
            elseif ($request->detail == 6):
                $coach_leads = BuddyReferralLead::selectRaw('COUNT(buddy_referral_leads.id) as total, mst_appointment_status.status as name')
                    ->where('buddy_referral_leads.staff_id', '=', $staff->id)
                    ->whereDate('buddy_referral_leads.created_at', '>=', Carbon::parse($from_date)->toDateString())
                    ->whereDate('buddy_referral_leads.created_at', '<', Carbon::parse($thru_date)->toDateString())
                    ->join('guest_appointments', function ($ga) {
                        $ga->on('guest_appointments.guest_id', '=', 'buddy_referral_leads.id');
                    })
                    ->join('guest_appointment_status', function ($ga_status) {
                        $ga_status->on('guest_appointment_status.ga_id', '=', 'guest_appointments.id');
                    })
                    ->join('mst_appointment_status', function ($status) {
                        $status->on('mst_appointment_status.id', '=', 'guest_appointment_status.status_id');
                    })
                    ->groupBy('mst_appointment_status.status')
                    ->get();

                $categories = $coach_leads;
            endif;
        endif;

        return response()->json($categories, $this->successStatus);
    }

    public function best_coach_club_v3(Request $request) {
        $date = Carbon::today();

        if (!empty($request->chosenDate)):
            $date = Carbon::parse($request->chosenDate);
        endif;

        if ($request->category == 'daily'):
            // Penjualan Membership
            $club_sales = User::selectRaw('users.id, users.name, member_personals.photo,
            (
                SELECT SUM(member_payments.amount_paid)
                FROM users u2
                INNER JOIN member_personals ON member_personals.user_id = u2.id 
                INNER JOIN member_payments ON member_payments.user_id = u2.id
                 WHERE member_personals.sales_person_id = "users"."id"
                 AND member_payments.payment_date = \''.Carbon::parse($date)->toDateString().'\' 
            ) as total_membership, 
            (
                SELECT SUM(guest_member_payments.amount_paid)
                FROM buddy_referral_leads brl
                JOIN guest_membership_members ON guest_membership_members.guest_id = brl.id
                JOIN guest_member_payments ON guest_member_payments.guest_membership_member_id = guest_membership_members.id
                WHERE guest_member_payments.payment_date = \''.Carbon::parse($date)->toDateString().'\'
                AND brl.staff_id = "users"."id"
            ) as total_promo,
            (
                SELECT SUM(locker_payments.pay)
                FROM locker_payments
                WHERE locker_payments.staff_id = "users"."id"
                AND locker_payments.payment_date = \''.Carbon::parse($date)->toDateString().'\'
            ) as total_locker,
            (
                SELECT (SUM(product_payment_details.qty) * product_payment_details.price)
                FROM product_payment_details
                JOIN product_payments ON product_payments.id = product_payment_details.product_payment_id
                JOIN mst_products ON mst_products.id = product_payment_details.product_id
                WHERE product_payments.cashier_id = "users"."id"
                AND mst_products.item_name ILIKE \'%glove%\'
                AND product_payments.payment_date = \''.Carbon::parse($date)->toDateString().'\'
                GROUP BY product_payment_details.price
            ) as total_gloves')
                ->leftJoin('member_personals', function ($personal) {
                    $personal->on('member_personals.user_id', '=', 'users.id');
                })
                ->where('users.club_id', '=', Auth::user()->club_id)
                ->where('users.role_id', '<>', '7')
                ->get();
        else:
            if ($request->category == 'weekly'):
                $from_date = $date;
                $thru_date = Carbon::parse($date)->addWeek();
            elseif ($request->category == 'monthly'):
                $from_date = Carbon::parse($date)->firstOfMonth();
                $thru_date = Carbon::parse($date)->lastOfMonth();
            endif;

            // Penjualan Membership
            $club_sales = User::selectRaw('users.id, users.name, member_personals.photo,
            (
                SELECT SUM(member_payments.amount_paid)
                FROM users u2
                INNER JOIN member_personals ON member_personals.user_id = u2.id 
                INNER JOIN member_payments ON member_payments.user_id = u2.id
                 WHERE member_personals.sales_person_id = "users"."id"
                 AND member_payments.payment_date >= \''.Carbon::parse($from_date)->toDateString().'\' 
                 AND member_payments.payment_date < \''.Carbon::parse($thru_date)->toDateString().'\' 
            ) as total_membership,
            (
                SELECT SUM(guest_member_payments.amount_paid)
                FROM buddy_referral_leads brl
                JOIN guest_membership_members ON guest_membership_members.guest_id = brl.id
                JOIN guest_member_payments ON guest_member_payments.guest_membership_member_id = guest_membership_members.id
                WHERE guest_member_payments.payment_date >= \''.Carbon::parse($from_date)->toDateString().'\'
                AND guest_member_payments.payment_date < \''.Carbon::parse($thru_date)->toDateString().'\'
                AND brl.staff_id = "users"."id"
            ) as total_promo,
            (
                SELECT SUM(locker_payments.pay)
                FROM locker_payments
                WHERE locker_payments.staff_id = "users"."id"
                AND locker_payments.payment_date >= \''.Carbon::parse($from_date)->toDateString().'\'
                AND locker_payments.payment_date < \''.Carbon::parse($thru_date)->toDateString().'\'
            ) as total_locker,
            (
                SELECT (SUM(product_payment_details.qty * product_payment_details.price))
                FROM product_payment_details
                JOIN product_payments ON product_payments.id = product_payment_details.product_payment_id
                JOIN mst_products ON mst_products.id = product_payment_details.product_id
                WHERE product_payments.cashier_id = "users"."id"
                AND mst_products.item_name ILIKE \'%glove%\'
                AND product_payments.payment_date >= \''.Carbon::parse($from_date)->toDateString().'\'
                AND product_payments.payment_date < \''.Carbon::parse($thru_date)->toDateString().'\'
            ) as total_gloves')
                ->leftJoin('member_personals', function ($personal) {
                    $personal->on('member_personals.user_id', '=', 'users.id');
                })
                ->where('users.club_id', '=', Auth::user()->club_id)
                ->where('users.role_id', '<>', '7')
                ->get();
        endif;

        $club_membership = $club_sales->sum('total_membership');
        $club_promo = $club_sales->sum('total_promo');
        $club_locker = $club_sales->sum('total_locker');
        $club_gloves = $club_sales->sum('total_gloves');

        foreach ($club_sales as $sale):
            if (!empty($sale->photo)):
                $sale->setAttribute('photo', 'member_photos/'.$sale->photo);
            else:
                $sale->setAttribute('photo', 'img.png');
            endif;

            $sale->setAttribute('total', $sale->total_membership + $sale->total_promo + $sale->total_locker + $sale->total_gloves);
            $sale->setAttribute('total_club', $club_membership + $club_promo + $club_locker + $club_gloves);
        endforeach;



        return response()->json([
            'coach_sales' => $club_sales->sortByDesc('total')->values(),
//            'club_revenue' => ($club_membership + $club_promo + $club_locker + $club_gloves)
        ],$this->successStatus);
    }

    public function get_data_staff_daily_report(Request $request) {
        $date = Carbon::today();

        if (!empty($request->chosenDate)):
            $date = Carbon::parse($request->chosenDate);
        endif;

        $coachReportData = CoachDailyReportHeader::where('coach_id', '=', $request->id)->whereDate('date', '=', Carbon::parse($date)->toDateString())->first();
        if (!empty($coachReportData)) {
            if ($request->type == 'source') {
                $data_source = CoachDailyReportHeader::selectRaw('
                    users.name as name,
                    date as date,
                    member_sources.source as source_name,
                    total_calling as total_calling,
                    answer as answer,
                    apo as apo,
                    reschedule as reschedule,
                    reject as reject,
                    wrong_number as wrong_number,
                    invalid as invalid,
                    na as na,
                    leads as leads,
                    (
                        SELECT sum(total_calling)
                        FROM coach_daily_report_ar_details
                        WHERE coach_daily_report_header_id = '.$coachReportData->id.'
                    ) as total_all_calling,
                    (
                        SELECT sum(answer)
                        FROM coach_daily_report_ar_details
                        WHERE coach_daily_report_header_id = '.$coachReportData->id.'
                    ) as total_answer,
                    (
                        SELECT sum(apo)
                        FROM coach_daily_report_ar_details
                        WHERE coach_daily_report_header_id = '.$coachReportData->id.'
                    ) as total_apo,
                    (
                        SELECT sum(reschedule)
                        FROM coach_daily_report_ar_details
                        WHERE coach_daily_report_header_id = '.$coachReportData->id.'
                    ) as total_reschedule,
                    (
                        SELECT sum(reject)
                        FROM coach_daily_report_ar_details
                        WHERE coach_daily_report_header_id = '.$coachReportData->id.'
                    ) as total_reject,
                    (
                        SELECT sum(wrong_number)
                        FROM coach_daily_report_ar_details
                        WHERE coach_daily_report_header_id = '.$coachReportData->id.'
                    ) as total_wrong_number,
                    (
                        SELECT sum(invalid)
                        FROM coach_daily_report_ar_details
                        WHERE coach_daily_report_header_id = '.$coachReportData->id.'
                    ) as total_invalid,
                    (
                        SELECT sum(na)
                        FROM coach_daily_report_ar_details
                        WHERE coach_daily_report_header_id = '.$coachReportData->id.'
                    ) as total_na,
                    (
                        SELECT sum(leads)
                        FROM coach_daily_report_ar_details
                        WHERE coach_daily_report_header_id = '.$coachReportData->id.'
                    ) as total_leads
                ')
                    ->whereDate('date', '=',  Carbon::parse($date)->toDateString())
                    ->join('coach_daily_report_ar_details', function ($cdrd) {
                        $cdrd->on('coach_daily_report_ar_details.coach_daily_report_header_id', '=', 'coach_daily_report_headers.id');
                    })
                    ->join('member_sources', function ($source) {
                        $source->on('member_sources.id', '=', 'coach_daily_report_ar_details.source_id');
                    })
                    ->join('users', function ($user) {
                        $user->on('users.id', '=', 'coach_daily_report_headers.coach_id');
                    })
                    ->orderBy('leads', 'desc')
                    ->get();

                return response()->json($data_source, $this->successStatus);
            } elseif ($request->type == 'sales') {
                $category_sales = CoachDailyReportCategory::selectRaw('
                    coach_daily_report_sales_details.coach_daily_report_header_id as header_id,
                    mst_coach_daily_report_categories.id as id,
                    category as category,
                    sum(total) as total
                ')
                    ->where('coach_daily_report_header_id', '=', $coachReportData->id)
                    ->join('coach_daily_report_sales_details', function ($cdrsd) {
                        $cdrsd->on('coach_daily_report_sales_details.category_id', '=', 'mst_coach_daily_report_categories.id');
                    })
                    ->groupBy('coach_daily_report_sales_details.coach_daily_report_header_id', 'mst_coach_daily_report_categories.id')
                    ->orderBy('mst_coach_daily_report_categories.id', 'asc')
                    ->get();

                foreach ($category_sales as $category) {
                    $child = CoachDailyReportSalesDetail::where('coach_daily_report_header_id', '=', $category->header_id)->where('category_id', '=', $category->id)->get();
                    if (count($child) > 1) {
                        $category->setAttribute('child', true);
                    } else {
                        $category->setAttribute('child', false);
                    }
                }

                return response()->json($category_sales, $this->successStatus);
            }
        }
    }

    public function get_data_staff_daily_report_sales_detail(Request $request) {
        if (!empty($request->header_id)) {
            $detail = CoachDailyReportSalesDetail::selectRaw('
                id,
                category_id,
                type,
                total
            ')
                ->where('coach_daily_report_header_id', '=', $request->header_id)
                ->where('category_id', '=', $request->id)
                ->orderBy('id', 'asc')
                ->get();

            return response()->json($detail, $this->successStatus);
        }

    }
}