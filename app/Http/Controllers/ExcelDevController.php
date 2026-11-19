<?php

namespace App\Http\Controllers;

use App\Model\Appointment\GuestAppointment;
use App\Model\Appointment\GuestAppointmentStatus;
use App\Model\Club\ClubInfo;
use App\Model\Guest\BuddyReferralLead;
use App\Model\Guest\GuestDetail;
use App\Model\Guest\GuestFat;
use App\Model\Staff\StaffCode;
use App\User;
use Session;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ExcelDevController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('wm-only');
        $this->middleware('developer-only', ['only' => [
            'main',
            'read_excel',
        ]]);
    }

    public function main() {
        return view('developer.import-from-excel');
    }

    public function read_excel(Request $request) {
        if ($request->hasFile('data')):
            $excel = Excel::load($request->data, function ($reader) {
//                $reader->takeRows(3);
//                $reader->dd();
            })->toArray();

            foreach ($excel as $key => $value):
                //Buddy Referral Lead
                $brl = new BuddyReferralLead;
                $brl->fullname = $value['name'];
                $brl->phone = '0'.$value['phone'];

                if ($value['member_source_id'] == 1):
                    $brl->is_br = 'BR';
                else:
                    $brl->is_br = 'NotBR';
                endif;

                $brl->source_id = $value['member_source_id'];
                $brl->staff_id = $value['coach_id'];
                $brl->club_id = Auth::user()->club_id;
                $brl->status_gm = 'Guest';
                $brl->save();


                //Guest Detail
                $guest_detail = new GuestDetail;
                $guest_detail->birth = Carbon::parse($value['do_birth'])->format('Y-m-d');
                $guest_detail->occupation = $value['job'];

                if (strtolower($value['married_not']) == 'yes'):
                    $guest_detail->status = 'Married';
                elseif (strtolower($value['married_not']) == 'no'):
                    $guest_detail->status = 'Single';
                endif;

                $guest_detail->fa_date = Carbon::parse($value['date'])->format('Y-m-d');
                $brl->buddyreferrallead_guestdetail()->save($guest_detail);


                //Guest Fat
                $guest_fat = new GuestFat;
                $guest_fat->weight = $value['weight'];
                $guest_fat->body_fat = $value['body_fat'];
                $guest_fat->visceral = $value['visceral_fat'];
                $brl->buddyreferrallead_guestfat()->save($guest_fat);

                $datetime = Carbon::createFromFormat('Y-m-d H:i', Carbon::parse($value['date'])->format('Y-m-d H:i'), session('timezone'))->setTimezone('UTC');
                //Guest Appointment
                $guest_appointment = new GuestAppointment;
                $guest_appointment->guest_id = $brl->id;
                $guest_appointment->staff_id = $value['coach_id'];
                $guest_appointment->fa_status = 'NonFA';
                $guest_appointment->fa_date = Carbon::parse($datetime)->format('Y-m-d');
                $guest_appointment->fa_hour = Carbon::parse($datetime)->format('H:i');
                $guest_appointment->guest_status_after = 'Lead';
                $guest_appointment->club_id = Auth::user()->club_id;
                $guest_appointment->save();


                //Guest Appointment Status
                $guest_appointment_status = new GuestAppointmentStatus;
                $guest_appointment_status->status_id = '6';
                $guest_appointment_status->c_date = $datetime;
                $guest_appointment_status->s_date = $datetime;
                $guest_appointment_status->sa_date = $datetime;
                $guest_appointment->guestappointment_gastatus()->save($guest_appointment_status);
            endforeach;
            return 'Sukses';
        else:
            return 'Ga ada';
        endif;
    }

    public function staff_code(){
        return view('developer.import-staff-code');
    }

    public function import_staff_code(Request $request){
        if ($request->hasFile('data')){
            $path = $request->file('data')->getRealPath();
            $data = Excel::load($path, function ($reader){/*$reader->dd();*/})->get();

            if (!empty($data) && $data->count()){
                foreach ($data as $key => $value){
                    //Cek Code length
                    $length  = strlen($value->code);
                    $user = User::where('username', '=', $value->username)->first();

                    if ($length > 3){ //code max length > 3
                        $code = substr($value->code, 0, 3);

                        if (!empty($user->user_staffcode->code)){
                            $staff_code = StaffCode::where('user_id', '=', $user->id)->first();
                            $staff_code->code = strtoupper($code);
                            $user->user_staffcode()->save($staff_code);
                        } else {
                            //Insert to Table
                            $staff_code = new StaffCode();
                            $staff_code->user_id = $user->id;
                            $staff_code->code = strtoupper($code);
                            $user->user_staffcode()->save($staff_code);
                        }
                        Session::flash('flash_message', trans('messages.success_import_staff_code'));
                        return redirect('dev/import-staff-code');
                    } else { //code max length < 3
                        if (!empty($user->user_staffcode->code)){
                            $staff_code = StaffCode::where('user_id', '=', $user->id)->first();
                            $staff_code->code = strtoupper($value->code);
                            $user->user_staffcode()->save($staff_code);
                        } else {
                            //Insert to Table
                            $staff_code = new StaffCode();
                            $staff_code->user_id = $user->id;
                            $staff_code->code = strtoupper($value->code);
                            $user->user_staffcode()->save($staff_code);
                        }
                        Session::flash('flash_message', trans('messages.success_import_staff_code'));
                        return redirect('dev/import-staff-code');
                    }
                }
            }
//            Session::flash('error', trans('messages.error_import_staff_code'));
//            return redirect('dev/import-staff-code');
        }
    }

    public function member_report_to_excel(Request $request) {
        $clubs = ClubInfo::where('status', '=', 'Active')->orderBy('club_name', 'asc')->pluck('club_name', 'id');
        if (!empty($request->clubs)) {
            $member_data = User::selectRaw('
                users.name,
                member_personals.address,
                member_personals.cellphone,
                member_personals.phone,
                member_personals.flag,
                member_status.status as status,
                (SELECT from_date FROM log_member_memberships WHERE user_id = users.id ORDER BY from_date desc LIMIT 1) as join_date,
                (SELECT COUNT(user_id) FROM workouts WHERE user_id = users.id) as total_wo,
                membership_types.membership_type_name
            ')
                ->join('member_personals', 'users.id', '=', 'member_personals.user_id')
                ->join('member_status', 'users.id', '=', 'member_status.user_id')
                ->join('membership_types', 'member_status.membership_type_id', '=', 'membership_types.id')
                ->where('club_id', '=', $request->clubs)
                ->where('role_id', '=', '7')
                ->orderBy('name', 'asc')
                ->get();
            return view('developer.export-data-member', compact('clubs', 'member_data'));
        } else {
            return view('developer.export-data-member', compact('clubs'));
        }
    }
}