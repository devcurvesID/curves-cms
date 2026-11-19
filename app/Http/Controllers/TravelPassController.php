<?php

namespace App\Http\Controllers;

use App\Model\Member\TravelPass;
use App\Model\Member\VisitingMember;
use App\Model\Member\VisitingMemberWorkout;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Session;

class TravelPassController extends Controller
{
    public function travel_pass_main(){
        return view('cf_lite.member.travel-pass-main');
    }

    public function travel_pass_new_request(){
        return view('cf_lite.member.travel-pass-new-request');
    }

    public function store_new_travel_pass(Request $request){
        $this->validate($request, [
            'member_id' => 'required',
            'from_date' => 'required|date',
        ]);

        $fromdate = $request->from_date;
        $thrudate =Carbon::parse($fromdate)->addDays(30);

        // $checks = TravelPass::where('user_id','=',$request->member_id)->whereDate('from_date','<=',$request->from_date)->whereDate('thru_date','>=',$request->from_date)->where('status','=','Active')->get();
        $checks = TravelPass::where('user_id','=',$request->member_id)->where(function($q) use($fromdate){
            $q->whereDate('from_date','<=',$fromdate);
            $q->whereDate('thru_date','>=',$fromdate);
        })->orWhere(function($r) use($thrudate){
            $r->whereDate('from_date','<=',$thrudate);
            $r->whereDate('thru_date','>=',$thrudate);
        })->where('status','=','Active')->get();

        if(count($checks) > 0){
            Session::flash('flash_message',trans('messages.travel_pass_for_this_member_exist'));
        }else{

            $travel = new TravelPass;
            $travel->user_id = $request->member_id;
            $travel->from_date = $request->from_date;
            $travel->thru_date = Carbon::parse($travel->from_date)->addDays(30);
            if(!empty($request->travel_pass_note)){
                $travel->notes = $request->travel_pass_note;
            }
            $travel->status = 'Active';
            $travel->save();

            Session::flash('flash_message',trans('messages.travel_pass_added'));
        }

        return redirect('cf-lite/travel-pass-new-request');
    }

    public function travel_pass_active_list(){
        $travels = TravelPass::where('status','=','Active')->whereHas('travelpass_user',function($q){
            $q->where('club_id','=',Auth::user()->club_id);
        })->orderBy('thru_date','asc')->get();

        return view('cf_lite.member.travel-pass-active-member-list',compact('travels'));
    }

    public function delete_travel_pass($id, Request $request){
        $travel = TravelPass::findOrFail($id);
        if(!empty($travel)){
            $travel->delete();
        }

        Session::flash('flash_message',trans('messages.travel_pass_deleted'));

        return redirect('cf-lite/travel-pass-active-list');
    }

    public function update_travel_pass(Request $request){
        $this->validate($request, [
            'from_date' => 'required|date',
            'thru_date' => 'required|date',
            'active_from_date' => 'required|date|after_or_equal:from_date',
            'active_thru_date' => 'required|date|after_or_equal:active_from_date|before_or_equal:thru_date',
        ]);

        $travel = TravelPass::findOrFail($request->editid);
        if(!empty($travel)){
            $travel->active_from_date = $request->active_from_date;
            $travel->active_thru_date = $request->active_thru_date;
            $travel->notes = $request->notes;
            $travel->status = 'Finished';
            $travel->save();
        }

        Session::flash('flash_message',trans('messages.travel_pass_updated'));

        return redirect('cf-lite/manual-login/'.$request->member_id);
    }

    public function travel_pass_finished_list(){
        $travels = TravelPass::where('status','=','Finished')->whereHas('travelpass_user',function($q){
            $q->where('club_id','=',Auth::user()->club_id);
        })->orderBy('thru_date','asc')->get();

        return view('cf_lite.member.travel-pass-finished-member-list',compact('travels'));
    }

    //Visiting Member
    public function visiting_member_list(){
        $members = VisitingMember::where('validity_date','>=',Carbon::today())->paginate(100);

        return view('cf_lite.member.visiting-member-active-list',compact('members'));
    }

    public function visiting_member_filter(Request $request) {
        $members = VisitingMember::where('validity_date','>=',Carbon::today())->whereNotNull('member_name');
        if (!empty($request->search_text)){
            if ($request->filter == 'member_name'){
                $members->where('member_name', 'LIKE', '%'.$request->search_text.'%');
            }else if ($request->filter == 'member_id'){
                $members->where('member_id', 'LIKE', '%'.$request->search_text.'%');
            }else if ($request->filter == 'origin_club'){
                $members->where('origin_club', 'LIKE', '%'.$request->search_text.'%');
            }
        }

        $members = $members->orderBy('id', 'asc')->paginate(100);

        return view('cf_lite.member.visiting-member-active-list', compact('members'));
    }

    public function store_new_visiting_member(Request $request){
        $this->validate($request,[
            'member_name' => 'required',
            'member_id' => 'required',
            'origin_club' => 'required',
            'first_use_date' => 'required|before_or_equal:validity_date',
            'validity_date' => 'required',
        ]);

        if($request->addoredit == 'add'){

            $member = new VisitingMember;
            $member->member_name = $request->member_name;
            $member->member_id = $request->member_id;
            $member->origin_club = $request->origin_club;
            if(!empty($request->origin_club_address)){
                $member->origin_club_address = $request->origin_club_address;
            }
            if(!empty($request->origin_club_phone_number)){
                $member->origin_club_phone_number = $request->origin_club_phone_number;
            }
            if(!empty($request->origin_club_fax_number)){
                $member->origin_club_fax_number = $request->origin_club_fax_number;
            }
            if(!empty($request->origin_club_email)){
                $member->origin_club_email = $request->origin_club_email;
            }
            $member->first_use_date = $request->first_use_date;
            $member->validity_date = $request->validity_date;
            $member->save();
        }else if($request->addoredit == 'edit'){

            $member = VisitingMember::findOrFail($request->editid);
            $member->member_name = $request->member_name;
            $member->member_id = $request->member_id;
            $member->origin_club = $request->origin_club;
            if(!empty($request->origin_club_address)){
                $member->origin_club_address = $request->origin_club_address;
            }
            if(!empty($request->origin_club_phone_number)){
                $member->origin_club_phone_number = $request->origin_club_phone_number;
            }
            if(!empty($request->origin_club_fax_number)){
                $member->origin_club_fax_number = $request->origin_club_fax_number;
            }
            if(!empty($request->origin_club_email)){
                $member->origin_club_email = $request->origin_club_email;
            }
            $member->first_use_date = $request->first_use_date;
            $member->validity_date = $request->validity_date;
            $member->save();
        }

        return redirect('cf-lite/visiting-member');
    }

    public function past_wo_vm($id){
        $visiting_member = VisitingMember::findOrFail($id);
        $date = Carbon::now();

        $userWo = VisitingMemberWorkout::where('visiting_member_id','=',$id)->get();

        return view('cf_lite.member.visiting-member-workout', compact('visiting_member','date','userWo'));
    }

    public function store_past_wo_vm(Request $request){
        // Add to Workout
        $dates = $request->date_chosen;
        $selDate = explode(" ", $dates);

        $visitingmember = VisitingMember::findOrFail($request->id);
        foreach($selDate as $date){
            $the_status = 0;
            foreach ($visitingmember->visitingmember_visitingmemberworkout as $workout):
                if (Carbon::parse($workout->workout_date)->format('d-m-Y') == Carbon::parse($date)->format('d-m-Y')):
                    $the_status = 1;
                endif;
            endforeach;

            if ($the_status == 0):
                $wo = new VisitingMemberWorkout;
                $wo->visiting_member_id = $visitingmember->id;
                $wo->workout_date = Carbon::parse($date);
                $wo->club_id = Auth::user()->club_id;
                $wo->save();
            endif;
        }

        //Delete from Workout
        $deldates = $request->date_chosen_delete;
        $selDelDate = explode(" ", $deldates);

        foreach($selDelDate as $date){
            $the_status = 0;
            foreach ($visitingmember->visitingmember_visitingmemberworkout as $workout):
                if (Carbon::parse($workout->workout_date)->format('d-m-Y') == Carbon::parse($date)->format('d-m-Y')):
                    $the_status = $workout->id;
                endif;
            endforeach;

            if ($the_status != 0):
                $wo = Workout::findOrFail($the_status);
                $wo->delete();
            endif;
        }

        return redirect('cf-lite/visiting-member');
    }

    public function visiting_member_inactive_list(){
        $members = VisitingMember::where('validity_date','<',Carbon::today())->paginate(100);

        return view('cf_lite.member.visiting-member-inactive-list',compact('members'));
    }

    public function visiting_member_inactive_filter(Request $request) {
        $members = VisitingMember::where('validity_date','<',Carbon::today())->whereNotNull('member_name');
        if (!empty($request->search_text)){
            if ($request->filter == 'member_name'){
                $members->where('member_name', 'LIKE', '%'.$request->search_text.'%');
            }else if ($request->filter == 'member_id'){
                $members->where('member_id', 'LIKE', '%'.$request->search_text.'%');
            }else if ($request->filter == 'origin_club'){
                $members->where('origin_club', 'LIKE', '%'.$request->search_text.'%');
            }
        }

        $members = $members->orderBy('id', 'asc')->paginate(100);

        return view('cf_lite.member.visiting-member-inactive-list', compact('members'));
    }
}
