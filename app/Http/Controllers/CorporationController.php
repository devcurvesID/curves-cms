<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Corporation\Corporation;
use App\Model\Corporation\MemberCorporation;
use App\Model\Appointment\GuestAppointment;
use App\Model\Guest\BuddyReferralLead;
use App\User;
use App\AutoNumber;
use Session;
use DB;

class CorporationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('wm-only');
    }

    public function corporation_options(){
    	return view('cf_lite/corporate/corporation-options');
    }

    public function corporation_list() {
        $corporations = Corporation::pluck('name','id');

        // OLD WHERE MEMBER WHO HAVEN'T PAID CAN BE ADDED TO CORPORATE
        // $guest_appointments = GuestAppointment::whereHas('guestappointment_gastatus', function($q) {
        //         $q->whereNotNull('c_date');
        //     })->whereHas('guestappointment_buddyreferrallead',function($r){
        //     	$r->whereDoesntHave('user_membercorporation');
        //     })->where('guest_status_after', '<>', 'Member')
        //       ->paginate(100);

        //NEW
        $users = User::whereHas('user_memberstatus', function($q) {
                $q->whereNotNull('membership_type_id')
                  ->whereHas('memberstatus_membershiptype',function($r){
                        $r->where('membership_type' ,'=','Corporate');
                  });
            })->whereNotIn('id',MemberCorporation::selectRaw('user_id')->get())
            ->paginate(100);

        return view('cf_lite/corporate/corporation-list', compact('corporations','users'));
    }

    public function store_corporation(Request $request){
        $this->validate($request, [
            'name' => 'required|string',
            'address' => 'required|string',
            'phone_number' => 'required|string',
            'email' => 'required|email',
        ]);

    	$corporation = new Corporation;
        $corporation->name = $request->name;
        $corporation->address = $request->address;
        $corporation->phone_number = $request->phone_number;
        $corporation->email = $request->email;
        $corporation->save();

        Session::flash('flash_message', trans('messages.corporation_create_message'));

        return redirect('cf-lite/corporate/corporation-options');
    }

    public function store_bulk_member_corporation(Request $request){

        $this->validate($request, [
            'group_name' => 'required_if:group_select,null',
            'group_select'=>'required_if:group_name,null',
            'corporate' => 'required|min:1'
        ]);

    	$input = $request->all();
    	$corp = $request->corporation;
        $corporates = $request->corporate;
        $auto_number = AutoNumber::findOrFail('2');
        if(($request->group_select == null && !empty($request->group_name)) || ($request->group_select != null && !empty($request->group_name))){
            $group_name = $request->group_name;
            foreach ($corporates as $key => $value) {
                if(!empty($value)){
                    $member_corporation = new MemberCorporation();
                    $member_corporation->corporation_id = $corp;
                    $member_corporation->user_id = $value;
                    $member_corporation->group_name = $group_name;
                    $member_corporation->sequence_group = $auto_number->current_number;
                    $member_corporation->save();
                }
            }
            $auto_number->current_number = $auto_number->current_number +1;
            $auto_number->save();
        }else if($request->group_select != null && empty($request->group_name)){
            $group_name = $request->group_select;
            $memberCorp = MemberCorporation::where('sequence_group','=',$request->group_select)->first();
            foreach ($corporates as $key => $value) {
                if(!empty($value)){
                    $member_corporation = new MemberCorporation();
                    $member_corporation->corporation_id = $corp;
                    $member_corporation->user_id = $value;
                    $member_corporation->group_name = $memberCorp->group_name;
                    $member_corporation->sequence_group = $memberCorp->sequence_group;
                    $member_corporation->save();
                }
            }
        }    	

        Session::flash('flash_message', trans('messages.corporation_create_message'));

        return redirect('cf-lite/corporate/corporation-options');
    }

    public function corporate_group_ajax(Request $request){

        // $corporations = MemberCorporation::where('corporation_id','=',$request->id)->get();
        // $corporations = DB::table('member_corporations')->select('sequence_group','group_name',DB::raw('count(*) as total'))->groupBy('group_name')->having('corporation_id','=',$request->id)->get();
        $corporations = MemberCorporation::selectRaw('sequence_group , group_name')->where('corporation_id','=',$request->id)->groupBy('sequence_group','group_name')->get();

        return response()->json(array('corporations' => $corporations));
    }

    //MASTER CORPORATE DATA
    public function mst_corporate_list(){
        $corporates = Corporation::paginate(50);

        return view('cf_lite/corporate/corporation-mst-list',compact('corporates'));
    }

    public function corporation_list_filter(Request $request){
        $corporates = Corporation::where('name','like','%'.$request->corporate_name.'%')->paginate(50);

        return view('cf_lite/corporate/corporation-mst-list',compact('corporates'));
    }

    public function store_new_corporation(Request $request){
        $this->validate($request, [
            'name' => 'required|string',
            'address' => 'required|string',
            'phone_number' => 'required|string',
            'email' => 'required|email',
        ]);
        if($request->addoredit == 'add'){
            $corporation = new Corporation;
            $corporation->name = $request->name;
            $corporation->address = $request->address;
            $corporation->phone_number = $request->phone_number;
            $corporation->email = $request->email;
            $corporation->save();

            Session::flash('flash_message', trans('messages.corporation_create_message'));
        }else{
            $corporation = Corporation::findOrFail($request->editid);
            $corporation->name = $request->name;
            $corporation->address = $request->address;
            $corporation->phone_number = $request->phone_number;
            $corporation->email = $request->email;
            $corporation->save();

            Session::flash('flash_message', trans('messages.corporation_update_message'));

        }

        return redirect('cf-lite/corporate/corporate-mst-list');
    }

    public function mst_corporate_detail($id){
        $groups = MemberCorporation::select('group_name','sequence_group')->groupBy('group_name','sequence_group')->where('corporation_id','=',$id)->paginate(12);
        $membercorps = MemberCorporation::get();

        return view('cf_lite/corporate/corporation-mst-detail-list',compact('groups','membercorps'));
    }

    public function corporate_group_list_ajax(Request $request){
        $corporate = Corporation::findOrFail($request->corporate_id);

        return response()->json(array('corporate' => $corporate));
    }

    public function delete_member_corporation(Request $request){
        $member_corporation = MemberCorporation::where('user_id','=',$request->id_mc)->first();
        $member_corporation->delete();

        Session::flash('flash_message', trans('messages.member_corporation_delete_message'));

        return redirect('cf-lite/corporate/corporate-mst-list');
    }
}
