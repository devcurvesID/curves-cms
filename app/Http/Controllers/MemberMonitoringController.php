<?php

namespace App\Http\Controllers;

use App\Http\Request\MemberMonitoringRequest;
use App\Model\Guest\BuddyReferralLead;
use App\Model\Log\LogContactedNewLead;
use App\Model\Workout\Workout;
use App\Model\Club\ClubInfo;
use App\Model\Member\MemberMonitoring;
use App\Model\Member\ContactType;
use Carbon\Carbon;
use Session;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class MemberMonitoringController extends Controller
{
    public function choose_mm_menu()
    {
        return view('cf_lite.member.member-monitoring-option');
    }

    // Workout Monitoring
    public function workout_monitoring_list()
    {
        if (Auth::user()) {
            $user = Auth::user();
            $club_id = $user->club_id;
            $club = ClubInfo::findOrFail($club_id);
            $neworfilter = 'new';

            $list_wo = User::where('club_id', '=', $club_id)->where('role_id', '=', '7')->whereHas('user_memberpersonal', function ($q) {

            })->get();

            $staffs = User::where('role_id', '<>', '7')->pluck('name', 'id');

            foreach ($list_wo as $user) {

                // Hitung total workout user
                $total = 0;
                foreach ($user->user_workout as $workout) {
                    $total++;
                }
                $user->setAttribute('total_wo', $total);

                // Hitung total panggilan ke user
                $totalCall = 0;
                $totalMessage = 0;
                foreach ($user->user_membermonitoring as $monitoring) {
                    if ($monitoring->contact_type_id == 1) {
                        $totalCall += $monitoring->call_count;
                        $totalMessage += $monitoring->message_count;
                    }
                }
                $user->setAttribute('total_call', $totalCall);
                $user->setAttribute('total_message', $totalMessage);
            }

            return view('cf_lite.member.workout-monitoring-list', compact('list_wo', 'club', 'staffs', 'neworfilter'));
        } else {
            return redirect('login');
        }
    }

    public function workout_monitoring_list_filter(Request $request)
    {
        if (Auth::user()) {
            $user = Auth::user();
            $club_id = $user->club_id;
            $club = ClubInfo::findOrFail($club_id);
            $neworfilter = 'filter';

            $list_wo = User::where('club_id', '=', $club_id)->where('role_id', '=', '7')->whereHas('user_memberpersonal', function ($q) {

            })->get();

            $staffs = User::where('role_id', '<>', '7')->pluck('name', 'id');

            foreach ($list_wo as $user) {

                // Hitung total workout user
                $total = 0;
                foreach ($user->user_workout as $workout) {
                    if ($workout->workout_date >= $request->from_date && $workout->workout_date <= $request->thru_date) {
                        $total++;
                    }
                }
                $user->setAttribute('total_wo', $total);

                // Hitung total panggilan ke user
                $totalCall = 0;
                $totalMessage = 0;
                foreach ($user->user_membermonitoring as $monitoring) {
                    if ($monitoring->contact_date >= $request->from_date && $monitoring->contact_date <= $request->thru_date && $monitoring->contact_type_id == 1) {
                        $totalCall += $monitoring->call_count;
                        $totalMessage += $monitoring->message_count;
                    }
                }
                $user->setAttribute('total_call', $totalCall);
                $user->setAttribute('total_message', $totalMessage);
            }

            return view('cf_lite.member.workout-monitoring-list', compact('list_wo', 'club', 'staffs', 'neworfilter'));
        } else {
            return redirect('login');
        }
    }

    public function store_member_monitoring(Request $request)
    {
        $this->validate($request, [
            'staff' => 'required',
            'contact_date' => 'required',
        ]);

        if (!($request->call_count == 0 && $request->message_count == 0)) {

            $member_monitoring = new MemberMonitoring;
            $member_monitoring->user_id = $request->user_id;
            $member_monitoring->staff_id = $request->staff;
            $member_monitoring->contact_date = $request->contact_date;
            $member_monitoring->call_count = $request->call_count;
            $member_monitoring->message_count = $request->message_count;
            $member_monitoring->contact_type_id = '1';
            $member_monitoring->save();

            $list_wo = User::where('id', '=', $request->user_id)->get();

            $totalCall = 0;
            $totalMessage = 0;

            if ($request->neworfilter == 'new') {
                foreach ($list_wo as $user) {

                    // Hitung total panggilan ke user
                    foreach ($user->user_membermonitoring as $monitoring) {
                        $totalCall += $monitoring->call_count;
                        $totalMessage += $monitoring->message_count;
                    }
                }
            } else {
                $totalCall = $request->total_call + $request->call_count;
                $totalMessage = $request->total_message + $request->message_count;
            }

            return response()->json([
                'call_count' => $totalCall,
                'message_count' => $totalMessage,
            ]);
        }
    }

    //Birthday Monitoring 
    public function birthday_monitoring_list()
    {
        if (Auth::user()) {
            $user = Auth::user();
            $club_id = $user->club_id;
            $club = ClubInfo::findOrFail($club_id);
            $neworfilter = 'new';
            $months = [
                '1' => 'Januari',
                '2' => 'Februari',
                '3' => 'Maret',
                '4' => 'April',
                '5' => 'Mei',
                '6' => 'Juni',
                '7' => 'Juli',
                '8' => 'Agustus',
                '9' => 'September',
                '10' => 'Oktober',
                '11' => 'November',
                '12' => 'Desember'
            ];
            $month = 1;

            $list_bday = User::where('club_id', '=', $club_id)->where('role_id', '=', '7')->whereHas('user_memberpersonal', function ($q) {

            })->get();

            $staffs = User::where('role_id', '<>', '7')->pluck('name', 'id');

            foreach ($list_bday as $user) {

                // Hitung total workout user
                $total = 0;
                foreach ($user->user_workout as $workout) {
                    $total++;
                }
                $user->setAttribute('total_wo', $total);

                // Hitung total panggilan ke user
                $totalCall = 0;
                $totalMessage = 0;
                foreach ($user->user_membermonitoring as $monitoring) {
                    if ($monitoring->contact_type_id == 2 && Carbon::parse($monitoring->contact_date)->format('Y') == Carbon::now()->year) {
                        $totalCall += $monitoring->call_count;
                        $totalMessage += $monitoring->message_count;
                    }
                }
                $user->setAttribute('total_call', $totalCall);
                $user->setAttribute('total_message', $totalMessage);
            }

            return view('cf_lite.member.birthday-monitoring-list', compact('list_bday', 'club', 'staffs', 'neworfilter', 'months', 'month'));
        } else {
            return redirect('login');
        }
    }

    public function birthday_monitoring_list_filter(Request $request)
    {
        if (Auth::user()) {
            $user = Auth::user();
            $club_id = $user->club_id;
            $club = ClubInfo::findOrFail($club_id);
            $neworfilter = 'filter';
            $months = [
                '1' => 'Januari',
                '2' => 'Februari',
                '3' => 'Maret',
                '4' => 'April',
                '5' => 'Mei',
                '6' => 'Juni',
                '7' => 'Juli',
                '8' => 'Agustus',
                '9' => 'September',
                '10' => 'Oktober',
                '11' => 'November',
                '12' => 'Desember'
            ];
            $month = $request->date_filter;

            $list_bday = User::where('club_id', '=', $club_id)->where('role_id', '=', '7')->whereHas('user_memberpersonal', function ($q) use ($month) {

            })->get();

            $staffs = User::where('role_id', '<>', '7')->pluck('name', 'id');

            foreach ($list_bday as $user) {

                // Hitung total panggilan ke user
                $totalCall = 0;
                $totalMessage = 0;
                foreach ($user->user_membermonitoring as $monitoring) {
                    if ($monitoring->contact_type_id == 2 && Carbon::parse($monitoring->contact_date)->format('Y') == Carbon::now()->year) {
                        $totalCall += $monitoring->call_count;
                        $totalMessage += $monitoring->message_count;
                    }
                }
                $user->setAttribute('total_call', $totalCall);
                $user->setAttribute('total_message', $totalMessage);
            }

            return view('cf_lite.member.birthday-monitoring-list', compact('list_bday', 'club', 'staffs', 'neworfilter', 'months', 'month'));
        } else {
            return redirect('login');
        }
    }

    public function store_member_monitoring_birthday(Request $request)
    {
        $this->validate($request, [
            'staff' => 'required',
            'contact_date' => 'required',
        ]);

        if (!($request->call_count == 0 && $request->message_count == 0)) {

            $member_monitoring = new MemberMonitoring;
            $member_monitoring->user_id = $request->user_id;
            $member_monitoring->staff_id = $request->staff;
            $member_monitoring->contact_date = $request->contact_date;
            $member_monitoring->call_count = $request->call_count;
            $member_monitoring->message_count = $request->message_count;
            $member_monitoring->contact_type_id = '2';
            $member_monitoring->save();

            $list_bday = User::where('id', '=', $request->user_id)->get();

            $totalCall = 0;
            $totalMessage = 0;

            if ($request->neworfilter == 'new') {
                foreach ($list_bday as $user) {

                    // Hitung total panggilan ke user
                    foreach ($user->user_membermonitoring as $monitoring) {
                        if ($monitoring->contact_type_id == 2 && Carbon::parse($monitoring->contact_date)->format('Y') == Carbon::now()->year) {
                            $totalCall += $monitoring->call_count;
                            $totalMessage += $monitoring->message_count;
                        }
                    }
                }
            } else {
                $totalCall = $request->total_call + $request->call_count;
                $totalMessage = $request->total_message + $request->message_count;
            }

            return response()->json([
                'call_count' => $totalCall,
                'message_count' => $totalMessage,
            ]);
        }
    }

    //Workout Club Monitoring List
    public function workout_club_monitoring_list(Request $request)
    {
//        $workouts = Workout::selectRaw('user_id, workouts.club_id, COUNT(workout_date) as wo_count')
//            ->whereHas('workout_user', function ($q) {
//                $q->where('club_id', '=', Auth::user()->club_id);
//            })
//            ->leftJoin('users', function ($join) {
//                $join->on('users.id', '=', 'workouts.user_id');
//            })
//            ->groupBy('user_id', 'workouts.club_id')
//            ->get();

        $workouts = User::selectRaw('users.id, name, workouts.club_id, COUNT(workout_date) as wo_count')
            ->where('users.club_id', '=', Auth::user()->club_id)
            ->leftJoin('workouts', function ($join) use ($request) {
                $join->on('workouts.user_id', '=', 'users.id');
                if (!empty($request->from_date)):
                    $join->whereDate('workout_date', '>=', Carbon::parse($request->from_date)->toDateString());
                endif;

                if (!empty($request->thru_date)):
                    $join->whereDate('workout_date', '<=', Carbon::parse($request->thru_date)->toDateString());
                endif;
            })
            ->groupBy('users.id', 'workouts.club_id')
            ->orderBy('users.id', 'asc')
            ->get();

        foreach ($workouts->where('wo_count', '<>', '0')->where('club_id', '<>', Auth::user()->club_id) as $wo):
            foreach ($workouts->where('id', $wo->id)->where('club_id', Auth::user()->club_id) as $wo_home):
                if ($wo_home->wo_count <= $wo->wo_count):
                    $wo_home->setAttribute('flag', 'Move');

                    if (empty($wo_home->destination)):
                        $club = ClubInfo::find($wo->club_id);
                        $wo_home->setAttribute('destination', $club->club_name);
                    endif;
                endif;
            endforeach;
        endforeach;

        $workout_shows = $workouts->where('wo_count', '<>', '0');
        foreach ($workout_shows as $workout):
            if (!empty($workout->club_id)):
                $club = ClubInfo::find($workout->club_id);
                $workout->setAttribute('club_name', $club->club_name);
            endif;
        endforeach;

        return view('cf_lite.member.workout-club-monitoring', compact('workout_shows'));
    }

    public function monitoring_lead_member()
    {
        $club = Auth::user()->user_club;

        $brls = BuddyReferralLead::selectRaw('buddy_referral_leads.id as id, buddy_referral_leads.fullname, 
        buddy_referral_leads.staff_id, buddy_referral_leads.club_id, buddy_referral_leads.status_gm, buddy_referral_leads.created_at')
            ->where('buddy_referral_leads.club_id', '=', Auth::user()->club_id)
            ->where(function ($q) {
                $q->where('buddy_referral_leads.status_gm', '=', 'Member');
                $q->orwhere('buddy_referral_leads.status_gm', '=', 'Guest');
            })
            ->where(function ($r) {
                $r->where('member_status.from_date', '>=', Carbon::now()->subMonth());
                $r->where('member_status.from_date', '<=', Carbon::now());
            });

        $brls = $brls->join('users', 'users.id', '=', 'buddy_referral_leads.staff_id')
            ->join('member_status', 'member_status.user_id', '=', 'users.id')->get();

        $staff = User::where('club_id', '=', Auth::user()->club_id)
            ->where('role_id', '<>', '7')
            ->where('role_id', '>', '2')
            ->orderBy('name', 'asc')
            ->pluck('name', 'id');
        return view('cf_lite.member.member-lead-monitoring-list', compact('brls', 'club', 'staff'));
    }

    public function store_bulk_lead_member(Request $request)
    {
        $members = $request->member;
        foreach ($members as $key => $value):
            if (!empty($value)):
                for ($i = 0; $i < $request->call[$key]; $i++):
                    $log_contacted_new_lead = new LogContactedNewLead();
                    $log_contacted_new_lead->guest_id = $request->member[$key];
                    $log_contacted_new_lead->staff_id = $request->staff[$key];
                    if ($request->status[$key] == 'answer'):
                        $log_contacted_new_lead->status = 'Answered';
                    elseif ($request->status[$key] == 'notanswer'):
                        $log_contacted_new_lead->status = 'NotAnswered';
                    endif;
                    $log_contacted_new_lead->save();
                endfor;
            endif;
        endforeach;

        Session::flash('flash_message', trans('messages.lead_call_log_monitoring_successfully_added'));

        return redirect('cf-lite/member/monitoring-lead-member-list');
    }
}
