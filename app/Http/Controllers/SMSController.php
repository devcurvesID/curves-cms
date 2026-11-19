<?php

namespace App\Http\Controllers;

use Session;
use App\Http\Requests\ExportSMSRequest;
use App\Model\MasterData\PredefinedMessage;
use App\Model\Workout\Workout;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class SMSController extends Controller
{
    public function sms_main() {
        $predefined_messages = PredefinedMessage::pluck('message', 'message');

        return view('cf_lite.sms.sms-main', compact('predefined_messages'));
    }

    public function sms_message(ExportSMSRequest $request) {
        $total_wos = $request->total_wo;
        $text = $request->custom_message;
        $start_count = 2;

        if (count($request->member) > 0):
            Excel::create('SMS', function ($excel) use ($request, $start_count, $text) {
                $excel->sheet('Sheet1', function ($sheet) use ($request, $start_count, $text) {
                    $sheet->row(1, array(
                        'Sebutan',
                        'Nama',
                        'HP',
                        'GrupServis',
                        'JenisKeanggotaan',
                        'KodeAnggota',
                        'NoMesin',
                        'TglServis',
                        'Tgl',
                        'TglLahir',
                        'Alamat',
                        'Kota',
                        'KodePos',
                        'Agama',
                        'Keterangan',
                    ));

                    foreach ($request->member as $key => $value):
                        $member = User::find($value);
                        $hp = '';
                        $birth = '';
                        if (!empty($member->user_memberpersonal->cellphone)):
                            $hp = $member->user_memberpersonal->cellphone;
                        else:
                            $hp = $member->user_memberpersonal->phone;
                        endif;

                        if (!empty($member->user_memberpersonal->birth)):
                            $birth = Carbon::parse($member->user_memberpersonal->birth)->format('d-m-Y');
                        endif;

                        $sheet->row($start_count, array(
                            '',
                            $member->name,
                            $hp,
                            '',
                            $member->user_memberstatus->memberstatus_membershiptype->membership_type_name,
                            $member->user_memberpersonal->key_tag_id,
                            '',
                            '',
                            '',
                            $birth,
                            '',
                            '',
                            '',
                            '',
                            $text
                        ));

                        $start_count++;
                    endforeach;
                });
            })->download('xls');

//            foreach ($request->member as $key => $value):
//                $open_gate = 0;
//
//                if (($request->target == 'birthday') || ($request->target == 'free')):
//                    $open_gate = 1;
//                else:
//                    if ($request->target == 'wo1'):
//                        if ($total_wos[$key] > 0):
//                            $open_gate = 1;
//                        endif;
//                    elseif ($request->target == 'wo2'):
//                        if ($total_wos[$key] > 1):
//                            $open_gate = 1;
//                        endif;
//                    elseif ($request->target == 'wo3'):
//                        if ($total_wos[$key] > 2):
//                            $open_gate = 1;
//                        endif;
//                    endif;
//                endif;
//
//                if ($open_gate == 1):
//                    //Update Auto Number
//                    $temp_auto_number = AutoNumber::findOrFail('4');
//                    $msg_number = $temp_auto_number->current_number;
//                    $temp_auto_number->current_number = $temp_auto_number->current_number + 1;
//                    $temp_auto_number->save();
//
//                    //Get WA Number
//                    $member = User::findOrFail($key);
//                    $real_wa = $member->user_memberpersonal->memberpersonal_buddyreferrallead->wa_number;
//                    $wa_numbers = '62'.substr($real_wa, 1);
//
//                    //Send Message
//                    $response = Curl::to('https://www.waboxapp.com/api/send/chat')
//                        ->withData(array('token' => 'ba830f3e8cc80603e164bb46bd07dc015ab4826b5aff7', 'uid' => $sender, 'to' => $wa_numbers, 'custom_uid' => 'msg-'.sprintf('%08d', $msg_number), 'text' => $text))
//                        ->get();
//                endif;
//            endforeach;
        endif;

        Session::flash('flash_message', trans('messages.message_has_been_sent'));
        return redirect('cf-lite/sms');
    }

    //Ajax Controller
    public function sms_ajax(Request $request) {
        if (!empty($request)):
            $members = User::where('club_id', '=', Auth::user()->club_id)
                ->where('role_id', '=', '7')
                ->whereHas('user_memberpersonal', function ($q) {
                    $q->whereNotNull('cellphone')
                        ->orWhere(function ($r) {
                            $r->whereNotNull('phone');
                        });
                });

            if (($request->target == 'wo1') || ($request->target == 'wo2') || ($request->target == 'wo3')):
                $members = $members->get();

                foreach ($members as $member):
                    $wo = Workout::where('user_id', '=', $member->id)
                        ->whereDate('workout_date', '>=', Carbon::today()->modify('this week')->toDateString())
                        ->get()
                        ->count();

                    if ($request->target == 'wo1'):
                        if ($wo >= 1):
                            $member->setAttribute('total_wo', $wo);
                            $member->setAttribute('birthday', Carbon::parse($member->user_memberpersonal->birth)->format('d-m-Y'));
                        else:
                            $member->setAttribute('total_wo', '0');
                        endif;
                    elseif ($request->target == 'wo2'):
                        if ($wo >= 2):
                            $member->setAttribute('total_wo', $wo);
                            $member->setAttribute('birthday', Carbon::parse($member->user_memberpersonal->birth)->format('d-m-Y'));
                        else:
                            $member->setAttribute('total_wo', '0');
                        endif;
                    elseif ($request->target == 'wo3'):
                        if ($wo >= 3):
                            $member->setAttribute('total_wo', $wo);
                            $member->setAttribute('birthday', Carbon::parse($member->user_memberpersonal->birth)->format('d-m-Y'));
                        else:
                            $member->setAttribute('total_wo', '0');
                        endif;
                    endif;
                endforeach;
            elseif (($request->target == 'birthday') || ($request->target == 'free')):
                if ($request->target == 'birthday'):
                    $start_date = new Carbon('first day of this month');
                    $end_date = new Carbon('last day of this month');

                    $members = $members->whereHas('user_memberpersonal', function ($s) use ($start_date, $end_date) {
                        $s->whereDate('birth', '>=', Carbon::parse($start_date)->toDateString())
                            ->whereDate('birth', '<=', Carbon::parse($end_date)->toDateString());
                    })->get();
                elseif ($request->target == 'free'):
                    $members = $members->get();
                endif;

                foreach ($members as $member):
                    $wo = Workout::where('user_id', '=', $member->id)
                        ->whereDate('workout_date', '>=', Carbon::today()->modify('this week')->toDateString())
                        ->get()
                        ->count();

                    $member->setAttribute('total_wo', $wo);
                    $member->setAttribute('birthday', Carbon::parse($member->user_memberpersonal->birth)->format('d-m-Y'));
                endforeach;
            endif;

            return response()->json(array('members' => $members));
        endif;
    }
}
