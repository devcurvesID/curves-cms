<?php

namespace App\Http\Controllers;

use App\Model\Appointment\GuestAppointment;
use App\Model\Appointment\MemberAppointment;
use App\Model\Curves\CurvesLearning\ClCategory;
use App\Model\Curves\CurvesLearning\ClMaterialHeader;
use App\Model\Guest\BuddyReferralLead;
use App\Model\Locker\MemberLocker;
use App\Model\MasterData\Certification;
use App\Model\MasterData\MemberSource;
use App\Model\MasterData\PredefinedMessage;
use App\Model\Member\MemberThirtyDay;
use App\Model\Member\StaffReport;
use App\Model\Staff\TransferHistory;
use App\Model\Voucher\Voucher;
use App\Model\Workout\Workout;
use App\Model\Event\GlobalEvent;
use App\Model\Member\GlobalMessage;
use App\Model\Member\TravelPass;
use App\Model\Member\VisitingMember;
use App\Model\MasterData\MembershipType;
use App\Model\MasterData\Locker;
use App\Model\CommunityPartner\CommunityPartner;
use App\Model\CommunityPartner\CommunityPartnerMessage;
use App\User;
use App\Model\MasterData\Challenge;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AjaxController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getprovince(Request $request)
    {
        $country_id = $request->country_id;
        $provinces = Province::where('country_id', $country_id)->pluck('province', 'id');
        return response()->json($provinces);
    }

    public function getcities(Request $request)
    {
        $province_id = $request->province_id;
        $cities = City::where('province_id', $province_id)->pluck('city', 'id');
        return response()->json($cities);
    }

    public function getMemberOrLeads(Request $request)
    {
        $name_user = $request->nama;
//        $results_member = User::where('role_id', '=', '7')->where('club_id', '=', Auth::user()->club_id)->where('name', 'ilike', '%' . $name_user . '%')->get();
        $results_member = User::where('club_id', '=', Auth::user()->club_id)
            ->where('name', 'ilike', '%' . $name_user . '%')
            ->whereHas('user_memberstatus', function ($q) {
                $q->where('status', '<>', 'Inactive')
                    ->where('status', '<>', 'Stopped');
            })
            ->get();
        $results_leads = BuddyReferralLead::where('status_gm', '!=', 'Member')->where('fullname', 'ilike', '%' . $name_user . '%')->get();

        //Tambah Foto
        foreach ($results_member as $member):
            if (!empty($member->user_memberpersonal)):
                $member->setAttribute('photo', $member->user_memberpersonal->photo);
            endif;
        endforeach;

        $results = [$results_member, $results_leads];
        return response()->json($results);
    }

    public function getMemberPersonal(Request $request) {
        $member = User::findOrFail($request->member_id);

        if (!empty($member->user_memberpersonal->photo)):
            $member->setAttribute('photo', $member->user_memberpersonal->photo);
        endif;

        $member_workout = Workout::where('user_id', '=', $request->member_id)->orderBy('workout_date', 'desc')->first();
        $member->setAttribute('workout_date', Carbon::parse($member_workout->workout_date)->setTimezone(session('timezone'))->toDateTimeString());

        return response()->json(array('member' => $member));
    }

    public function getAppointmentData(Request $request) {
        if ($request->appointment_type == 'G'):
            $appointment = GuestAppointment::findOrFail($request->appointment_id);
            $user = BuddyReferralLead::findOrFail($appointment->guest_id);
        elseif ($request->appointment_type == 'M'):
            $appointment = MemberAppointment::findOrFail($request->appointment_id);
            $appointment->setAttribute('appointment_type', $appointment->memberappointment_appointmenttype->type);

            $user = User::findOrFail($appointment->user_id);
            if (!empty($user->user_memberpersonal->photo)):
                $user->setAttribute('photo', $user->user_memberpersonal->photo);
            endif;
        endif;

        return response()->json(array('appointment' => $appointment, 'user' => $user));
    }

    public function ajax_check_voucher_product(Request $request) {
        $voucher = Voucher::where('identity_number', '=', $request->voucher_code)
            ->whereDate('from_date', '<=', Carbon::now()->toDateString())
            ->whereDate('thru_date', '>=', Carbon::now()->toDateString())
            ->first();

        if (count($voucher) > 0):
            return response()->json(array('voucher_data' => $voucher));
        else:
            return response()->json(array('voucher_data' => 'expired'));
        endif;
    }

    public function get_member_detail(Request $request) {
        $member_name = $request->member_name;
        $members = User::where('role_id', '=', '7')
            ->where('club_id', '=', $request->club_id)
            ->where('name', 'ILIKE', '%'.$member_name.'%')
            ->whereHas('user_memberstatus', function ($q) {
                $q->where('status', '=', 'Active');
            })
            ->orderBy('name', 'asc')
            ->get();

        //Tambah Foto
        foreach ($members as $member):
            if (!empty($member->user_memberpersonal)):
                $member->setAttribute('photo', $member->user_memberpersonal->photo);
                $member->setAttribute('phone', $member->user_memberpersonal->phone);
            endif;
        endforeach;

        return response()->json(array('members' => $members));
    }

    public function get_lead_detail(Request $request) {
        $lead = BuddyReferralLead::findOrFail($request->lead_id);

        return response()->json(array('lead' => $lead));
    }

    public function get_member_message_note_detail(Request $request) {
        $member = User::findOrFail($request->member_id);

        $message_note = $member->user_membermessagenote;

        return response()->json(array('member' => $member, 'message' => $message_note));
    }

    public function get_predefined_message_detail(Request $request) {
        $message = PredefinedMessage::findOrFail($request->message_id);

        return response()->json(array('message' => $message));
    }

    public function get_challenges_detail(Request $request) {
        $challenge = Challenge::findOrFail($request->challenge_id);

        return response()->json(array('challenge' => $challenge));
    }
	
	public function get_staff_report_detail(Request $request) {
        $report = StaffReport::findOrFail($request->report_id);

        return response()->json(array('report' => $report));
    }

    public function get_thirty_days_report_detail(Request $request) {
        $thirty_days = MemberThirtyDay::findOrFail($request->thirty_days_id);

        return response()->json(array('thirty_days' => $thirty_days));
    }

    public function get_globalevent_detail(Request $request) {
        $globalevent = GlobalEvent::findOrFail($request->globalevent_id);
        $selectedclub = $globalevent->globalevent_club;

        return response()->json(array('globalevent' => $globalevent , 'selectedclub' => $selectedclub));
    }

    public function get_globalmessage_detail(Request $request) {
        $globalmessage = GlobalMessage::findOrFail($request->globalmessage_id);
        $selectedclub = $globalmessage->globalmessage_club;

        return response()->json(array('globalmessage' => $globalmessage , 'selectedclub' => $selectedclub));
    }

    public function set_staff_session(Request $request) {
        $staff = User::findOrFail($request->staff_id);

        session(['staff_session' => $staff->id, 'staff_session_name' => $staff->name]);

        return response()->json(array('staff' => $staff));
    }

    public function get_membership_type_detail(Request $request) {
        $membershipType = MembershipType::findOrFail($request->membership_type_id);

        return response()->json(array('membershipType' => $membershipType));
    }

    public function get_locker_detail(Request $request){
        $locker = Locker::findOrFail($request->locker_id);

        return response()->json(array('locker' => $locker));
    }

    public function get_cp_detail(Request $request) {
        $cp = CommunityPartner::findOrFail($request->cp_id);

        return response()->json(array('cp' => $cp));
    }

    public function get_cp_message_detail(Request $request) {
        $cpm = CommunityPartnerMessage::findOrFail($request->cpm_id);

        return response()->json(array('cpm' => $cpm));
    }

    public function get_certification_detail(Request $request) {
        $certification = Certification::findOrFail($request->certification_id);

        return response()->json(array('certification' => $certification));
    }

    public function get_memberlocker_detail(Request $request){
        $memberlocker = MemberLocker::findOrFail($request->memberlocker_id);
        $memberlocker->setAttribute('namauser',$memberlocker->memberlocker_user->name);
        $memberlocker->setAttribute('iduser',$memberlocker->memberlocker_user->id);
        $memberlocker->setAttribute('namaloker',$memberlocker->memberlocker_lockerinfo->locker_name);

        return response()->json(array('memberlocker' => $memberlocker));
    }

    public function get_travel_pass_detail(Request $request){
        $travelpass = TravelPass::findOrFail($request->travelpass_id);
        $travelpass->setAttribute('member_name',$travelpass->travelpass_user->name);

        return response()->json(array('travelpass' => $travelpass));
    }

    public function get_visiting_member_detail(Request $request){
        $members = VisitingMember::findOrFail($request->visitingmember_id);

        return response()->json(array('members' => $members));
    }
	
	public function get_fa_detail(Request $request) {
        $fa = GuestAppointment::join('guest_appointment_status','guest_appointment_status.ga_id','=','guest_appointments.id')
            ->join('buddy_referral_leads','buddy_referral_leads.id','=','guest_appointments.guest_id')
            ->where('guest_appointments.id','=',$request->fa_id)
            ->selectRaw('buddy_referral_leads.fullname as name,
                                    buddy_referral_leads.phone as phone,
                                    guest_appointment_status.status_id as status_id,
                                    guest_appointments.id as fa_id')
            ->first();

        return response()->json(array('fa' => $fa));
    }
	
	public function get_staff_name_from(Request $request){
        $staffs = User::where('club_id', '=', $request->club_id)
            ->where(function ($q){
                $q->where('role_id', '=', 6)->get();
            })->get();
        return response()->json(array('staffs' => $staffs));
    }

    public function get_staff_name_to(Request $request){
        $staffs = User::where('club_id', '=', $request->club_id)
            ->where(function ($q){
                $q->where('role_id', '=', 6)->get();
            })->get();
        return response()->json(array('staffs' => $staffs));
    }

    public function get_ajax_category(Request $request){
        $cate_id = ClCategory::findOrFail($request->cat_id);

        return response()->json(array('cate_id'=>$cate_id));
    }

    public function get_ajax_material_header(Request $request){
        $material_header = ClMaterialHeader::findOrFail($request->materialheader_id);
        $category = ClCategory::findOrFail($material_header->category_id);

        return response()->json(array('materialheader'=>$material_header, 'category'=>($category->name)));
    }

    public function get_staff_with_membercoach(Request $request){
        $coaches = collect(new User);
        if(!empty($request->club_id)){
            $coaches = User::where('club_id','=',$request->club_id)->whereRaw('role_id IN (4,5,6,8)')->whereHas('coachuser_coachmember')->whereHas('user_memberstatus',function($r){
                $r->where('status','=','Active');
            })->get();
        }

        return response()->json(array('coaches'=>$coaches));
    }
}
