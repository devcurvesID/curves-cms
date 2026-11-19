<?php

namespace App\Http\Controllers;

use App\Http\Requests\FirstThirtyDaysRequest;
use Session;
use App\Model\Club\ClubInfo;
use App\Model\MasterData\ThirtyDay;
use App\Model\Member\CoachMember;
use App\Model\Member\MemberThirtyDay;
use App\Model\Member\StaffReport;
use App\Model\WM\Xfactor;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('wm-only');
    }

    public function staff_report_choose_member(Request $request) {
        $club = ClubInfo::findOrFail(Auth::user()->club_id);

        $query = User::where('club_id', '=', $club->id)
            ->where('role_id', '=', '7');

        if (!empty($request->s)):
            $search = $request->s;

            if ($request->search_option == 'name'):
                $query->where('name', 'LIKE', '%'.ucwords($search).'%');
            elseif ($request->search_option == 'keytag'):
                $query->whereHas('user_memberpersonal', function ($q) use ($search) {
                    $q->where('key_tag_id', 'LIKE', '%'.$search.'%');
                });
            endif;
        endif;

        // Cek Status
        $status = $request->status ?? 'Active';

        if($status == 'Semua'){

        }else{
            $query->whereHas('user_memberstatus', function($q) use ($status){
                $q->where('status','=',$status);
            });
        }

        $members = $query->orderBy('name', 'asc')->paginate(100);

        return view('cf_lite.document.staff-report.staff-report-member-list', compact('members', 'club','status'));
    }

    public function staff_report_member_detail($id) {
        $member = User::findOrFail($id);

        $member_thirty_days = MemberThirtyDay::where('user_id', '=', $member->id)
            ->whereDate('created_at', '>=', Carbon::parse($member->user_memberpersonal->joined)->toDateString())
            ->orderBy('created_at', 'asc')
            ->get();

        $member_reports = StaffReport::where('member_id', '=', $member->id)
            ->whereDate('contact_date', '>=', Carbon::parse($member->user_memberstatus->from_date)->toDateString())
            ->orderBy('contact_date', 'asc')
            ->get();

        $xfactors = Xfactor::where('user_id', '=', $member->id)
            ->whereDate('created_at', '>=', Carbon::parse($member->user_memberstatus->from_date)->toDateString())
            ->get();

        $thirty_days = ThirtyDay::orderBy('sequence', 'asc')->get();

        $staff_list = User::where('role_id', '<>', '7')
            ->where('club_id', '=', $member->club_id)
            ->orderBy('name', 'asc')
            ->pluck('name', 'id');

        $coach_member = CoachMember::where('member_id',$id)->first();

        return view('cf_lite.document.staff-report.staff-report-member-detail', compact('member', 'member_thirty_days', 'member_reports', 'xfactors', 'thirty_days', 'staff_list','coach_member'));
    }

    public function update_staff_report($id, Request $request) {
        $this->validate($request, [
            'report_attribute' => 'sometimes',
            'the_date' => 'required|date',
            'the_note' => 'sometimes',
            'the_report' => 'required|string',
            'the_staff' => 'required',
        ]);

        $member = User::findOrFail($id);

        if (!empty($request->report_attribute)):
            Session::flash('flash_message', trans('messages.staff_report_update_message'));
            $report = StaffReport::findOrFail($request->report_attribute);
        else:
            Session::flash('flash_message', trans('messages.staff_report_added_message'));
            $report = new StaffReport;
            $report->member_id = $member->id;
        endif;

        $report->staff_id = $request->the_staff;
        $report->report = $request->the_report;

        if (!empty($request->the_note)):
            $report->note = $request->the_note;
        endif;

        $report->contact_date = $request->the_date;
        $report->save();

        return redirect('cf-lite/staff-report/'.$member->id);
    }

    public function store_thirty_days($id, FirstThirtyDaysRequest $request) {
        $member = User::findOrFail($id);

        $results = $request->thirty_result;
        foreach ($request->thirty_box as $key => $value):
            if (!empty($value)):
                $member_thirty_days = new MemberThirtyDay;

                $member_thirty_days->user_id = $member->id;
                $member_thirty_days->thirty_days_id = $value;
                $member_thirty_days->result = $results[$key];
                $member_thirty_days->staff_id = $request->thirty_staff;
                $member_thirty_days->save();

                Session::flash('flash_message', trans('messages.member_thirty_days_added_message'));
            endif;
        endforeach;

        return redirect('cf-lite/staff-report/'.$member->id);
    }

    public function update_first_thirty_days($id, Request $request) {
        $this->validate($request, [
            'thirty_days_attribute' => 'required',
            'the_result' => 'required|string',
            'the_thirty_staff' => 'required',
        ]);

        $member = User::findOrFail($id);
        $member_thirty = MemberThirtyDay::findOrFail($request->thirty_days_attribute);

        $member_thirty->result = $request->the_result;
        $member_thirty->staff_id = $request->the_thirty_staff;
        $member_thirty->save();

        Session::flash('flash_message', trans('messages.member_thirty_days_updated_message'));
        return redirect('cf-lite/staff-report/'.$member->id);
    }
}
