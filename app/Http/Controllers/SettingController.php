<?php

namespace App\Http\Controllers;

use App\Model\Accounting\AccountingAccount;
use App\Model\Guest\CoachLeadSequence;
use App\Model\Guest\BuddyReferralLead;
use App\Model\Member\CoachMember;
use App\Model\Member\MemberPersonal;
use App\Model\Staff\CoachSequence;
use App\Model\Workout\Workout;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Session;
use App\Model\Club\ClubInfo;
use App\Model\MasterData\AccessMenu;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('wm-only');
		$this->middleware('coach_assign',['only'=>[
            'auto_assign_home',
            'generate_auto_assign_member',
            'edit_auto_assign',
            'update_auto_assign',
            'coach_sequence_home',
            'update_coach_sequence',
            'coach_lead_home',
            'edit_coach_lead',
        ]]);
    }

    public function setting_main() {
        $access_list = AccessMenu::all();

        return view('cf_lite.choose-setting', compact('access_list'));
    }

    public function other_setting_main(){
    	return view('cf_lite.choose-other-setting');
    }

    //Auto Assign Member
    public function auto_assign_home(Request $request) {
        $user = Auth::user();

        if (!empty($request->club)):
            $club = ClubInfo::findOrFail($request->club);
        else:
            $club = ClubInfo::findOrFail($user->club_id);
        endif;

        if ($user->user_club->club_jubilant->type == 'Franchise') {
            $clubs = ClubInfo::where('status', '=', 'Active')
                ->where('jubilant_id', '=', $user->user_club->jubilant_id)
                ->orderBy('club_name', 'asc')
                ->pluck('club_name', 'id');
        } else {
            $clubs = ClubInfo::where('status', '=', 'Active')
                ->orderBy('club_name', 'asc')
                ->pluck('club_name', 'id');
        }

        $staffs = User::join('member_personals', 'users.id', '=', 'member_personals.user_id')
            ->join('mst_roles', 'users.role_id', '=', 'mst_roles.id')
            ->leftJoin('coach_members', function ($leftjoin) {
                $leftjoin->on('coach_members.coach_id', '=', 'users.id');
            })
            ->join('users as u2', function($q){
                $q->on('coach_members.member_id','=','u2.id');
            })
            ->selectRaw('COUNT(coach_members.member_id) as total_member, users.name, users.id, users.role_id, mst_roles.role, member_personals.photo')
            ->where('users.club_id', '=', $club->id)
            ->where('users.role_id', '<>', '7')
            ->where('u2.club_id','=', $club->id)
            ->whereHas('user_memberstatus', function ($q) {
                $q->where('status', '=', 'Active');
            })
            ->groupBy('users.name', 'users.id', 'mst_roles.role', 'member_personals.photo')
            ->orderBy('users.role_id', 'asc')
            ->orderBy('users.name', 'asc')
            ->get();

        return view('cf_lite.staff.auto-assign-member', compact('staffs', 'clubs', 'club'));
    }

    public function generate_auto_assign_member(Request $request) {
        $this->validate($request, [
            'chosen_club' => 'required',
        ]);

        $staffs = $request->staff;
        $total_staff = count($staffs);

        if (!empty($staffs)):
            //Set Default minimal WO
//            $totalNeedA = 12;
//            $totalNeedB = 8;
//            $totalNeedC = 4;
//            $totalNeedD = 1;
//            $totalNeedE = 0;

            //Hitung jumlah aktif & hold member
//            $members = User::leftJoin('workouts', function ($leftjoin) {
//                $leftjoin->on('workouts.user_id', '=', 'users.id')
//                    ->whereDate('workouts.workout_date', '>=', Carbon::today()->subMonth()->toDateString())
//                    ->whereDate('workouts.workout_date', '<=', Carbon::today()->toDateString());
//                })
//                ->selectRaw('COUNT(workouts.id) as wo_count, users.id')
//                ->where('users.club_id', '=', $request->chosen_club)
//                ->where('users.role_id', '=', '7')
//                ->whereHas('user_memberstatus', function ($q) {
//                    $q->where('status', '=', 'Active')
//                        ->orWhere('status', '=', 'Hold');
//                })
//                ->groupBy('users.id')
//                ->get();

            $member_stopped = User::where('club_id', '=', $request->chosen_club)
                ->whereHas('user_memberstatus', function ($q) {
                    $q->where('status', '=', 'Inactive')->orWhere('status', '=', 'Stopped');
                })
                ->get();

            foreach ($member_stopped as $member) {
                $coach_member = DB::delete('DELETE FROM coach_members WHERE member_id = '.$member->id);
            }

            $members = User::where('club_id', '=', $request->chosen_club)
                ->where('role_id', '=', '7')
                ->whereHas('user_memberstatus', function ($q) {
                    $q->where('status', '=', 'Active')
                        ->orWhere('status', '=', 'Hold');
                })
                ->get();

            $total_member = count($members);

            //Tentukan Flag Member
            foreach ($members as $member):
//                if ($member->wo_count >= $totalNeedA):
//                    $member->setAttribute('flag', 'A');
//                elseif (($member->wo_count >= $totalNeedB) && ($member->wo_count < $totalNeedA)):
//                    $member->setAttribute('flag', 'B');
//                elseif (($member->wo_count >= $totalNeedC) && ($member->wo_count < $totalNeedB)):
//                    $member->setAttribute('flag', 'C');
//                elseif (($member->wo_count >= $totalNeedD) && ($member->wo_count < $totalNeedC)):
//                    $member->setAttribute('flag', 'D');
//                elseif ($member->wo_count >= $totalNeedE):
//                    $member->setAttribute('flag', 'E');
//                endif;
                $member->setAttribute('flag', $member->user_memberpersonal->flag);
            endforeach;

            //Hitung Total Member per Flag
            $member['A'] = count($members->where('flag', 'A'));
            $member['B'] = count($members->where('flag', 'B'));
            $member['C'] = count($members->where('flag', 'C'));
            $member['D'] = count($members->where('flag', 'D'));
            $member['E'] = count($members->where('flag', 'E'));

            //Cek apakah ada staff selain coach
            $not_coach = 0;
            foreach ($staffs as $key => $value):
                if ($value < 6):
                    $not_coach++;
                endif;
            endforeach;

            //Set total member/flag untuk setiap staff
            $alokasi['A'] = ceil($member['A'] / $total_staff);
            $alokasi['B'] = ceil($member['B'] / $total_staff);
            $alokasi['C'] = ceil($member['C'] / $total_staff);
            $alokasi['D'] = ceil($member['D'] / ($total_staff - $not_coach));
            $alokasi['E'] = ceil($member['E'] / ($total_staff - $not_coach));

            //Set alokasi member per staff
            $type = 1; //Tipe Normal, member utk setiap coach + TL di bawah 20 orang
            if (($alokasi['A'] + $alokasi['B'] + $alokasi['C']) > 20):
                $type = 2; //Staff selain coach maksimal mengelola 20 member
                $alokasiTL['A'] = $alokasi['A'];
                $alokasiTL['B'] = $alokasi['B'];
                $alokasiTL['C'] = 20 - $alokasi['A'] + $alokasi['B'];

                if (($alokasiTL['A'] + $alokasiTL['B']) > 20):
                    $alokasiTL['B'] = 20 - $alokasiTL['A'];
                    $alokasiTL['C'] = 0;
                endif;

                if ($alokasiTL['A'] > 20):
                    $alokasiTL['A'] = 20;
                    $alokasiTL['B'] = 0;
                    $alokasiTL['C'] = 0;
                endif;
            endif;


            $check = '';
            if ($type == 1):
                for ($i = 'A'; $i <= 'E'; $i++):
                    $start = 0;
                    foreach ($staffs as $staff_key => $staff_value):
                        $gate = 1;
                        if (($i == 'D') || ($i == 'E')):
                            if ($staff_value < 6):
                                $gate = 0;
                            endif;
                        endif;

                        if ($gate == 1):
                            $flag_counter = 0;
                            $flag_alokasi = 0;
                            foreach ($members->where('flag', $i) as $membere):
                                if ($start < $member[$i]):
                                    if (($flag_counter == $start) && ($flag_alokasi < $alokasi[$i])):
                                        //Tambahkan Staff-Member ke Tabel
                                        $the_member = User::findOrFail($membere->id);

                                        if (!empty($the_member->memberuser_coachmember)):
                                            $coach_member = $the_member->memberuser_coachmember;
                                        else:
                                            $coach_member = new CoachMember;
                                        endif;

                                        $coach_member->coach_id = $staff_key;
                                        $the_member->memberuser_coachmember()->save($coach_member);

                                        $start++;
                                        $flag_counter++;
                                        $flag_alokasi++;
                                    else:
                                        $flag_counter++;
                                    endif;
                                endif;
                            endforeach;
                        endif;
                    endforeach;
                endfor;
            elseif ($type == 2):
                for ($i = 'A'; $i <= 'E'; $i++):
                    $start = 0;
                    foreach ($staffs as $staff_key => $staff_value):
                        $gate = 1;
                        if (($i == 'D') || ($i == 'E')):
                            if ($staff_value != 6):
                                $gate = 0;
                            endif;
                        endif;

                        if ($gate == 1):
                            $flag_counter = 0;
                            $flag_alokasi = 0;
                            foreach ($members->where('flag', $i) as $membere):
                                if ($start < $member[$i]):
                                    if ($staff_value != 6):
                                        if (($flag_counter == $start) && ($flag_alokasi < $alokasiTL[$i])):
                                            //Tambahkan Staff-Member ke Tabel
                                            $the_member = User::findOrFail($membere->id);

                                            if (!empty($the_member->memberuser_coachmember)):
                                                $coach_member = $the_member->memberuser_coachmember;
                                            else:
                                                $coach_member = new CoachMember;
                                            endif;

                                            $coach_member->coach_id = $staff_key;
                                            $the_member->memberuser_coachmember()->save($coach_member);

                                            $start++;
                                            $flag_counter++;
                                            $flag_alokasi++;
                                        else:
                                            $flag_counter++;
                                        endif;
                                    else:
                                        if (($flag_counter == $start) && ($flag_alokasi < $alokasi[$i])):
                                            //Tambahkan Staff-Member ke Tabel
                                            $the_member = User::findOrFail($membere->id);

                                            if (!empty($the_member->memberuser_coachmember)):
                                                $coach_member = $the_member->memberuser_coachmember;
                                            else:
                                                $coach_member = new CoachMember;
                                            endif;

                                            $coach_member->coach_id = $staff_key;
                                            $the_member->memberuser_coachmember()->save($coach_member);

                                            $start++;
                                            $flag_counter++;
                                            $flag_alokasi++;
                                        else:
                                            $flag_counter++;
                                        endif;
                                    endif;
                                endif;
                            endforeach;
                        endif;
                    endforeach;
                endfor;
            endif;

            Session::flash('flash_message', trans('messages.auto_assign_succeed'));
            return redirect('cf-lite/auto-assign-member');
        else:
            Session::flash('flash_message', trans('messages.nothing_has_been_chosen'));
            return redirect('cf-lite/auto-assign-member');
        endif;
    }

    public function edit_auto_assign($id) {
        $staff = User::findOrFail($id);

        $othermembers = User::where('role_id', '=', '7')
            ->where('club_id', '=', $staff->club_id)
            ->whereHas('user_memberstatus', function ($q) {
                $q->where('status', '=', 'Active')
                    ->orWhere('status', '=', 'Hold');
            })
            ->whereHas('memberuser_coachmember', function ($r) use ($staff) {
                $r->where('coach_id', '<>', $staff->id);
            })
            ->get();

        $members = MemberPersonal::whereHas('memberpersonal_user', function ($q) use ($staff) {
            $q->whereHas('memberuser_coachmember', function ($r) use ($staff) {
                $r->where('coach_id', '=', $staff->id);
            });
        })->get();

//        $members = User::where('role_id', '=', '7')
//            ->whereHas('memberuser_coachmember', function ($q) use ($staff) {
//                $q->where('coach_id', '=', $staff->id);
//            })
//            ->get();

        return view('cf_lite.staff.assigned-member-detail', compact('staff', 'members', 'othermembers'));
    }

    public function update_auto_assign($id, Request $request) {
        $staff = User::findOrFail($id);

        if (!empty($request->member)):
            foreach ($request->member as $key => $value):
                $member = User::findOrFail($value);

                if (!empty($member->memberuser_coachmember)):
                    $coachmember = $member->memberuser_coachmember;
                else:
                    $coachmember = new CoachMember;
                endif;

                $coachmember->coach_id = $staff->id;
                $member->memberuser_coachmember()->save($coachmember);
            endforeach;

            Session::flash('flash_message', trans('messages.assigning_member_succeed'));
        else:
            Session::flash('flash_message', trans('messages.nothing_has_been_assigned'));
        endif;

        return redirect('cf-lite/auto-assign-member/'.$staff->id);
    }

    //Coach Sequence Setting
    public function coach_sequence_home(Request $request) {
        $user = Auth::user();

        if (!empty($request->club)):
            $club = ClubInfo::findOrFail($request->club);
        else:
            $club = ClubInfo::findOrFail($user->club_id);
        endif;

        if ($user->user_club->club_jubilant->type == 'Franchise') {
            $clubs = ClubInfo::where('status', '=', 'Active')
                ->where('jubilant_id', '=', $user->user_club->jubilant_id)
                ->orderBy('club_name', 'asc')
                ->pluck('club_name', 'id');
        } else {
            $clubs = ClubInfo::where('status', '=', 'Active')
                ->orderBy('club_name', 'asc')
                ->pluck('club_name', 'id');
        }

        $staffs = User::where('users.role_id', '=', '6')
            ->where('users.club_id', '=', $club->id)
            ->whereHas('user_memberstatus', function ($q) {
                $q->where('status', '=', 'Active');
            })
            ->leftJoin('coach_sequences', function ($leftjoin) {
                $leftjoin->on('coach_sequences.coach_id', '=', 'users.id');
            })
            ->join('member_personals', 'member_personals.user_id', '=', 'users.id')
            ->join('mst_roles', 'mst_roles.id', '=', 'users.role_id')
            ->selectRaw('users.id, users.name, mst_roles.role, coach_sequences.sequence, member_personals.photo')
            ->groupBy('users.id', 'users.name', 'mst_roles.role', 'member_personals.photo', 'coach_sequences.sequence')
            ->orderBy('coach_sequences.sequence', 'asc')
            ->get();

        return view('cf_lite.staff.coach-sequence', compact('staffs', 'clubs', 'club'));
    }

    public function update_coach_sequence(Request $request) {
        $this->validate($request, [
            'chosen_club' => 'required',
        ]);

        $seq = $request->seq;

        if (!empty($request->staff)):
            //Update coach sequence
            foreach ($request->staff as $key => $value):
                $coach = User::findOrFail($value);

                if (!empty($coach->user_coachsequence)):
                    $coach_sequence = $coach->user_coachsequence;
                else:
                    $coach_sequence = new CoachSequence;
                endif;
                $coach_sequence->club_id = $request->chosen_club;
                $coach_sequence->sequence = $seq[$key];
                $coach->user_coachsequence()->save($coach_sequence);
            endforeach;

            //remove coach sequence
            if (!empty($request->remove)):
                foreach ($request->remove as $rkey => $rval):
                    if ($rval == 'true'):
                        $rcoach = User::findOrFail($rkey);
                        $rcoach_sequence = $rcoach->user_coachsequence;
                        $rcoach_sequence->delete();
                    endif;
                endforeach;
            endif;

            Session::flash('flash_message', trans('messages.coach_sequence_has_been_successfully_updated'));
        else:
            Session::flash('flash_message', trans('messages.nothing_has_been_updated'));
        endif;

        return redirect('cf-lite/coach-sequence?club='.$request->chosen_club);
    }

    public function coach_lead_home(Request $request){
        $user = Auth::user();

        if (!empty($request->club)):
            $club = ClubInfo::findOrFail($request->club);
        else:
            $club = ClubInfo::findOrFail($user->club_id);
        endif;

        if ($user->user_club->club_jubilant->type == 'Franchise') {
            $clubs = ClubInfo::where('status', '=', 'Active')
                ->where('jubilant_id', '=', $user->user_club->jubilant_id)
                ->orderBy('club_name', 'asc')
                ->pluck('club_name', 'id');
        } else {
            $clubs = ClubInfo::where('status', '=', 'Active')
                ->orderBy('club_name', 'asc')
                ->pluck('club_name', 'id');
        }

        $staffs = User::join('member_personals', 'users.id', '=', 'member_personals.user_id')
            ->join('mst_roles', 'users.role_id', '=', 'mst_roles.id')
            ->selectRaw('(SELECT count(id)
               FROM buddy_referral_leads
               WHERE staff_id = users.id AND status_gm = \'Guest\') as total_guest, users.name, users.id, users.role_id, mst_roles.role, member_personals.photo')
            ->where('users.role_id', '<>', '7')
            ->where('users.role_id', '<>', '1')
            ->where('users.club_id', '=', $club->id)
            ->whereHas('user_memberstatus', function ($join){
                $join->where('status', '=', 'Active');
            })
            ->groupBy('users.name', 'users.id', 'mst_roles.role', 'member_personals.photo')
            ->orderBy('users.role_id', 'asc')
            ->orderBy('users.name', 'asc')
            ->get();

        return view('cf_lite.coach-lead.coach-lead-setting', compact('staffs', 'club', 'clubs'));
    }

    public function edit_coach_lead($id){
        $staff = User::findOrFail($id);

        $guests = BuddyReferralLead::where('staff_id', '=', $id)
            ->where('status_gm', '=', 'Guest')
            ->get();

        $other_guests = BuddyReferralLead::where('staff_id', '<>', $id)
            ->where('status_gm', '=', 'Guest')
            ->where('club_id', '=', $staff->club_id)
            ->get();

        return view('cf_lite.coach-lead.assigned-coach-detail', compact('staff', 'guests', 'other_guests'));
    }

    public function update_coach_lead($id, Request $request){
        $staff = User::findOrFail($id);

        if (!empty($request->member)):
            foreach ($request->member as $key => $value):
                $member = BuddyReferralLead::findOrFail($value);

                if (!empty($member)):
                    $member->staff_id = $staff->id;
                    $member->save();
                endif;
            endforeach;

            Session::flash('flash_message', trans('messages.assigning_lead_succeed'));
        else:
            Session::flash('flash_message', trans('messages.nothing_has_been_assigned'));
        endif;

        return redirect('cf-lite/coach-lead-setting/'.$staff->id);
    }

    public function coach_lead_sequence_home(Request $request){
        $user = Auth::user();
        if (!empty($request->club)):
            $club = ClubInfo::findOrFail($request->club);
        else:
            $club = ClubInfo::findOrFail($user->club_id);
        endif;

        if ($user->user_club->club_jubilant->type == 'Franchise') {
            $clubs = ClubInfo::where('status', '=', 'Active')
                ->where('jubilant_id', '=', $user->user_club->jubilant_id)
                ->orderBy('club_name', 'asc')
                ->pluck('club_name', 'id');
        } else {
            $clubs = ClubInfo::where('status', '=', 'Active')
                ->orderBy('club_name', 'asc')
                ->pluck('club_name', 'id');
        }

        $staffs = User::join('member_personals', 'users.id', '=', 'member_personals.user_id')
            ->join('mst_roles', 'users.role_id', '=', 'mst_roles.id')
            ->leftJoin('coach_lead_sequences', function ($leftjoin) {
                $leftjoin->on('coach_lead_sequences.coach_lead_id', '=', 'users.id');
            })
            ->selectRaw('users.id, users.name, users.role_id, mst_roles.role, member_personals.photo, coach_lead_sequences.sequence')
            ->where('users.role_id', '>', '4')
            ->where('users.role_id', '<', '7')
            ->where('users.club_id', '=', $club->id)
            ->whereHas('user_memberstatus', function ($join){
                $join->where('status', '=', 'Active');
            })
            ->groupBy('users.name', 'users.id', 'mst_roles.role', 'member_personals.photo', 'coach_lead_sequences.sequence')
            ->orderBy('users.role_id', 'asc')
            ->orderBy('users.name', 'asc')
            ->orderBy('coach_lead_sequences.sequence', 'asc')
            ->get();

        return view('cf_lite.coach-lead.coach-lead-sequence', compact('club', 'clubs', 'staffs'));
    }

    public function update_coach_lead_sequence(Request $request){
        $this->validate($request, [
            'chosen_club' => 'required',
        ]);

        $seq = $request->seq;

        if (!empty($request->staff)):
            //Update coach lead sequence
            foreach ($request->staff as $key => $value):
                $coachlead = User::findOrFail($value);

                if (!empty($coachlead->user_coachleadsequence)):
                    $coach_lead_sequence = $coachlead->user_coachleadsequence;
                else:
                    $coach_lead_sequence = new CoachLeadSequence;
                endif;
                $coach_lead_sequence->club_id = $request->chosen_club;
                $coach_lead_sequence->sequence = $seq[$key];
                $coachlead->user_coachleadsequence()->save($coach_lead_sequence);
            endforeach;

            //remove coach sequence
            if (!empty($request->remove)):
                foreach ($request->remove as $rkey => $rval):
                    if ($rval == 'true'):
                        $rcoach_lead = User::findOrFail($rkey);
                        $rcoach_lead_sequence = $rcoach_lead->user_coachleadsequence;
                        $rcoach_lead_sequence->delete();
                    endif;
                endforeach;
            endif;

            Session::flash('flash_message', trans('messages.coach_lead_sequence_has_been_successfully_updated'));
        else:
            Session::flash('flash_message', trans('messages.nothing_has_been_updated'));
        endif;

        return redirect('cf-lite/coach-lead-sequence?club='.$request->chosen_club);
    }

    public function setting_accounting_account () {
        $accounts = AccountingAccount::get();
        return view('cf_lite.jubilant.accounting-account', compact('accounts'));
    }


}
