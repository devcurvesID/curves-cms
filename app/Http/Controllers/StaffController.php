<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddNewStaffRequest;
use App\Model\Appointment\GuestAppointment;
use App\Model\CoachDailyReport\CoachDailyReportArDetail;
use App\Model\CoachDailyReport\CoachDailyReportHeader;
use App\Model\CoachDailyReport\CoachDailyReportSalesDetail;
use App\Model\Guest\BuddyReferralLead;
use App\Model\Guest\GuestMonitoring;
use App\Model\Guest\GuestMonitoringDetail;
use App\Model\GuestMembership\GuestMemberPayment;
use App\Model\Locker\LockerPayment;
use App\Model\Log\LogMemberStatus;
use App\Model\MasterData\AccessMenu;
use App\Model\MasterData\CoachDailyReportCategory;
use App\Model\MasterData\MembershipType;
use App\Model\MasterData\MemberSource;
use App\Model\MasterData\TargetCategoryDetail;
use App\Model\Member\CoachMember;
use App\Model\Member\MemberPersonal;
use App\Model\Member\MemberStatus;
use App\Model\Product\ProductPaymentDetail;
use App\Model\Staff\CoachSequence;
use App\Model\Staff\CoachTarget;
use App\Model\Staff\StaffCode;
use App\Model\Staff\StaffInactive;
use App\Model\Staff\StaffNote;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Keygen\Keygen;
use Maatwebsite\Excel\Facades\Excel;
use mysql_xdevapi\Exception;
use PhpParser\Node\Expr\List_;
use Session;
use App\Http\Requests\StaffDetailRequest;
use App\Model\Club\ClubInfo;
use App\Model\MasterData\City;
use App\Model\MasterData\Country;
use App\Model\Role\FunctionalRole;
use App\Model\Role\Role;
use App\Model\Staff\StaffPersonal;
use App\Model\Staff\StaffWorkingHour;
use App\Model\Staff\TransferHistory;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\ApiClientService;
class StaffController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('wm-only');
        $this->middleware('staff', ['only' => [
            'club_list',
        ]]);
        $this->middleware('staff-management', ['only' => [
            'set_staff_inactive',
            'inactive_staff_list',
            'activate_permission_member',
            'reactivate_resigned_member',
        ]]);
        $this->middleware('staff-password', ['only' => [
            'update_password',
        ]]);
    }

    public function index()
    {
        $users = User::where('role_id', '<>', '7')->orderBy('name', 'asc')->get();
        return view('cf_back.staff.staff', compact('users'));
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        
        // Jonviter DEV
//        $staff_hours = StaffHours::where('user_id', $user->id)->get();
//        $staff_notes = StaffNotes::where('user_id', $user->id)->get();
//        $staff_timeoff = StaffTimeOff::where('user_id', $user->id)->get();
        return view('cf_back.staff.detail', compact('user'));
    }

    public function create()
    {
        $roles = Role::orderBy('role', 'asc')->where('role', '<>', 'Member')->pluck('role', 'id');
        $clubs = ClubInfo::orderBy('club_name', 'asc')->pluck('club_name', 'id');
        $cities = DB::table('mst_cities')->join('mst_provinces', 'mst_cities.province_id', '=', 'mst_provinces.id')->join('mst_countries', 'mst_provinces.country_id', '=', 'mst_countries.id')->select('mst_cities.id', 'mst_cities.city', 'mst_provinces.province', 'mst_countries.country')->get();


//        $froles = FunctionalRole::orderBy('f_role', 'asc')->pluck('f_role', 'id');

        return view('cf_back.staff.add-staff', compact('roles', 'clubs', 'cities'));
    }

    public function store(StaffPersonalRequest $request)
    {
        $user = new User;
        $user->name = $request->first_name . ' ' . $request->sur_name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->role_id = $request->role_id;
        $user->club_id = $request->club_id;
        $user->save();

        $staff = new StaffPersonal;
        $staff->first_name = $request->first_name;
        $staff->middle_name = $request->middle_name;
        $staff->sur_name = $request->sur_name;

        if ($request->hasFile('photo')) {
            $path = 'staffphotos';
            $staff->photo = $this->uploadPhoto($path, $request);
        }

        $staff->birth = $request->birth;
        $staff->sex = $request->sex;
        $staff->email_personal = $request->email_personal;
        $staff->nik = $request->nik;
        $staff->npwp = $request->npwp;
        $staff->country_id = $request->country_id;
        $staff->province_id = $request->province_id;
        $staff->city_id = $request->city_id;
        $staff->address = $request->address;
        $staff->postal = $request->postal;
        $staff->phone = $request->phone;
        $staff->cellphone = $request->cellphone;
        $staff->key_tag_id = $request->key_tag_id;
        $staff->joined = Carbon::now();
        $user->user_staffpersonal()->save($staff);

        $status = new StaffStatus;
        $status->status = 'Active';
        $user->user_staffstatus()->save($status);

        if (!empty($request->frole_id)):
            $user->user_frole()->attach($request->input('frole_id'));
        endif;

        // Jonviter DEV
        $user_by_id = DB::table('usermobilecurves_v')->where('id', $user->id)->first();
        if ($user_by_id):
            $apiClientOne = new ApiClientService();
            $fetch_usercurves = $apiClientOne->post("/mobile-curves/user", (array) $user_by_id);
        endif;
         // Jonviter DEV
        


        Session::flash('flash_message', trans('messages.userstaff_create_message'));

        return redirect('staff');
    }
//    public function edit($id) {
//        $user = User::findOrFail($id);
//        $roles = Role::pluck('role', 'id');
//        $clubs = ClubInfo::pluck('club_name', 'id');
//        $countries = Country::pluck('country', 'id');
//        $provinces = Province::where('country_id', $user->user_staffpersonal->country_id)->pluck('province', 'id');
//        $cities = City::where('province_id', $user->user_staffpersonal->province_id)->pluck('city', 'id');
//        $froles = FunctionalRole::orderBy('f_role', 'asc')->get();
//        return view('users.edit-staff', compact('user', 'clubs', 'roles', 'countries', 'provinces', 'cities', 'froles'));
//    }
//
//    public function update($id, StaffPersonalRequest $request) {
//        $user = User::findOrFail($id);
//
//        $user->name = $request->first_name.' '.$request->sur_name;
//        $user->email = $request->email;
//        $user->role_id = $request->role_id;
//        $user->club_id = $request->club_id;
//        $user->save();
//
//        $staff = $user->user_staffpersonal;
//        $staff->first_name = $request->first_name;
//        $staff->sur_name = $request->sur_name;
//
//        if ($request->hasFile('photo')) {
//            $this->deletePhoto($user);
//
//            $path = 'staffphotos';
//            $staff->photo = $this->uploadPhoto($path ,$request);
//        }
//
//        $staff->birth = $request->birth;
//        $staff->sex = $request->sex;
//        $staff->email_personal = $request->email_personal;
//        $staff->nik = $request->nik;
//        $staff->npwp = $request->npwp;
//        $staff->country_id = $request->country_id;
//        $staff->province_id = $request->province_id;
//        $staff->city_id = $request->city_id;
//        $staff->address = $request->address;
//        $staff->postal = $request->postal;
//        $staff->phone = $request->phone;
//        $staff->cellphone = $request->cellphone;
//        $staff->key_tag_id = $request->key_tag_id;
//        $user->user_staffpersonal()->save($staff);
//
//        $user->user_frole()->sync($request->frole_id);
//
//        Session::flash('flash_message', trans('messages.userstaff_update_message'));
//
//        return redirect('user/'.$id.'/edit');
//    }
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $this->deletePhoto($user);
        $user->delete();

        Session::flash('flash_message', trans('messages.user_delete_message'));

        return redirect('staff');
    }

    //CF LITE
    public function club_list() {
        $clubs = ClubInfo::where('status', '=', 'Active')->orderBy('club_name', 'asc')->paginate(12);

        //Adding club manager list
        if (Auth::user()->role_id < 4):
            $managers = User::where('role_id', '=', '4')->orderBy('name', 'asc')->get();

            return view('cf_lite.staff.club-list', compact('clubs', 'managers'));
        else:
            return view('cf_lite.staff.club-list', compact('clubs'));
        endif;
    }

    public function staff_list($id) {
        $club = ClubInfo::findOrFail($id);

        $staffs = User::where('club_id', '=', $id)
            ->whereHas('user_memberstatus', function ($q) {
                $q->where('status', '=', 'Active');
            })
            ->where('role_id', '<>', '7')
            ->orderBy('role_id', 'asc')
            ->paginate(12);

        $access_list = AccessMenu::all();

        return view('cf_lite.staff.staff-list', compact('staffs', 'club', 'access_list'));
    }

    public function add_new_staff($id) {
        $club = ClubInfo::findOrFail($id);

        if (Auth::user()->role_id != 8):
            $roles = Role::where('id', '<>', '7')->where('id', '>', Auth::user()->role_id)->orderBy('id', 'asc')->pluck('role', 'id');
        else:
            $roles = Role::where('id', '<>', '7')->where('id', '>', '3')->orderBy('id', 'asc')->pluck('role', 'id');
        endif;

        return view('cf_lite.staff.add-new-staff', compact('club', 'roles'));
    }

    public function store_new_staff($id, AddNewStaffRequest $request) {
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email_personal;
        $user->password = bcrypt($request->password);
        $user->role_id = $request->role;
        $user->club_id = $id;
        $user->username = $request->username;
        $user->save();


        //Buddy Referral Leads added 07032018
        $brl = new BuddyReferralLead;
        $brl->fullname = $request->name;
        $brl->phone = $request->phone;
        $brl->is_br = 'NotBR';
        $brl->staff_id = Auth::id();
        $brl->club_id = $id;
        $brl->status_gm = 'Member';
        $brl->email = $request->email_personal;
        $brl->save();

        //staff code
        $code = new StaffCode;
        $code->user_id = $user->id;
        $code->code = $request->staff_code;
        $code->save();

        //Member Personal
        $member_personal = new MemberPersonal;

        if (!empty($request->key_tag_id)):
            $member_personal->key_tag_id = $request->key_tag_id;
        else:
            $member_personal->key_tag_id = '9999'.Keygen::numeric(5)->generate();
        endif;

        $member_personal->phone = $request->phone;
        $member_personal->sales_person_id = Auth::id();
        $member_personal->guest_id = $brl->id;
        $member_personal->joined = Carbon::now();
        $member_personal->created_by = Auth::id();

        $user->user_memberpersonal()->save($member_personal);


        //Staff Personal
        if (!empty($request->email_personal)):
            $staff_personal = new StaffPersonal;
            $staff_personal->email_personal = $request->email_personal;
            $user->user_staffpersonal()->save($staff_personal);
        endif;


        //Member Status -> Untuk Staff
        if (!empty($user->user_memberstatus)):
            $member_status = $user->user_memberstatus;
        else:
            $member_status = new MemberStatus;
        endif;
        $member_status->status = 'Active';

        $membership_staff = MembershipType::where('membership_type', '=', 'Staff')->first();
        $member_status->membership_type_id = $membership_staff->id;

        $member_status->from_date = Carbon::now();
        $member_status->thru_date = Carbon::now()->addYears(99);
        $member_status->payment_status = 'NotSet';
        $user->user_memberstatus()->save($member_status);


        //Log Member Status
        $log_member_status = new LogMemberStatus;
        $log_member_status->user_id = $user->id;
        $log_member_status->membership_type_id = $membership_staff->id;
        $log_member_status->from_date = Carbon::now();
        $log_member_status->thru_date = Carbon::now()->addYears(99);
        $log_member_status->save();

        //Transfer Staff History
        $transferHistory = new TransferHistory;
        $transferHistory->user_id = $user->id;
        $transferHistory->club_id = $user->club_id;
        $transferHistory->role_id = $user->role_id;
        $transferHistory->from_date = \Carbon\Carbon::now();
        $transferHistory->save();

        //Access Menu Default
        $access_list = AccessMenu::where('default_role', '>=', $request->role)->get();
        if ($access_list->count() > 0):
            foreach ($access_list as $access):
                $user->user_accessmenu()->attach($access->id);
            endforeach;
        endif;

        // Adding coach target detail
        $target_details = TargetCategoryDetail::where('type', '=', 'Staff')->get();
        if ($target_details->count() > 0):
            foreach ($target_details as $detail):
                $coach_target = new CoachTarget();
                $coach_target->coach_id = $user->id;
                $coach_target->target_category_detail_id = $detail->id;
                $coach_target->target = $detail->target;
                $coach_target->acquired = 0;
                $coach_target->status = $detail->status;
                $coach_target->save();
            endforeach;
        endif;

        Session::flash('flash_message', trans('messages.staff_has_been_added'));

        return redirect('cf-lite/staff/'.$user->id.'/detail');
    }

    public function staff_detail($id) {
        $staff = User::findOrFail($id);
         // Jonviter DEV
        // $user_by_id = DB::table('usermobilecurves_v')->where('id', $staff->id)->first();
        // if ($user_by_id):
        //     $apiClientOne = new ApiClientService();
        //     $fetch_usercurves = $apiClientOne->post("/mobile-curves/user", (array) $user_by_id);
        // endif;
         // Jonviter DEV
        if (Auth::user()->role_id == '4'):
            $roles = Role::where('id', '<>', '7')->where('id', '>', '4')->orderBy('id', 'asc')->pluck('role', 'id');
        else:
            $roles = Role::where('id', '<>', '7')->orderBy('id', 'asc')->pluck('role', 'id');
        endif;
        $functional_roles = FunctionalRole::orderBy('functional_role', 'asc')->pluck('functional_role', 'id');

        $transferHistories = TransferHistory::where('user_id', '=', $id )->orderBy('from_date', 'asc')->paginate(10);

        return view('cf_lite.staff.staff-detail', compact('staff', 'roles', 'functional_roles','transferHistories'));
    }

    public function staff_update($id, StaffDetailRequest $request) {
        $staff = User::findOrFail($id);

        if ($request->attribute == 'info'):
            $staff_index_start = 0;
            $staff_index_end = 1;
        elseif ($request->attribute == 'working-hour'):
            $staff_index_start = 1;
            $staff_index_end = 2;
        elseif ($request->attribute == 'sfr'):
            $staff_index_start = 2;
            $staff_index_end = 3;
        else:
            $staff_index_start = 0;
            $staff_index_end = 4;
        endif;

        for ($j = $staff_index_start; $j < $staff_index_end; $j++):
            if ($j == 0):
                //Edit Data USER
                $staff->name = $request->name;
                $staff->email = $request->email;

                if (!empty($request->role)):
                    if($staff->role_id != $request->role){
                        $transferHistory = new TransferHistory;
                        $transferHistory->user_id = $staff->id;
                        $transferHistory->club_id = $staff->club_id;
                        $transferHistory->role_id = $request->role;
                        $transferHistory->from_date = \Carbon\Carbon::now();
                        $transferHistory->save();
                    }
                    $staff->role_id = $request->role;
                endif;

                $staff->save();

                if(!empty($request->staff_code))
                {
                    $staffcode = StaffCode::where('user_id','=',$staff->id)->first();
                    if($staffcode!=null)
                    {
                        $staffcode->code = strtoupper($request->staff_code);
                        $staffcode->save();
                    }
                    else
                    {
                        $staffcode = new StaffCode;
                        $staffcode->user_id = $staff->id;
                        $staffcode->code = strtoupper($request->staff_code);
                        $staffcode->save();
                    }
                }

                //Edit Data Member Personal
                $member_personal = $staff->user_memberpersonal;
                $member_personal->birth = $request->birth;
                $member_personal->address = $request->address;
                $member_personal->postal = $request->postal;

                if (empty($request->phone)):
                    $member_personal->phone = $request->cellphone;
                else:
                    $member_personal->phone = $request->phone;
                endif;

                $member_personal->cellphone = $request->cellphone;
                $member_personal->nik = $request->nik;

                if ($request->hasFile('photo')) {
                    if (!empty($member_personal->photo)):
                        $this->deletePhoto($staff);
                    endif;

                    $path = 'images/member_photos';
                    $member_personal->photo = $this->uploadPhoto($path ,$request);
                }

                $member_personal->key_tag_id = $request->key_tag_id;
                $member_personal->joined = $request->joined;
                $staff->user_memberpersonal()->save($member_personal);


                //Edit Staff Personal
                if (!empty($staff->user_staffpersonal)):
                    $staff_personal = $staff->user_staffpersonal;
                else:
                    $staff_personal = new StaffPersonal;
                endif;

                $staff_personal->email_personal = $request->email_personal;
                $staff_personal->npwp = $request->npwp;
                $staff->user_staffpersonal()->save($staff_personal);

                Session::flash('flash_message', trans('messages.staff_personal_has_been_updated'));
            elseif ($j == 1):
                $staff_working_hour_list = StaffWorkingHour::where('user_id', '=', $staff->id)->orderBy('id', 'asc')->get();
                if (count($request->working_hour_id) > 0):
                    //Tahap 1
                    foreach ($staff_working_hour_list as $swh):
                        $swh_edit = 0;
                        foreach ($request->working_hour_id as $key => $value):
                            if ($swh->id == $value):
                                $swh_edit = 1;
                                $swh_temp = StaffWorkingHour::findOrFail($swh->id);
                                $swh_temp->from_day = $request->from_day[$key];
                                $swh_temp->thru_day = $request->thru_day[$key];
                                $swh_temp->from_hour = Carbon::createFromFormat('H:i', Carbon::parse($request->from_hour[$key])->format('H:i'), session('timezone'))->setTimezone('UTC');
                                $swh_temp->thru_hour = Carbon::createFromFormat('H:i', Carbon::parse($request->thru_hour[$key])->format('H:i'), session('timezone'))->setTimezone('UTC');
                                $swh_temp->save();
                            endif;
                        endforeach;

                        if ($swh_edit == 0):
                            $swh_temp = StaffWorkingHour::findOrFail($swh->id);
                            $swh_temp->delete();
                        endif;
                    endforeach;

                    //Tahap 2
                    foreach ($request->working_hour_id as $key => $value):
                        if(!is_numeric($value)):
                            $new_swh = new StaffWorkingHour;
                            $new_swh->user_id = $staff->id;
                            $new_swh->from_day = $request->from_day[$key];
                            $new_swh->thru_day = $request->thru_day[$key];
                            $new_swh->from_hour = $request->from_hour[$key];
                            $new_swh->thru_hour = $request->thru_hour[$key];
                            $new_swh->save();
                        endif;
                    endforeach;
                endif;
                
                Session::flash('flash_message', trans('messages.staff_working_hour_has_been_updated'));
            elseif ($j == 2):
                if (count($staff->staff_functionalrole) > 0):
                    $staff->staff_functionalrole()->sync($request->input('functional_role'));
                else:
                    $staff->staff_functionalrole()->attach($request->input('functional_role'));
                endif;

                Session::flash('flash_message', trans('messages.staff_functional_role_has_been_updated'));
            endif;
        endfor;

        return redirect('cf-lite/staff/'.$id.'/detail');
    }

    public function set_staff_inactive($id, Request $request) {
        $this->validate($request, [
            'inactive_type' => 'required|in:Permission,Resign',
            'reason' => 'sometimes',
//            'from_date' => 'required|date|after:yesterday',
            'from_date' => 'required|date',
        ]);

        if ($request->inactive_type == 'Permission'):
            $this->validate($request, [
                'thru_date' => 'required|date|after:from_date',
            ]);
        elseif ($request->inactive_type == 'Resign'):
            $this->validate($request, [
                'thru_date' => 'sometimes',
            ]);
        endif;

        $staff = User::findOrFail($id);
        if ($staff->user_memberstatus->status == 'Active'):
            //Change member status into Inactive
            $staff_status = $staff->user_memberstatus;
            $staff_status->status = 'Inactive';

            if ($request->inactive_type == 'Resign'):
                $staff_status->thru_date = Carbon::now();
            endif;

            $staff->user_memberstatus()->save($staff_status);


            if ($request->inactive_type == 'Resign'):
                //Change log member status
                $log_member_status = LogMemberStatus::where('user_id', '=', $staff->id)
                    ->where('membership_type_id', '=', '9')
                    ->whereDate('from_date', '=', Carbon::parse($staff->user_memberstatus->from_date))
                    ->orderBy('created_at', 'desc')
                    ->first();

                $log_member_membership = LogMemberStatus::findOrFail($log_member_status->id);
                $log_member_membership->thru_date = Carbon::now();
                $log_member_membership->save();

                //Change User username & password
                $staff->email = date('dmYhis', strtotime(Carbon::now())).'@curvesmember.com';
                $staff->password = bcrypt('curvesindonesia');
                $staff->role_id = '7';
                $staff->username = date('dnygi', strtotime(Carbon::now())).'.cm.'.$staff->user_memberpersonal->memberpersonal_buddyreferrallead->id;
                $staff->save();
            endif;

            //Set Staff Inactive
            $staff_inactive = new StaffInactive;
            $staff_inactive->staff_id = $staff->id;
            $staff_inactive->inactive_type = $request->inactive_type;

            if (!empty($request->reason)):
                $staff_inactive->reason = $request->reason;
            endif;

            $staff_inactive->from_date = $request->from_date;

            if (($request->inactive_type == 'Permission') && !empty($request->thru_date)):
                $staff_inactive->thru_date = $request->thru_date;
            endif;

            if ($request->inactive_type == 'Permission'):
                $staff_inactive->status = 'Ongoing';
            elseif ($request->inactive_type == 'Resign'):
                $staff_inactive->status = 'Done';
            endif;

            $staff_inactive->save();
        endif;

        if ($request->inactive_type == 'Permission'):
            return redirect('cf-lite/staff/'.Auth::user()->club_id.'/inactive-staff-list');
        elseif ($request->inactive_type == 'Resign'):
            return redirect('cf-lite/staff/'.Auth::user()->club_id);
        endif;
    }

    public function inactive_staff_list($id) {
        $club = ClubInfo::findOrFail($id);

        $permission_members = StaffInactive::where('inactive_type', '=', 'Permission')
            ->where('status', '=', 'Ongoing')
            ->whereHas('staffinactive_user', function ($q) use ($club) {
                $q->where('club_id', '=', $club->id)
                    ->whereHas('user_memberstatus', function ($r) {
                        $r->where('status', '=', 'Inactive');
                    });
            })
            ->orderBy('thru_date', 'asc')
            ->get();

        $resigned_members = StaffInactive::where('inactive_type', '=', 'Resign')
            ->where('status', '=', 'Done')
            ->whereHas('staffinactive_user', function ($s) {
                $s->whereHas('user_memberstatus', function ($t) {
                    $t->where('status', '=', 'Inactive');
                });
            })
            ->orderBy('from_date', 'asc')
            ->get();

        $roles = Role::where('id', '>', '2')
            ->where('id', '<>', '7')
            ->pluck('role', 'id');

        return view('cf_lite.staff.inactive-staff-list', compact('club', 'permission_members', 'resigned_members', 'roles'));
    }

    public function activate_permission_member($id) {
        $staff_inactive = StaffInactive::findOrFail($id);
        $staff_inactive->status = 'Done';
        $staff_inactive->save();

        $staff = User::findOrFail($staff_inactive->staff_id);
        $member_status = $staff->user_memberstatus;
        $member_status->status = 'Active';
        $staff->user_memberstatus()->save($member_status);

        Session::flash('flash_message', trans('messages.staff_has_been_activated'));
        return redirect('cf-lite/staff/'.$staff->club_id.'/inactive-staff-list');
    }

    public function reactivate_resigned_member(Request $request) {
        $reactivate = StaffInactive::findOrFail($request->reactivate_id);

        $this->validate($request, [
            'reactivate_id' => 'required|numeric',
            'role' => 'required',
            'email' => 'required|string|email|max:255|unique:users,email,'.$reactivate->staff_id,
            'username' => 'required|string|min:6|unique:users,username,'.$reactivate->staff_id,
            'password' => 'required|confirmed|min:6',
            'club' => 'required',
            'from_date' => 'required|date',
        ]);

//        $reactivate = StaffInactive::findOrFail($request->reactivate_id);
        $staff = User::findOrFail($reactivate->staff_id);

        if (($staff->role_id == '7') && ($staff->user_memberstatus->status == 'Inactive')):
            $staff->email = $request->email;
            $staff->password = bcrypt($request->password);
            $staff->role_id = $request->role;
            $staff->club_id = $request->club;
            $staff->username = $request->username;
            $staff->save();

            $member_status = $staff->user_memberstatus;
            $member_status->status = 'Active';
            $member_status->membership_type_id = '9';
            $member_status->from_date = $request->from_date;
            $member_status->thru_date = Carbon::parse($request->from_date)->addYears(99);
            $member_status->payment_status = 'NotSet';
            $staff->user_memberstatus()->save($member_status);

            //Log Member Status
            $log_member_status = new LogMemberStatus;
            $log_member_status->user_id = $staff->id;
            $log_member_status->membership_type_id = $staff->user_memberstatus->membership_type_id;
            $log_member_status->from_date = $request->from_date;
            $log_member_status->thru_date = Carbon::parse($request->from_date)->addYears(99);
            $log_member_status->save();

            //Transfer Staff History
            $transferHistory = new TransferHistory;
            $transferHistory->user_id = $staff->id;
            $transferHistory->club_id = $request->club;
            $transferHistory->role_id = $request->role;
            $transferHistory->from_date = $request->from_date;
            $transferHistory->save();

            //Access Menu Default
            $access_list = AccessMenu::where('default_role', '>=', $request->role)->get();
            if ($access_list->count() > 0):
                foreach ($access_list as $access):
                    $staff->user_accessmenu()->sync($access->id);
                endforeach;
            endif;

            //Reactivate Status
            $reactivate->status = 'Reactivated';
            $reactivate->save();

            Session::flash('flash_message', trans('messages.staff_has_been_reactivated'));
        else:
            Session::flash('flash_message', trans('messages.staff_cannot_be_reactivated'));
        endif;

        return redirect('cf-lite/staff/'.$request->club.'/inactive-staff-list');
    }

    public function manage_password($id) {
        $staff = User::findOrFail($id);

        return view('cf_lite.staff.change-password', compact('staff'));
    }

    public function update_password($id, Request $request) {
        $this->validate($request, [
            'old_password' => 'required|string|min:6',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $the_status = 0;
        $staff = User::findOrFail($id);

        if (Hash::check($request->old_password, $staff->password)):
            $the_status = 1;
        endif;

        if ($the_status == 1):
            $staff->password = bcrypt($request->password);
            $staff->save();

             // Jonviter DEV
            $user_by_id = DB::table('usermobilecurves_v')->where('id', $id)->first();
            if ($user_by_id):
                $apiClientOne = new ApiClientService();
                $fetch_usercurves = $apiClientOne->put("/mobile-curves/user", (array) $user_by_id);
            endif;
            // Jonviter DEV
            Session::flash('flash_message', trans('messages.staff_password_has_been_changed'));
            return redirect('cf-lite/staff/'.$staff->id.'/detail');
        else:
            Session::flash('flash_message', trans('messages.old_password_not_match'));
            return redirect('cf-lite/staff/'.$staff->id.'/manage-password');
        endif;
       
    }

    public function reset_password($id, Request $request){
        $user = User::findOrFail($id);
        $user->password = bcrypt('curvesindonesia');
        $user->save();
         // Jonviter DEV
        $user_by_id = DB::table('usermobilecurves_v')->where('id', $user->id)->first();
        if ($user_by_id):
            $apiClientOne = new ApiClientService();
            $fetch_usercurves = $apiClientOne->put("/mobile-curves/user", (array) $user_by_id);
        endif;
        // Jonviter DEV
        Session::flash('flash_message', trans('messages.staff_password_has_been_resetted'));
        return redirect('cf-lite/staff/' . $id . '/detail');
    }

    public function manage_note($id) {
        $staff = User::findOrFail($id);
        $notes = $staff->user_staffnote->where('status', 'Ongoing');

        return view('cf_lite.staff.manage-note', compact('staff', 'notes'));
    }

    public function store_staff_note($id, Request $request) {
        $this->validate($request, [
            'note' => 'required|string',
        ]);

        $staff = User::findOrFail($id);
        $staff_note = new StaffNote;
        $staff_note->user_id = $staff->id;
        $staff_note->note = $request->note;
        $staff_note->status = 'Ongoing';
        $staff_note->save();

        Session::flash('flash_message', trans('messages.note_has_been_added'));
        return redirect('cf-lite/staff/'.$staff->id.'/manage-note');
    }

    public function delete_staff_note($id) {
        $staff_note = StaffNote::findOrFail($id);
        $staff_note->status = 'Done';
        $staff_note->save();

        Session::flash('flash_message', trans('messages.note_has_been_deleted'));
        return redirect('cf-lite/staff/'.$staff_note->user_id.'/manage-note');
    }

    public function manager_class_changing_club($id, Request $request) {
        $user = User::findOrFail($id);
        $user->club_id = $request->active_club;
        $user->save();

        $request->session()->invalidate();

        Session::flash('flash_message', trans('messages.please_login_to').': '.$user->user_club->club_name);
        return redirect('login');
    }

    public function manager_club_setting($id) {
        if (Auth::user()->role_id < 4):
            $user = User::findOrFail($id);
            $clubs = ClubInfo::orderBy('club_name', 'asc')->get();

            return view('cf_lite.staff.manager-club-setting', compact('user', 'clubs'));
        else:
            return redirect('cf-lite/staff');
        endif;
    }

    public function store_manager_club_setting($id, Request $request) {
        if (Auth::user()->role_id < 4):
            $user = User::findOrFail($id);

            if (!empty($request->input('club'))):
                if (count($user->manager_club) > 0):
                    $user->manager_club()->sync($request->input('club'));
                else:
                    $user->manager_club()->attach($request->input('club'));
                endif;
            endif;

            Session::flash('flash_message', trans('messages.manager_club_update_succeed'));
            return redirect('cf-lite/staff/'.$user->id.'/manager-club-setting');
        else:
            return redirect('cf-lite/staff');
        endif;
    }
    //End of CF LITE


    private function uploadPhoto($path, Request $request)
    {
        $photo = $request->file('photo');
        $ext = $photo->getClientOriginalExtension();

        if ($request->file('photo')->isValid()) {
            $photo_name = date('YmdHis') . ".$ext";
            $upload_path = $path;
            $request->file('photo')->move($upload_path, $photo_name);
            return $photo_name;
        }

        return false;
    }

    private function deletePhoto(User $user)
    {
        //$user = User::findOrFail($id);
        $exist = Storage::disk('member_photos')->exists($user->user_staffpersonal->photo);

        if (isset($user->user_staffpersonal->photo) && $exist) {
            $delete = Storage::disk('member_photos')->delete($user->user_staffpersonal->photo);
            if ($delete) {
                return true;
            }
            return false;
        }
    }

    // STAFF TRANSFER
    public function transfer_staff_club_list() {
        $clubs = ClubInfo::where('status', '=', 'Active')->orderBy('club_name', 'asc')->paginate(12);

        return view('cf_lite.staff.staff-transfer-club-list', compact('clubs'));
    }

    public function staff_transfer_staff_list($id) {
        $club = ClubInfo::findOrFail($id);

        $staffs = User::where('club_id', '=', $id)
            ->where('role_id', '<>', '7')
            ->whereHas('user_memberstatus', function ($q) {
                $q->where('status', '=', 'Active');
            })
            ->orderBy('role_id', 'asc')
            ->paginate(12);

        return view('cf_lite.staff.staff-transfer-staff-list', compact('staffs', 'club'));
    }

    public function transfer_staff_detail($id) {
        $staff = User::findOrFail($id);

        $roles = Role::where('id', '<>', '7')->where('id','>','3')->orderBy('id', 'asc')->pluck('role', 'id');
        $curClub = ClubInfo::where('id','=',$staff->club_id)->where('status', '=', 'Active')->orderBy('club_name', 'asc')->pluck('club_name', 'id');
        $clubs = ClubInfo::where('id','<>',$staff->club_id)->where('status', '=', 'Active')->orderBy('club_name', 'asc')->pluck('club_name', 'id');

        return view('cf_lite.staff.transfer-staff-detail', compact('staff', 'roles', 'clubs','curClub'));
    }

    public function transfer_staff($id, Request $request){
        $this->validate($request,[
            'club'=>'required',
            'transfer_opt' => 'required',
            'selected_coach' => 'required_if:transfer_opt,swap'
        ]);

        $staff = User::findOrFail($id);

        $member_stopped = User::where('club_id', '=', $request->chosen_club)
            ->whereHas('user_memberstatus', function ($q) {
                $q->where('status', '=', 'Inactive')->orWhere('status', '=', 'Stopped');
            })
            ->get();

        foreach ($member_stopped as $member) {
            $coach_member = DB::delete('DELETE FROM coach_members WHERE member_id = '.$member->id);
        }

        if($request->transfer_opt == 'still'){

        }else if($request->transfer_opt == 'swap'){
            $prev_members = $staff->coachuser_coachmember;
            $default = User::where('name','ILIKE','%receptionist%')->where('club_id','=',$staff->club_id)->first();
            foreach ($prev_members as $member) {
                if(!empty($default)){
                    $member->coach_id = $default->id;
                    $member->save();
                }
            }

            $swapped = User::findOrFail($request->selected_coach);
            $members = $swapped->coachuser_coachmember;

            foreach ($members as $member) {
                $member->coach_id = $id;
                $member->save();
            }
        }else if($request->transfer_opt == 'cutnoswap'){
            $prev_members = $staff->coachuser_coachmember;
            $default = User::where('name','ILIKE','%receptionist%')->where('club_id','=',$staff->club_id)->first();
            // return $default;
            foreach ($prev_members as $member) {
                if(!empty($default)){
                    $member->coach_id = $default->id;
                    $member->save();
                }
            }
        }

        //Edit Data USER
        if (!empty($request->role)):
            $staff->role_id = $request->role;
        endif;

        if (!empty($request->club)):
            $staff->club_id = $request->club;
        endif;

        $staff->save();

        //Add to transfer History
        $transferHistory = new TransferHistory;
        $transferHistory->user_id = $staff->id;
        $transferHistory->club_id = $staff->club_id;
        $transferHistory->role_id = $staff->role_id;
        $transferHistory->from_date = \Carbon\Carbon::now();
        $transferHistory->save();

        //Edit Data Member Personal
        $member_personal = $staff->user_memberpersonal;

        $member_personal->key_tag_id = $request->key_tag_id;
        $staff->user_memberpersonal()->save($member_personal);

        //Edit Staff Personal
        if (!empty($staff->user_staffpersonal)):
            $staff_personal = $staff->user_staffpersonal;
        else:
            $staff_personal = new StaffPersonal;
        endif;

        $staff->user_staffpersonal()->save($staff_personal);

        Session::flash('flash_message', trans('messages.staff_personal_has_been_updated'));

        return redirect('cf-lite/transfer-staff/'.$id.'/detail');
    }

    public function choose_mutation(){
        return view('cf_lite.staff.staff-mutation-choice');
    }

    public function staff_rotation(){
        $club = ClubInfo::orderBy('id', 'asc')->pluck('club_name', 'id');
        return view('cf_lite.staff.staff-rotation', compact('club', 'staff'));
    }

    public function store_rotation(Request $request){
        $staff_from = $request->staff_from;
        $staff_to = $request->staff_to;

        $cs_from = CoachSequence::where('coach_id', '=', $staff_from)->first();
        $cs_to = CoachSequence::where('coach_id', '=', $staff_to)->first();
        $cs_from_temp = 0 + $cs_from->sequence;

        $cm_from = CoachMember::where('coach_id', '=', $staff_from)->get();
        $cm_to = CoachMember::where('coach_id', '=', $staff_to)->get();

        $user_from = User::where('id', '=', $staff_from)->first();
        $user_to = User::where('id', '=', $staff_to)->first();
        $user_temp = 0 + $user_from->club_id;

        if ($staff_from == $staff_to):
            Session::flash('flash_message', trans('messages.can_not_rotation_staff'));
            return redirect('cf-lite/transfer-staff/staff-rotation/club');
        else:
            $cs_from->club_id = $request->club_to; //mengubah data cs dengan yang baru
            $cs_from->sequence = $cs_to->sequence;
            $cs_from->save();

            $cs_to->club_id = $request->club_from; //mengubah data cs lain dengan value cs from
            $cs_to->sequence = $cs_from_temp;
            $cs_to->save();

            $user_from->club_id = $request->club_to; //mengubah data user club id
            $user_to->club_id = $user_temp;
            $user_from->save();
            $user_to->save();

            foreach ($cm_from as $co_member_from): //diisi dengan staff to
                if (!empty($cm_from)):
                    $co_member_from->coach_id = $staff_to;
                    $co_member_from->save();
                endif;
            endforeach;

            foreach ($cm_to as $co_member_to):
                if (!empty($cm_to)):
                    $co_member_to->coach_id = $staff_from;
                    $co_member_to->save();
                endif;
            endforeach;
            Session::flash('flash_message', trans('messages.success_rotation_staff'));
            return redirect('cf-lite/transfer-staff/staff-rotation/club');
        endif;
    }
    // END OF STAFF TRANSFER

    public function staff_daily_report(Request $request) {
        $staffs = User::whereRaw('role_id IN (5,6,8)')
            ->where('club_id', '=', Auth::user()->club_id)->pluck('name', 'id');
        $today = Carbon::today();
        if (!empty($request->date)):
            $today = $request->date;
        endif;

        $daily_reports = [];
        $memberships = '';
        $promos = '';
        $total_fa = '';
        $total_sales = '';
        $total_fwo = '';
        $total_leads = '';
        $gloves = '';
        $lockers = '';
        if ($request->staff != ''){
//        if (!empty($request->staff)):
            $user = User::findOrFail($request->staff);
//        else:
//            $user = Auth::user();
//        endif;

            $daily_reports = MemberSource::selectRaw('member_sources.id as source_id,
            member_sources.source,
            (
                SELECT COUNT(id)
                FROM buddy_referral_leads
                WHERE to_char(created_at, \'YYYY-MM-DD\') = \''.Carbon::parse($today)->toDateString().'\'
                    AND staff_id = '.$user->id.'
                    AND buddy_referral_leads.source_id = member_sources.id
            ) as leads,
            (
                SELECT COUNT(guest_monitoring_details.id)
                FROM guest_monitoring_details
                JOIN buddy_referral_leads
                    ON guest_monitoring_details.guest_monitoring_id = buddy_referral_leads.id
                WHERE to_char(start_call, \'YYYY-MM-DD\') = \''.Carbon::parse($today)->toDateString().'\'
                    AND guest_monitoring_details.staff_id = '.$user->id.'
                    AND buddy_referral_leads.source_id = member_sources.id
            ) as call_total,
            (
                SELECT COUNT(guest_monitoring_details.id)
                FROM guest_monitoring_details
                JOIN buddy_referral_leads
                    ON guest_monitoring_details.guest_monitoring_id = buddy_referral_leads.id
                WHERE to_char(start_call, \'YYYY-MM-DD\') = \''.Carbon::parse($today)->toDateString().'\'
                    AND guest_monitoring_details.status = \'Success\'
                    AND guest_monitoring_details.staff_id = '.$user->id.'
                    AND buddy_referral_leads.source_id = member_sources.id
            ) as answer,
            (
                SELECT COUNT(guest_monitoring_details.id)
                FROM guest_monitoring_details
                JOIN buddy_referral_leads
                    ON guest_monitoring_details.guest_monitoring_id = buddy_referral_leads.id
                JOIN guest_appointments
                    ON (guest_monitoring_details.guest_monitoring_id = guest_appointments.guest_id AND to_char(guest_appointments.created_at, \'YYYY-MM-DD\') = \''.Carbon::parse($today)->toDateString().'\')
                WHERE to_char(start_call, \'YYYY-MM-DD\') = \''.Carbon::parse($today)->toDateString().'\'
                    AND guest_monitoring_details.status = \'Success\'
                    AND guest_monitoring_details.staff_id = '.$user->id.'
                    AND buddy_referral_leads.source_id = member_sources.id
            ) as apo,
            (
                SELECT COUNT(guest_monitoring_details.id)
                FROM guest_monitoring_details
                JOIN buddy_referral_leads
                    ON guest_monitoring_details.guest_monitoring_id = buddy_referral_leads.id
                WHERE to_char(start_call, \'YYYY-MM-DD\') = \''.Carbon::parse($today)->toDateString().'\'
                    AND guest_monitoring_details.status = \'Rejected\'
                    AND guest_monitoring_details.staff_id = '.$user->id.'
                    AND buddy_referral_leads.source_id = member_sources.id
            ) as rejected,
            (
                SELECT COUNT(guest_monitoring_details.id)
                FROM guest_monitoring_details
                JOIN buddy_referral_leads
                    ON guest_monitoring_details.guest_monitoring_id = buddy_referral_leads.id
                WHERE to_char(start_call, \'YYYY-MM-DD\') = \''.Carbon::parse($today)->toDateString().'\'
                    AND guest_monitoring_details.status = \'Invalid\'
                    AND guest_monitoring_details.staff_id = '.$user->id.'
                    AND buddy_referral_leads.source_id = member_sources.id
            ) as invalid,
            (
                SELECT COUNT(guest_monitoring_details.id)
                FROM guest_monitoring_details
                JOIN buddy_referral_leads
                    ON guest_monitoring_details.guest_monitoring_id = buddy_referral_leads.id
                WHERE to_char(start_call, \'YYYY-MM-DD\') = \''.Carbon::parse($today)->toDateString().'\'
                    AND guest_monitoring_details.status = \'Hanging\'
                    AND guest_monitoring_details.staff_id = '.$user->id.'
                    AND buddy_referral_leads.source_id = member_sources.id
            ) as hanging')
                ->groupBy( 'member_sources.id')
                ->orderBy('member_sources.id', 'asc')
                ->get();

//            return $daily_reports;

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
                if (!empty($sales)):
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
                endif;

                if ($gate):
                    $membership->setAttribute('total_member', 0);
                endif;
            endforeach;

            // Fitness Study => Membership Promo
            $promos = GuestMemberPayment::select('id')
                ->whereDate('payment_date', '=', Carbon::parse($today)->toDateString())
                ->whereHas('gmpayment_gmmember', function($gmm) use ($user) {
                    $gmm->whereHas('gmmember_guest', function ($guest) use ($user) {
                        $guest->where('staff_id', '=', $user->id);
                    });
                })
                ->count();
            $total_sales += $promos;
            $total_fa += $promos;

            // Total FWO
//            $total_fwo = User::where('club_id', '=', Auth::user()->club_id)
//                ->where('role_id', '=', '7')
//                ->whereHas('user_logmemberstatus', function ($q) use ($today) {
//                    $q->whereDate('from_date', '<=', Carbon::parse($today)->toDateString());
//                })
//                ->whereHas('user_workout', function ($r) use ($today) {
//                    $r->whereDate('workout_date', '=', Carbon::parse($today)->toDateString());
//                        ->whereNotIn('user_id', DB::table('workouts')
//                            ->WhereDate('workout_date', '<', Carbon::parse($today))
//                            ->pluck('user_id'));
//                })
//                ->count();
            $total_fwo = 0;

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
        }

//        return response()->json([
//            'call_reports' => $daily_reports,
//            'sales' => $memberships,
//            'fs' => $promos,
//            'total_fa' => $total_fa,
//            'total_sales' => $total_sales,
//            'total_fwo' => $total_fwo,
//            'total_leads' => $total_leads,
//            'gloves' => $gloves,
//            'lockers' => $lockers]);

        return view('cf_lite.staff.staff-daily-report', compact('today', 'staffs', 'daily_reports', 'memberships', 'promos', 'total_fa', 'total_sales', 'total_fwo', 'total_leads', 'gloves', 'lockers'));
    }

    public function import_coach_report(Request $request) {
        if ($request->hasFile('data')) {
            $path = $request->file('data')->getRealPath();
            $data = Excel::load($path, function ($reader){
                $reader->noHeading();
            })->get();

            $row_counter = -1;
            $cek = [];
            $sales_header = [];
            $cekcdrheader = 0;
            $cdr_header = new CoachDailyReportHeader;
            foreach ($data as $key => $value) {
                $row_counter++;
                if ($row_counter == 0) {
                    //Cek Header
                    $arr = explode('-', $value[0]);
                    $date = $arr[3] . '-' . $arr[4] . '-' . $arr[5];
                    $user = User::findOrFail($arr[2]);
                    if (!empty($user)) {
                        $cek = CoachDailyReportHeader::where('coach_id', '=', $user->id)->where('date', '=', $date)->first();
                        if (!empty($cek)) {
                            $cekcdrheader = 1;
                        } else {
                            $cdr_header->coach_id = $user->id;
                            $cdr_header->date = $date;
                            $cdr_header->save();
                        }
                    }
                    continue;
                }

                if ($row_counter == 1) {
                } elseif ($row_counter >= 2 && $row_counter <= 19) {
                    if ($cekcdrheader == 0) {
                        //Add new data
                        $dataSource = new CoachDailyReportArDetail;
                        $dataSource->coach_daily_report_header_id = $cdr_header->id;
                        $source = MemberSource::where('source', 'ILIKE', '%' . $value[0] . '%')->first();
                        if (!empty($source)) {
                            $dataSource->source_id = $source->id;
                            $dataSource->total_calling = !empty($value[1]) ? $value[1] : 0;
                            $dataSource->answer = !empty($value[2]) ? $value[2] : 0;
                            $dataSource->apo = !empty($value[3]) ? $value[3] : 0;
                            $dataSource->reschedule = !empty($value[4]) ? $value[4] : 0;
                            $dataSource->reject = !empty($value[5]) ? $value[5] : 0;
                            $dataSource->wrong_number = !empty($value[6]) ? $value[6] : 0;
                            $dataSource->invalid = !empty($value[7]) ? $value[7] : 0;
                            $dataSource->na = !empty($value[8]) ? $value[8] : 0;
                            $dataSource->leads = !empty($value[9]) ? $value[9] : 0;
                            $dataSource->save();
                        }
                    } else {
                        //Update exist data
                        $source = MemberSource::where('source', 'ILIKE', '%' . $value[0] . '%')->first();
                        if (!empty($source)) {
                            $update_cdrdetail = $cek->cdrheader_cdrardetail()->where('source_id', '=', $source->id)->first();
                            if (!empty($update_cdrdetail)) {
                                $update_cdrdetail->total_calling = !empty($value[1]) ? $value[1] : 0;
                                $update_cdrdetail->answer = !empty($value[2]) ? $value[2] : 0;
                                $update_cdrdetail->apo = !empty($value[3]) ? $value[3] : 0;
                                $update_cdrdetail->reschedule = !empty($value[4]) ? $value[4] : 0;
                                $update_cdrdetail->reject = !empty($value[5]) ? $value[5] : 0;
                                $update_cdrdetail->wrong_number = !empty($value[6]) ? $value[6] : 0;
                                $update_cdrdetail->invalid = !empty($value[7]) ? $value[7] : 0;
                                $update_cdrdetail->na = !empty($value[8]) ? $value[8] : 0;
                                $update_cdrdetail->leads = !empty($value[9]) ? $value[9] : 0;
                                $update_cdrdetail->save();
                            }
                        }
                    }
                } elseif ($row_counter == 19) {
                } elseif ($row_counter == 20) { //Call-In Header
                    $mst_cdr_category = CoachDailyReportCategory::where('category', 'ILIKE', '%' . $value[0] . '%')->first();
                    if (!empty($mst_cdr_category)) {
                        $call_in_header = [
                            $mst_cdr_category->id,
                            $value[1],
                            $value[2],
                            $value[3],
                            $value[4],
                            $value[5],
                        ];
                    }
                } elseif ($row_counter == 21) { //Call-In Value
                    $call_in_detail = [
                        !empty($value[1]) ? $value[1] : 0,
                        !empty($value[2]) ? $value[2] : 0,
                        !empty($value[3]) ? $value[3] : 0,
                        !empty($value[4]) ? $value[4] : 0,
                        !empty($value[5]) ? $value[5] : 0,
                    ];
                    if ($cekcdrheader == 0) {
                        //Add new data
                        for ($i = 1; $i <= count($call_in_detail); $i++) {
                            $callin = new CoachDailyReportSalesDetail;
                            $callin->coach_daily_report_header_id = $cdr_header->id;
                            $callin->category_id = $call_in_header[0];
                            $callin->type = $call_in_header[$i];
                            $callin->total = $call_in_detail[$i - 1];
                            $callin->save();
                        }
                    } else {
                        //Update data
                        if (!empty($cek)) {
                            for ($i = 1; $i <= count($call_in_detail); $i++) {
                                $callin = CoachDailyReportSalesDetail::where('coach_daily_report_header_id', '=', $cek->id)
                                    ->where('category_id', '=', $call_in_header[0])
                                    ->where('type', '=', $call_in_header[$i])
                                    ->first();
                                if (!empty($callin)) {
                                    $callin->total = $call_in_detail[$i - 1];
                                    $callin->save();
                                }
                            }
                        }
                    }
                } elseif ($row_counter == 22) { //Walk-In Header
                    $mst_cdr_category = CoachDailyReportCategory::where('category', 'ILIKE', '%' . $value[0] . '%')->first();
                    if (!empty($mst_cdr_category)) {
                        $walk_in_header = [
                            $mst_cdr_category->id,
                            $value[1],
                            $value[2],
                            $value[3],
                            $value[4],
                            $value[5],
                        ];
                    }
                } elseif ($row_counter == 23) { //Walk-In Value
                    $walk_in_detail = [
                        !empty($value[1]) ? $value[1] : 0,
                        !empty($value[2]) ? $value[2] : 0,
                        !empty($value[3]) ? $value[3] : 0,
                        !empty($value[4]) ? $value[4] : 0,
                        !empty($value[5]) ? $value[5] : 0,
                    ];
                    if ($cekcdrheader == 0) {
                        //Add new data
                        for ($i = 1; $i <= count($walk_in_detail); $i++) {
                            $walkin = new CoachDailyReportSalesDetail;
                            $walkin->coach_daily_report_header_id = $cdr_header->id;
                            $walkin->category_id = $walk_in_header[0];
                            $walkin->type = $walk_in_header[$i];
                            $walkin->total = $walk_in_detail[$i - 1];
                            $walkin->save();
                        }
                    } else {
                        //Update data
                        if (!empty($cek)) {
                            for ($i = 1; $i <= count($walk_in_detail); $i++) {
                                $walkin = CoachDailyReportSalesDetail::where('coach_daily_report_header_id', '=', $cek->id)
                                    ->where('category_id', '=', $walk_in_header[0])
                                    ->where('type', '=', $walk_in_header[$i])
                                    ->first();
                                if (!empty($walkin)) {
                                    $walkin->total = $walk_in_detail[$i - 1];
                                    $walkin->save();
                                }
                            }
                        }
                    }
                } elseif ($row_counter == 24) { //Call In + Walk In
                    $mst_cdr_category = CoachDailyReportCategory::where('category', 'ILIKE', '%' . $value[0] . '%')->first();
                    if ($cekcdrheader == 0) {
                        //Add new data
                        if (!empty($mst_cdr_category)) {
                            $call_walk = new CoachDailyReportSalesDetail;
                            $call_walk->coach_daily_report_header_id = $cdr_header->id;
                            $call_walk->category_id = $mst_cdr_category->id;
                            $call_walk->type = $value[0];
                            $call_walk->total = !empty($value[1]) ? $value[1] : 0;
                            $call_walk->save();
                        }
                    } else {
                        //Update data
                        $call_walk = CoachDailyReportSalesDetail::where('coach_daily_report_header_id', '=', $cek->id)
                            ->where('category_id', '=', $mst_cdr_category->id)
                            ->where('type', '=', $value[0])
                            ->first();
                        if (!empty($call_walk)) {
                            $call_walk->total = !empty($value[1]) ? $value[1] : 0;
                            $call_walk->save();
                        }
                    }
                } elseif ($row_counter == 25) { //Total FA
                    $mst_cdr_category = CoachDailyReportCategory::where('category', 'ILIKE', '%' . $value[0] . '%')->first();
                    if ($cekcdrheader == 0) {
                        //Add new data
                        if (!empty($mst_cdr_category)) {
                            $totalfa = new CoachDailyReportSalesDetail;
                            $totalfa->coach_daily_report_header_id = $cdr_header->id;
                            $totalfa->category_id = $mst_cdr_category->id;
                            $totalfa->type = $value[0];
                            $totalfa->total = $value[1];
                            $totalfa->save();
                        }
                    } else {
                        //Update data
                        $totalfa = CoachDailyReportSalesDetail::where('coach_daily_report_header_id', '=', $cek->id)
                            ->where('category_id', '=', $mst_cdr_category->id)
                            ->where('type', '=', $value[0])
                            ->first();
                        if (!empty($totalfa)) {
                            $totalfa->total = $value[1];
                            $totalfa->save();
                        }
                    }
                } elseif ($row_counter == 26) { //Sales Header
                    for ($i = 0; $i < count($value); $i++) {
                        array_push($sales_header, $value[$i]);
                    }
                } elseif ($row_counter == 27) { //Sales value
                    $detail = [];
                    for ($i = 0; $i < count($value); $i++) {
                        array_push($detail, $value[$i]);
                    }
                    if ($cekcdrheader == 0) {
                        //Add new data
                        $headersales = CoachDailyReportCategory::where('category', 'ILIKE', '%'. $sales_header[0].'%')->first();
                        if (!empty($headersales)) {
                            for ($i = 1; $i < count($value); $i++) {
                                if ($sales_header[$i] != 'Total') {
                                    if ($value[$i] == '0' || $value[$i] > 0) {
                                        $sales = new CoachDailyReportSalesDetail;
                                        $sales->coach_daily_report_header_id = $cdr_header->id;
                                        $sales->category_id = $headersales->id;
                                        $sales->type = $sales_header[$i];
                                        $sales->total = $detail[$i];
                                        $sales->save();
                                    }
                                }
                            }
                        }
                    } else {
                        //Update data
                        if (!empty($cek)) {
                            for ($i = 1; $i < count($value); $i++) {
                                if ($sales_header[$i] != 'Total') {
                                    if ($value[$i] == '0' || $value[$i] > 0) {
                                        $sales = CoachDailyReportSalesDetail::where('coach_daily_report_header_id', '=', $cek->id)
                                            ->whereHas('cdrsalesdetail_cdrcategory', function ($q) use ($sales_header){
                                                $q->where('category', 'ILIKE', '%'.$sales_header[0].'%');
                                            })
                                            ->where('type', 'ILIKE', '%'.$sales_header[$i].'%')
                                            ->first();
                                        if (!empty($sales)) {
                                            $sales->total = !empty($detail[$i]) ? $detail[$i] : 0;
                                            $sales->save();
                                        }
                                    }
                                }
                            }
                        }
                    }
                } elseif ($row_counter == 28) { //No Sales
                    $mst_cdr_category = CoachDailyReportCategory::where('category', 'ILIKE', '%' . $value[0] . '%')->first();
                    if ($cekcdrheader == 0) {
                        //Add new data
                        if (!empty($mst_cdr_category)) {
                            $nosales = new CoachDailyReportSalesDetail;
                            $nosales->coach_daily_report_header_id = $cdr_header->id;
                            $nosales->category_id = $mst_cdr_category->id;
                            $nosales->type = $value[0];
                            $nosales->total = $value[1];
                            $nosales->save();
                        }
                    } else {
                        //Update data
                        $nosales = CoachDailyReportSalesDetail::where('coach_daily_report_header_id', '=', $cek->id)
                            ->where('category_id', '=', $mst_cdr_category->id)
                            ->where('type', '=', $value[0])
                            ->first();
                        if (!empty($nosales)) {
                            $nosales->total = $value[1];
                            $nosales->save();
                        }
                    }
                } elseif ($row_counter == 29) { //Total FWO
                    $mst_cdr_category = CoachDailyReportCategory::where('category', 'ILIKE', '%' . $value[0] . '%')->first();
                    if ($cekcdrheader == 0) {
                        //Add new data
                        if (!empty($mst_cdr_category)) {
                            $totalfwo = new CoachDailyReportSalesDetail;
                            $totalfwo->coach_daily_report_header_id = $cdr_header->id;
                            $totalfwo->category_id = $mst_cdr_category->id;
                            $totalfwo->type = $value[0];
                            $totalfwo->total = $value[1];
                            $totalfwo->save();
                        }
                    } else {
                        //Update data
                        $totalfwo = CoachDailyReportSalesDetail::where('coach_daily_report_header_id', '=', $cek->id)
                            ->where('category_id', '=', $mst_cdr_category->id)
                            ->where('type', '=', $value[0])
                            ->first();
                        if (!empty($totalfwo)) {
                            $totalfwo->total = $value[1];
                            $totalfwo->save();
                        }
                    }
                } elseif ($row_counter == 30) { //Gloves
                    $mst_cdr_category = CoachDailyReportCategory::where('category', 'ILIKE', '%' . $value[0] . '%')->first();
                    if ($cekcdrheader == 0) {
                        //Add new data
                        if (!empty($mst_cdr_category)) {
                            $totalfwo = new CoachDailyReportSalesDetail;
                            $totalfwo->coach_daily_report_header_id = $cdr_header->id;
                            $totalfwo->category_id = $mst_cdr_category->id;
                            $totalfwo->type = $value[0];
                            $totalfwo->total = $value[1];
                            $totalfwo->save();
                        }
                    } else {
                        //Update data
                        $totalfwo = CoachDailyReportSalesDetail::where('coach_daily_report_header_id', '=', $cek->id)
                            ->where('category_id', '=', $mst_cdr_category->id)
                            ->where('type', '=', $value[0])
                            ->first();
                        if (!empty($totalfwo)) {
                            $totalfwo->total = $value[1];
                            $totalfwo->save();
                        }
                    }
                } elseif ($row_counter == 31) { //Locker
                    $mst_cdr_category = CoachDailyReportCategory::where('category', 'ILIKE', '%' . $value[0] . '%')->first();
                    if ($cekcdrheader == 0) {
                        //Add new data
                        if (!empty($mst_cdr_category)) {
                            $locker = new CoachDailyReportSalesDetail;
                            $locker->coach_daily_report_header_id = $cdr_header->id;
                            $locker->category_id = $mst_cdr_category->id;
                            $locker->type = $value[0];
                            $locker->total = $value[1];
                            $locker->save();
                        }
                    } else {
                        //Update data
                        $locker = CoachDailyReportSalesDetail::where('coach_daily_report_header_id', '=', $cek->id)
                            ->where('category_id', '=', $mst_cdr_category->id)
                            ->where('type', '=', $value[0])
                            ->first();
                        if (!empty($locker)) {
                            $locker->total = $value[1];
                            $locker->save();
                        }
                    }
                } elseif ($row_counter == 32) { //FL
                    $mst_cdr_category = CoachDailyReportCategory::where('category', 'ILIKE', '%' . $value[0] . '%')->first();
                    if ($cekcdrheader == 0) {
                        //Add new data
                        if (!empty($mst_cdr_category)) {
                            $fl = new CoachDailyReportSalesDetail;
                            $fl->coach_daily_report_header_id = $cdr_header->id;
                            $fl->category_id = $mst_cdr_category->id;
                            $fl->type = $value[0];
                            $fl->total = $value[1];
                            $fl->save();
                        }
                    } else {
                        //Update data
                        $fl = CoachDailyReportSalesDetail::where('coach_daily_report_header_id', '=', $cek->id)
                            ->where('category_id', '=', $mst_cdr_category->id)
                            ->where('type', '=', $value[0])
                            ->first();
                        if (!empty($fl)) {
                            $fl->total = $value[1];
                            $fl->save();
                        }
                    }
                } elseif ($row_counter == 33) { //N&N
                    $mst_cdr_category = CoachDailyReportCategory::where('category', 'ILIKE', '%' . $value[0] . '%')->first();
                    if ($cekcdrheader == 0) {
                        //Add new data
                        if (!empty($mst_cdr_category)) {
                            $nn = new CoachDailyReportSalesDetail;
                            $nn->coach_daily_report_header_id = $cdr_header->id;
                            $nn->category_id = $mst_cdr_category->id;
                            $nn->type = $value[0];
                            $nn->total = $value[1];
                            $nn->save();
                        }
                    } else {
                        //Update data
                        $nn = CoachDailyReportSalesDetail::where('coach_daily_report_header_id', '=', $cek->id)
                            ->where('category_id', '=', $mst_cdr_category->id)
                            ->where('type', '=', $value[0])
                            ->first();
                        if (!empty($nn)) {
                            $nn->total = $value[1];
                            $nn->save();
                        }
                    }
                }

            }
            Session::flash('flash_message', trans('messages.data_has_been_imported'));
            return redirect('cf-lite/report/staff-daily-report');
        } else {
            Session::flash('flash_message', trans('messages.no_data_has_been_imported'));
            return redirect('cf-lite/report/staff-daily-report');
        }
    }
}
