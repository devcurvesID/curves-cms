<?php

namespace App\Http\Controllers;

use App\Model\Club\ClubInfo;
use App\Model\Log\LogMemberStatus;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Session;
use App\Model\Log\LogAdditionalBonus;
use App\Model\MasterData\MembershipType;
use Illuminate\Http\Request;

class BonusDevController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('developer-only');
    }

    public function main() {
        $logs = LogAdditionalBonus::orderBy('created_at', 'desc')->take(5)->get();
        $memberships = MembershipType::all();
        $clubs = ClubInfo::pluck('club_name', 'id');

        return view('developer.additional-bonus-main', compact('logs', 'memberships', 'clubs'));
    }

    public function execute(Request $request) {
        $this->validate($request, [
            'date' => 'required|date',
            'additional' => 'required|numeric',
            'club' => 'required|integer|exists:club_infos,id',
        ]);

        $memberships = $request->membership;

        if (count($memberships) > 0):
            $active_members = User::where('role_id', '=', '7')
                ->where('club_id', '=', $request->club)
                ->whereHas('user_memberstatus', function ($q) use ($request) {
                    $q->where('status', '=', 'Active')
                        ->whereDate('from_date', '<=', Carbon::parse($request->date)->toDateString())
                        ->whereDate('thru_date', '>=', Carbon::parse($request->date)->toDateString())
                        ->where(function ($r) use ($request) {
                        $counter = 0;
                        foreach ($request->membership as $key => $value):
                            if ($counter == 0):
                                $r->where('membership_type_id', '=', $value);
                                $counter++;
                            else:
                                $r->orWhere('membership_type_id', '=', $value);
                            endif;
                        endforeach;
                    });
                })
                ->whereHas('user_memberpayment', function ($s) use ($request) {
                    $s->whereDate('payment_date', '<=', Carbon::parse($request->date)->toDateString())
                        ->where('payment_status', '=', 'Paid')
                        ->where('rest_of_bill', '=', '0');
                })
                ->get();

            if (!empty($active_members) && (count($active_members) > 0)):
                foreach ($active_members as $active_member):
                    $member = User::findOrFail($active_member->id);
                    $last_thru = $member->user_memberstatus->thru_date;

                    //Update Member Status
                    $member_status = $member->user_memberstatus;
                    $member_status->thru_date = Carbon::parse($member_status->thru_date)->addMonths($request->additional);
                    $member->user_memberstatus()->save($member_status);

                    //Update Log
                    $log_member_status = LogMemberStatus::where('user_id', '=', $active_member->id)
                        ->where(function ($q) use ($request) {
                            $counter = 0;
                            foreach ($request->membership as $key => $value):
                                if ($counter == 0):
                                    $q->where('membership_type_id', '=', $value);
                                    $counter++;
                                else:
                                    $q->orWhere('membership_type_id', '=', $value);
                                endif;
                            endforeach;
                        })
                        ->whereDate('from_date', '<=', Carbon::parse($request->date)->toDateString())
                        ->whereDate('thru_date', '>=', Carbon::parse($request->date)->toDateString())
                        ->orderBy('created_at', 'desc')
                        ->first();

                    if (empty($log_member_status)):
                        $log_member_status = LogMemberStatus::where('user_id', '=', $active_member->id)
                            ->where(function ($q) use ($request) {
                                $counter = 0;
                                foreach ($request->membership as $key => $value):
                                    if ($counter == 0):
                                        $q->where('membership_type_id', '=', $value);
                                        $counter++;
                                    else:
                                        $q->orWhere('membership_type_id', '=', $value);
                                    endif;
                                endforeach;
                            })
                            ->orderBy('created_at', 'desc')
                            ->first();
                    endif;

                    if (!empty($log_member_status)):
                        $new_log_member_status = LogMemberStatus::findOrFail($log_member_status->id);
                        $new_log_member_status->thru_date = $member_status->thru_date;
                        $new_log_member_status->save();
                    endif;
                endforeach;

                //Write Log
                $log = new LogAdditionalBonus;
                $log->user_id = Auth::id();
                $log->save();

                Session::flash('flash_message', trans('messages.additional_successful'));
            endif;
        else:
            Session::flash('flash_message', trans('messages.no_membership_has_been_chosen'));
        endif;

        return redirect('dev/additional-bonus');
    }

    public function bonus_duration() {

        return view('developer.bonus-duration');
    }

    public function store_bonus_duration(Request $request) {
        $users = User::where('role_id', '=', 7)
            ->whereHas('user_memberstatus', function ($q) use ($request) {
                $q->whereDate('thru_date', '>=', Carbon::parse($request->date)->toDateString())
                    ->where(function ($r) {
                        $r->where('status', '=', 'Active')->orWhere('status', '=', 'Hold');
                    });
            })
            ->orderBy('id', 'asc')
            ->get();

        foreach ($users as $user) {
            $member = User::findOrFail($user->id);

            //Update Member Status
            $member_status = $member->user_memberstatus;
            $member_status->thru_date = Carbon::parse($member_status->thru_date)->addDay($request->bonus);
            $member->user_memberstatus()->save($member_status);

            //Update Log Member Membership
            $log_member_status = LogMemberStatus::where('user_id', '=', $user->id)
                ->whereDate('thru_date', '>=', Carbon::parse($request->date)->toDateString())
                ->orderBy('created_at', 'desc')
                ->first();

            if (!empty($log_member_status)) {
                $new_log_member_status = LogMemberStatus::findOrFail($log_member_status->id);
                $new_log_member_status->thru_date = $member_status->thru_date;
                $new_log_member_status->save();
            }
        }
        Session::flash('flash_message', trans('messages.bonus_successful_added'));
        return redirect('dev/bonus-duration');
    }
}
