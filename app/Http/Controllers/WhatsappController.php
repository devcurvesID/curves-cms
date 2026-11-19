<?php

namespace App\Http\Controllers;

use App\Model\Club\ClubInfo;
use App\Model\Guest\BuddyReferralLead;
//use Maatwebsite\Excel\Facades\Excel;
use Session;
use App\AutoNumber;
use App\Http\Requests\SendMessageRequest;
use App\Model\MasterData\PredefinedMessage;
use App\Model\Workout\Workout;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ixudra\Curl\Facades\Curl;

use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel;



class WhatsappController extends Controller
{

    public function sendtest() {
        $text = 'Selamat bergabung dengan Curves.Kini kami hadir di Indonesia.';

        $response = Curl::to('https://www.waboxapp.com/api/send/chat')
            ->withData(array('token' => '762cf34833380449380c260df3847a61643712312020b', 'uid' => '6282117451293', 'to' => '6282218392157', 'custom_uid' => 'msg-6666', 'text' => $text))
            ->get();

            /* $response = 'https://www.waboxapp.com/api/send/chat?token=762cf34833380449380c260df3847a61643712312020b&uid=628112266977&to=6282117451293&custom_uid=msg-1331&text=Selamat+bergabung+dengan+Curves.Kini+kami+hadir+di+Indonesia.';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $response);

            $buffer = curl_exec($ch);
            curl_close($ch);
            if (empty($buffer)){
                print "Nothing returned from url.<p>";
                die();
            }
            else{
                print $buffer;

            } */

        dd($response);
        die();

    }

    public function tester(Request $request) {
//        $text = 'Selamat bergabung dengan Curves.Kini kami hadir di Indonesia.';
        if (!empty($request->text)):
            $text = $request->text;

            $response = Curl::to('https://www.waboxapp.com/api/send/chat')
                ->withData(array('token' => '762cf34833380449380c260df3847a61643712312020b', 'uid' => '6289681649596', 'to' => '6287826558555', 'custom_uid' => 'msg-6369', 'text' => $text))
                ->get();

//            $response = Curl::to('https://www.waboxapp.com/api/send/chat')
//                ->withData(array('token' => '762cf34833380449380c260df3847a61643712312020b', 'uid' => '6289681649596', 'to' => '628121497107', 'custom_uid' => 'msg-6368', 'text' => $text))
//                ->get();

            return view('cf_lite.cmd', compact('response'));
        else:
//            $ack = Curl::to('http://localhost/cfindonesialite/public/whatsapp')->post();
            return view('cf_lite.cmd');
        endif;
    }

    public function whatsapp_main()
    {
        $predefined_messages = PredefinedMessage::pluck('message', 'message');

        return view('cf_lite.whatsapp.whatsapp-main', compact('predefined_messages'));
    }

    public function send_message(SendMessageRequest $request)
    {
        $total_wos = $request->total_wo;
        $text = $request->custom_message;
        $sender = '62' . substr($request->sender, 1);

        if (count($request->member) > 0):
            foreach ($request->member as $key => $value):
                $open_gate = 0;

                if ($request->target == 'wo1'):
                    if ($total_wos[$key] > 0):
                        $open_gate = 1;
                    endif;
                elseif ($request->target == 'wo2'):
                    if ($total_wos[$key] > 1):
                        $open_gate = 1;
                    endif;
                elseif ($request->target == 'wo3'):
                    if ($total_wos[$key] > 2):
                        $open_gate = 1;
                    endif;
                else:
                    $open_gate = 1;
                endif;

                if ($open_gate == 1):
                    //Update Auto Number
                    $temp_auto_number = AutoNumber::findOrFail('4');
                    $msg_number = $temp_auto_number->current_number;
                    $temp_auto_number->current_number = $temp_auto_number->current_number + 1;
                    $temp_auto_number->save();

                    //Get WA Number
                    $member = User::findOrFail($key);
                    $real_wa = $member->user_memberpersonal->memberpersonal_buddyreferrallead->wa_number;
                    $wa_numbers = '62'.substr($real_wa, 1);

                    //Send Message
                    $response = Curl::to('https://www.waboxapp.com/api/send/chat')
                        ->withData(array('token' => '762cf34833380449380c260df3847a61643712312020b', 'uid' => $sender, 'to' => $wa_numbers, 'custom_uid' => 'msg-'.sprintf('%08d', $msg_number), 'text' => $text))
                        ->get();
                endif;
            endforeach;
        endif;

        Session::flash('flash_message', trans('messages.message_has_been_sent'));
        return redirect('cf-lite/whatsapp');
    }

    //Ajax Controller
    public function whatsapp_ajax(Request $request) {
        if (!empty($request)):
            $date = Carbon::today()->firstOfMonth();
            $members = User::where('club_id', '=', Auth::user()->club_id)
                ->where('role_id', '=', '7')
                ->whereHas('user_memberpersonal', function ($q) {
                    $q->whereHas('memberpersonal_buddyreferrallead', function ($r) {
                        $r->whereNotNull('wa_number');
                    });
                })->whereHas('user_memberstatus',function($q){
                    $q->where('ignore_wa','=',false);
                });

            if (($request->target == 'wo1') || ($request->target == 'wo2') || ($request->target == 'wo3')):
                $members = $members->get();

                foreach ($members as $member):
                    $wo = Workout::where('user_id', '=', $member->id)
                        ->whereDate('workout_date', '>=', Carbon::today()->modify('this week')->toDateString())
                        ->get()
                        ->count();

                    if ($request->target == 'wo1'):
                        if ($wo == 1): //Diganti dari >=
                            $member->setAttribute('total_wo', $wo);
                            $member->setAttribute('birthday', Carbon::parse($member->user_memberpersonal->birth)->format('d-m-Y'));
                        else:
                            $member->setAttribute('total_wo', '0');
                        endif;
                    elseif ($request->target == 'wo2'):
                        if ($wo == 2):
                            $member->setAttribute('total_wo', $wo);
                            $member->setAttribute('birthday', Carbon::parse($member->user_memberpersonal->birth)->format('d-m-Y'));
                        else:
                            $member->setAttribute('total_wo', '0');
                        endif;
                    elseif ($request->target == 'wo3'):
                        if ($wo == 3):
                            $member->setAttribute('total_wo', $wo);
                            $member->setAttribute('birthday', Carbon::parse($member->user_memberpersonal->birth)->format('d-m-Y'));
                        else:
                            $member->setAttribute('total_wo', '0');
                        endif;
                    endif;
                endforeach;
            elseif (($request->target == 'birthday') || ($request->target == 'free') || ($request->target == 'cancel')):
                if ($request->target == 'birthday'):
                    $start_date = new Carbon('first day of this month');
                    $end_date = new Carbon('last day of this month');

                    $members = $members->whereHas('user_memberpersonal', function ($s) use ($start_date, $end_date) {
                        $s->whereDate('birth', '>=', Carbon::parse($start_date)->toDateString())
                            ->whereDate('birth', '<=', Carbon::parse($end_date)->toDateString());
                    })->whereHas('user_memberstatus', function ($w) {
                        $w->where('status', '=', 'Active')
                            ->orWhere('status', '=', 'Hold');
                    })->get();
                elseif ($request->target == 'free'):
                    $members = $members->whereHas('user_memberstatus', function ($w) {
                        $w->where('status', '=', 'Active')
                            ->orWhere('status', '=', 'Hold');
                    })->get();
                elseif ($request->target == 'cancel'):
                    $members = $members->whereHas('user_memberstatus', function ($w) {
                        $w->where('status', '=', 'Inactive')
                            ->orWhere('status', '=', 'Stopped');
                    })->get();
                endif;

                foreach ($members as $member):
                    $wo = Workout::where('user_id', '=', $member->id)
                        ->whereDate('workout_date', '>=', Carbon::today()->modify('this week')->toDateString())
                        ->get()
                        ->count();

                    $member->setAttribute('total_wo', $wo);
                    $member->setAttribute('birthday', Carbon::parse($member->user_memberpersonal->birth)->format('d-m-Y'));
                endforeach;
            elseif ($request->target == 'expire'):
                if(!empty($request->month)){
                    $date = Carbon::parse('01-'.$request->month)->firstOfMonth();
                }
                $members = $members->whereHas('user_memberstatus', function ($w) use ($date) {
                    $w->whereRaw('thru_date BETWEEN \''.Carbon::parse($date)->format('Y-m-d').'\' AND \''.Carbon::parse($date->lastOfMonth())->format('Y-m-d').'\'');
                })->where( function($x) use ($date) {
                    $x->whereHas('user_continuemembership', function ($y) use ($date) {
                        $y->whereDate('from_date','<','\''.Carbon::parse($date)->format('Y-m-d').'\'');
                    })->doesntHave('user_continuemembership','or')->get();
                })->get();

                foreach ($members as $member):
                    $wo = Workout::where('user_id', '=', $member->id)
                        ->whereDate('workout_date', '>=', Carbon::today()->modify('this week')->toDateString())
                        ->get()
                        ->count();

                    $member->setAttribute('total_wo', $wo);
                    $member->setAttribute('birthday', Carbon::parse($member->user_memberpersonal->birth)->format('d-m-Y'));
                endforeach;
            elseif (in_array($request->target, ['A','B','C','D','E'])):

                $members = $members->whereHas('user_memberstatus', function($q){
                    $q->where('status','Active');
                })->whereHas('user_memberpersonal', function ($w) use ($request) {
                    $w->where('flag','=',$request->target);
                })->get();

                foreach ($members as $member) :
                    $wo = Workout::where('user_id', '=', $member->id)
                        ->whereDate('workout_date', '>=', Carbon::today()->modify('this week')->toDateString())
                        ->get()
                        ->count();

                    $member->setAttribute('total_wo', $wo);
                    $member->setAttribute('birthday', Carbon::parse($member->user_memberpersonal->birth)->format('d-m-Y'));
                endforeach;
            endif;

            return response()->json(array('members' => $members, 'date' => $date));
        endif;
    }

    //No Sale Blast Message
    public function nosale_main() {
        $club = ClubInfo::findOrFail(Auth::user()->club_id);

        $clubs = ClubInfo::orderBy('club_name', 'asc')->pluck('club_name', 'id');

        $predefined_messages = PredefinedMessage::pluck('message', 'message');

        return view('cf_lite.whatsapp.whatsapp-no-sale', compact('club', 'clubs', 'predefined_messages'));
    }

    public function send_nosale_blast(Request $request) {
        $this->validate($request, [
            'custom_message' => 'required|string',
            'clubs' => 'required|integer|exists:club_infos,id',
            'sender' => 'required|string',
        ]);

        if ($request->hasFile('leads')):
            //$path = $request->file('leads')->getRealPath();
            //$data = Excel::load($path, function ($reader){/*$reader->dd();*/})->get();
            $path = $request->file('leads')->store('files');
            $data = Excel::toArray([], $path);
            //var_dump(Excel::class);

            $data = array_filter(array_map('array_filter', $data[0]));
            //dd($data);
            //die();
            if (!empty($data)):
                array_shift($data);

                //Send Message
                $text = $request->custom_message;
                $sender = '62'.substr($request->sender, 1);

                foreach ($data as $key => $value):

                    if (!empty($value[2])):
                        //Update Auto Number
                        $temp_auto_number = AutoNumber::findOrFail('4');
                        $msg_number = $temp_auto_number->current_number;
                        $temp_auto_number->current_number = $temp_auto_number->current_number + 1;
                        $temp_auto_number->save();

                        //Get WA Number
                        $wa_numbers = '62'.substr($value[2], 1);
                        //var_dump( $wa_numbers );
                        //print "<br>==========<br>";

                        //Send Message
                        $response = Curl::to('https://www.waboxapp.com/api/send/chat')
                            ->withData(array('token' => '762cf34833380449380c260df3847a61643712312020b', 'uid' => $sender, 'to' => $wa_numbers, 'custom_uid' => 'msg-'.sprintf('%08d', $msg_number), 'text' => $text))
                            ->get();
                        var_dump($response);
                    endif;
                endforeach;
                //die();

            //if (!empty($data) && $data->count()):
                //Validation Check
//                foreach ($data as $key => $value):
//                    $imported_data = new Request([
//                        'nama' => $value->nama,
//                        'wa' => $value->wa,
//                    ]);
//
//                    $this->validate($imported_data, [
//                        'nama' => 'required|string',
//                        'wa' => 'required|string',
//                    ]);
//                endforeach;

                //Send Message
                /* $text = $request->custom_message;
                $sender = '62'.substr($request->sender, 1);

                foreach ($data as $key => $value):
                    if (!empty($value->wa)):
                        //Update Auto Number
                        $temp_auto_number = AutoNumber::findOrFail('4');
                        $msg_number = $temp_auto_number->current_number;
                        $temp_auto_number->current_number = $temp_auto_number->current_number + 1;
                        $temp_auto_number->save();

                        //Get WA Number
                        $wa_numbers = '62'.substr($value->wa, 1);

                        //Send Message
                        $response = Curl::to('https://www.waboxapp.com/api/send/chat')
                            ->withData(array('token' => '762cf34833380449380c260df3847a61643712312020b', 'uid' => $sender, 'to' => $wa_numbers, 'custom_uid' => 'msg-'.sprintf('%08d', $msg_number), 'text' => $text))
                            ->get();
                    endif;
                endforeach; */

                Session::flash('flash_message', trans('messages.message_has_been_sent'));
                return redirect('cf-lite/whatsapp/no-sale');
            endif;
        endif;
    }

    public function nosale_ajax(Request $request) {
        if (!empty($request)):
            $club = ClubInfo::findOrFail($request->club);

            return response()->json($club);
        endif;
    }
}
