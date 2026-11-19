<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActivateMemberHoldRequest;
use App\Http\Requests\FinishMemberHoldRequest;
use App\Model\Log\LogMemberStatus;
use Session;
use App\Model\MasterData\MemberHoldReason;
use App\Model\Member\MemberHold;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberHoldController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('wm-only');
    }

    public function member_hold_main() {
        $hold_request_list = MemberHold::select('id', 'status')
            ->where('club_id', '=', Auth::user()->club_id)
            ->where('status', '=', 'Pending')
            ->get();

        $hold_list = MemberHold::select('id', 'status')
            ->where('club_id', '=', Auth::user()->club_id)
            ->whereDate('from_date', '<=', Carbon::now()->toDateString())
            ->whereDate('thru_date', '>=', Carbon::now()->toDateString())
            ->where('status', '=', 'OnHold')
            ->get();

        $hold_finished_list = MemberHold::where('club_id', '=', Auth::user()->club_id)
            ->whereDate('thru_date', '<', Carbon::now()->toDateString())
            ->where('status', '=', 'OnHold')
            ->get()
            ->count();

        return view('cf_lite.member.member-hold-main', compact('hold_request_list', 'hold_list', 'hold_finished_list'));
    }

    public function add_new_request() {
        $reasons = MemberHoldReason::pluck('member_hold_reason', 'id');

        return view('cf_lite.member.member-hold-new-request', compact('reasons'));
    }

    public function store_new_member_hold_request(Request $request) {
        $this->validate($request, [
            'member_id' => 'required',
            'member_hold_reason' => 'required',
            'from_date' => 'required|date',
            'thru_date' => 'required|date',
        ]);

        $member = User::findOrFail($request->member_id);

        if (($member->user_memberstatus->memberstatus_membershiptype->duration == '12') || ($member->user_memberstatus->memberstatus_membershiptype->duration == '6') || ($member->user_memberstatus->memberstatus_membershiptype->duration == '24')):
            $member_hold = new MemberHold;
            $member_hold->user_id = $member->id;
            $member_hold->club_id = $member->club_id;
            $member_hold->member_hold_reason_id = $request->member_hold_reason;

            if (!empty($request->member_hold_note)):
                $member_hold->member_hold_note = $request->member_hold_note;
            endif;

            $member_hold->from_date = $request->from_date;
            $member_hold->thru_date = $request->thru_date;
            $member_hold->status = 'Pending';
            $member_hold->save();

            Session::flash('cf_lite.flash_message', trans('messages.member_hold_request_has_been_submitted'));

            return redirect('cf-lite/member-hold');
        else:
            Session::flash('cf_lite.flash_message', trans('messages.member_hold_request_has_been_rejected_due_to_membership_type'));

            return redirect('cf-lite/member-hold/request/add');
        endif;
    }

    public function hold_request_list() {
        $request_list = MemberHold::where('club_id', '=', Auth::user()->club_id)
            ->where('status', '=', 'Pending')
            ->where('from_date', '>', Carbon::now()->toDateString())
            ->paginate(50);

        $to_activate_list = MemberHold::where('club_id', '=', Auth::user()->club_id)
            ->where('status', '=', 'Pending')
            ->where('from_date', '<=', Carbon::now()->toDateString())
            ->orderBy('from_date', 'asc')
            ->get();

        return view('cf_lite.member.member-hold-request-list', compact('request_list', 'to_activate_list'));
    }

    public function cancel_member_hold_request($id) {
        $member_hold = MemberHold::findOrFail($id);
        $member_hold->status = 'Cancelled';
        $member_hold->save();

        Session::flash('cf_lite.flash_message', trans('messages.member_hold_request_has_been_cancelled'));
        return redirect('cf-lite/member-hold/request/list');
    }

    public function activate_member_hold(ActivateMemberHoldRequest $request) {
        if (!empty($request->activate)):
            foreach ($request->activate as $key => $value):
                if (!empty($value)):
                    $member_hold = MemberHold::findOrFail($value);
                    $member_hold->status = 'OnHold';
                    $member_hold->save();

                    $member = User::findOrFail($member_hold->user_id);
                    if (!empty($member->user_memberstatus)):
                        $member_status = $member->user_memberstatus;

                        //count total days hold
                        $diff = strtotime($member_hold->thru_date) - strtotime($member_hold->from_date);
                        $diffdays = round($diff / (60 * 60 * 24));

                        //Added 01102018 Log Member Membership
                        $log_member_status = LogMemberStatus::where('user_id', '=', $member->id)
                            ->whereDate('from_date', '=', Carbon::parse($member_status->from_date)->toDateString())
                            ->whereDate('thru_date', '=', Carbon::parse($member_status->thru_date)->toDateString())
                            ->first();

                        //Added 19122018
                        if (empty($log_member_status)):
                            $log_member_status = LogMemberStatus::where('user_id', '=', $member->id)
                                ->whereDate('thru_date', '=', Carbon::parse($member_status->thru_date)->toDateString())
                                ->orderBy('created_at', 'desc')
                                ->first();

                            if (empty($log_member_status)):
                                $log_member_status = LogMemberStatus::where('user_id', '=', $member->id)
                                    ->whereDate('thru_date', '<=', Carbon::parse($member_status->thru_date)->toDateString())
                                    ->orderBy('created_at', 'desc')
                                    ->first();
                            endif;
                        endif;

                        $update_log_memberstatus = LogMemberStatus::findOrFail($log_member_status->id);
                        $update_log_memberstatus->thru_date = Carbon::parse($log_member_status->thru_date)->addDays($diffdays);
                        //Added 01102018 Log Member Membership

                        $member_status->thru_date = Carbon::parse($member_status->thru_date)->addDays($diffdays);
                        $member_status->status = 'Hold';

                        $member->user_memberstatus()->save($member_status);
                        $update_log_memberstatus->save();
                    endif;
                endif;
            endforeach;

            Session::flash('cf_lite.flash_message', trans('messages.member_hold_request_has_been_activated'));
        endif;
        
        return redirect('cf-lite/member-hold/request/list');
    }

    public function hold_request_detail($id) {
        $member_hold = MemberHold::findOrFail($id);

        return view('cf_lite.member.member-hold-request-detail', compact('member_hold'));
    }

    public function update_member_hold_request($id, Request $request) {
        $this->validate($request, [
            'from_date' => 'required|date',
            'thru_date' => 'required|date',
            'member_hold_note' => 'sometimes',
        ]);

        $member_hold = MemberHold::findOrFail($id);

        if (($member_hold->status == 'Pending') || ($member_hold->status == 'OnHold')):
            if ($member_hold->status == 'Pending'):
                $member_hold->from_date = $request->from_date;
            elseif ($member_hold->status == 'OnHold'):
                $first_thru_date = $member_hold->thru_date;
            endif;

            $member_hold->thru_date = $request->thru_date;

            if (!empty($request->member_hold_note)):
                $member_hold->member_hold_note = $request->member_hold_note;
            endif;

            $member_hold->save();

            if ($member_hold->status == 'OnHold'):
                $member = User::findOrFail($member_hold->user_id);
                if (!empty($member->user_memberstatus)):
                    $member_status = $member->user_memberstatus;

                    //count total days hold
                    $first_diff = strtotime($first_thru_date) - strtotime($member_hold->from_date);
                    $first_diffdays = round($first_diff / (60 * 60 * 24));
                    $diff = strtotime($request->thru_date) - strtotime($member_hold->from_date);
                    $diffdays = round($diff / (60 * 60 * 24));
                    $real_diff = $diffdays - $first_diffdays;

                    $member_status->thru_date = Carbon::parse($member_status->thru_date)->addDays($real_diff);
                    $member->user_memberstatus()->save($member_status);
                endif;
            endif;

            Session::flash('cf_lite.flash_message', trans('messages.member_hold_request_has_been_updated'));
        else:
            Session::flash('cf_lite.flash_message', trans('messages.member_hold_could_not_be_update'));
        endif;

        return redirect('cf-lite/member-hold/request/'.$member_hold->id.'/edit');
    }

    public function member_hold_list() {
        $hold_list = MemberHold::where('club_id', '=', Auth::user()->club_id)
            ->whereDate('from_date', '<=', Carbon::now()->toDateString())
            ->whereDate('thru_date', '>=', Carbon::now()->toDateString())
            ->where('status', '=', 'OnHold')
            ->orderBy('thru_date', 'asc')
            ->paginate(50);

        return view('cf_lite.member.member-hold-list', compact('hold_list'));
    }

    public function finish_member_on_hold($id) {
        $faster = 0;
        $member_hold = MemberHold::findOrFail($id);
        $member_hold->status = 'Finished';

        //count total days hold
        $first_diff = strtotime($member_hold->thru_date) - strtotime($member_hold->from_date);
        $first_diffdays = round($first_diff / (60 * 60 * 24));
        $diff = strtotime(Carbon::now()) - strtotime($member_hold->from_date);
        $diffdays = round($diff / (60 * 60 * 24));

        if ($diffdays < $first_diffdays):
            $faster = 1;
            $member_hold->thru_date = Carbon::today();
        endif;

        $member_hold->save();

        //Change Member Status
        $member = User::findOrFail($member_hold->user_id);
        if (!empty($member->user_memberstatus)):
            $member_status = $member->user_memberstatus;
            $member_status->status = 'Active';

            if ($faster == '1'):
                $real_diff = $first_diffdays - $diffdays;
                $member_status->thru_date = Carbon::parse($member_status->thru_date)->subDays($real_diff);
            endif;

            $member->user_memberstatus()->save($member_status);
        endif;

        Session::flash('cf_lite.flash_message', trans('messages.member_hold_request_has_been_activated'));
        return redirect('cf-lite/member-hold/on-hold/list');
    }

    public function member_hold_finished_list() {
        $hold_finished_list = MemberHold::where('club_id', '=', Auth::user()->club_id)
            ->whereDate('thru_date', '<', Carbon::now()->toDateString())
            ->where('status', '=', 'OnHold')
            ->orderBy('thru_date', 'asc')
            ->paginate(50);

        return view('cf_lite.member.member-hold-finished-list', compact('hold_finished_list'));
    }

    public function member_hold_finished(FinishMemberHoldRequest $request) {
        foreach ($request->finished as $key => $value):
            if (!empty($value)):
                $member_hold = MemberHold::findOrFail($value);
                $member_hold->status = 'Finished';
                $member_hold->save();

                $member = User::findOrFail($member_hold->user_id);
                if (!empty($member->user_memberstatus)):
                    $member_status = $member->user_memberstatus;
                    $member_status->status = 'Active';
                    $member->user_memberstatus()->save($member_status);
                endif;
            endif;
        endforeach;

        Session::flash('cf_lite.flash_message', trans('messages.member_hold_list_has_been_updated'));
        return redirect('cf-lite/member-hold/finished/list');
    }
}
