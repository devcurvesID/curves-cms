<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Model\Member\MemberMessageNote;
use App\Model\Payment\MemberPayment;
use App\Model\WM\WeighMeasure;
use App\Model\WM\Xfactor;
use App\Model\Workout\Workout;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Model\Club\ClubInfo;
use App\Model\Role\Role;
use Illuminate\Support\Facades\DB;



class UserController extends Controller
{
    public $successStatus = 200;

    public function user_detail() {
        $user = Auth::user();

//        $another = User::find(17);
//        $another->user_memberpersonal;

        return response()->json(['success' => $user], $this->successStatus);
    }

    //WORKOUT
    public function workout() {
        $user = Auth::user();

        $list_wo = Workout::where('user_id', '=', $user->id)->orderBy('workout_date', 'ASC')->get();
        $data = [];

        if (count($list_wo) > 0):
            $counter = 0;
            $temp_date = '';
            $temp_total = 0;
            foreach ($list_wo as $wo):
                if ($temp_date != Carbon::parse($wo->workout_date)->format('F Y')):
                    if ($counter > 0):
                        array_push($data, ['date' => $temp_date, 'total' => $temp_total]);
                    endif;
                    $temp_date = Carbon::parse($wo->workout_date)->format('F Y');
                    $temp_total = 1;
                else:
                    $temp_total++;
                endif;
                $counter++;
            endforeach;

            if (($counter == count($list_wo)) && ($temp_total > 0)):
                array_push($data, ['date' => $temp_date, 'total' => $temp_total]);
            endif;
        endif;

        return response()->json(['data' => $data], $this->successStatus);
    }

    public function workout_detail($date) {
        $from_date = new Carbon('first day of '.$date);
        $thru_date = new Carbon('last day of '.$date);

        $user = Auth::user();

        $workouts = Workout::where('user_id', '=', $user->id)
            ->whereDate('workout_date', '>=', Carbon::parse($from_date)->toDateString())
            ->whereDate('workout_date', '<=', Carbon::parse($thru_date)->toDateString())
            ->get();

        $count = 0;
		
		foreach ($workouts as $workout):
            $workout->workout_club->club_name;
            $count += 1;
        endforeach;

        return response()->json(['data' => $workouts,'count' => $count], $this->successStatus);
    }

    //WM
    public function wm() {
        $user = Auth::user();

        $wms = WeighMeasure::where('user_id', '=', $user->id)->orderBy('wm_date', 'DESC')->get();

        $xfactors = Xfactor::where('user_id', '=', $user->id)->get();

        return response()->json(['wms' => $wms, 'xfactors' => $xfactors], $this->successStatus);
    }

    public function wm_detail($id) {
        $user = Auth::user();

        $wm = WeighMeasure::find($id);

        return response()->json(['wm' => $wm], $this->successStatus);
    }

    //USER'S PROFILE
    public function profile() {
        $user = Auth::user();
        $user->user_memberpersonal;
        $user->user_memberstatus;
        $user->user_club;
        $totalWO = Workout::where('user_id','=',$user->id)->count();

        return response()->json(['user' => $user,'totalWO' => $totalWO], $this->successStatus);
    }
	
	public function qrcode() {
        $user = Auth::user();

        return response()->json(['qr' => $user->user_memberpersonal->key_tag_id], $this->successStatus);
    }

    public function updatePhone() {
        if (!empty(request('phone_number'))):
            $user = User::findOrFail(3);

            $member_personal = $user->user_memberpersonal;
            $member_personal->phone = request('phone_number');
            $user->user_memberpersonal()->save($member_personal);

            return response()->json(['success' => true], $this->successStatus);
        else:
            return response()->json(['success' => false, 'message' => 'yey'], $this->successStatus);
        endif;
    }
	
	public function updatePhoto() {
        if (!empty(request('base64'))):
            $photo = request('base64');

            //Upload Photo
            $photo_name = date('YmdHis') . ".jpeg";
            $photo = base64_decode($photo);
            file_put_contents(public_path('images/member_photos').'/'.$photo_name, $photo);

            $user = Auth::user();
            $member_personal = $user->user_memberpersonal;
            $member_personal->photo = $photo_name;
            $user->user_memberpersonal()->save($member_personal);

            return response()->json(['success' => 'berhasil'], $this->successStatus);
        else:
            return response()->json(['success' => 'gagal'], $this->successStatus);
        endif;
    }
	
	//Message
    public function message() {
        $user = Auth::user();

        $messages = MemberMessageNote::where('user_id', '=', $user->id)
            ->whereDate('from_date', '<=', Carbon::now()->toDateString())
            ->whereDate('thru_date', '>=', Carbon::now()->toDateString())
            ->orderBy('from_date', 'ASC')
            ->get();

        return response()->json(['data' => $messages], $this->successStatus);
    }

    //Payment
    public function debt() {
        $user = Auth::user();

        $debts = MemberPayment::select('id', 'payment_date', 'rest_of_bill','payment_status')
            ->where('user_id', '=', $user->id)
            ->where('payment_status', '<>', 'Paid')
            ->where('rest_of_bill', '<>', '0')
            ->orderBy('payment_date', 'ASC')
            ->get();
			
		foreach ($debts as $debt):
            $debt->rest_of_bill = number_format($debt->rest_of_bill,2,',','.');
            $debt->setAttribute('month', Carbon::parse($debt->payment_date)->format('F'));
        endforeach;

        return response()->json(['debts' => $debts], $this->successStatus);
    }

    public function payment() {
        $user = Auth::user();

        $payments = MemberPayment::select('id', 'payment_number', 'payment_date', 'amount_paid', 'payment_method_id','payment_status')
            ->where('user_id', '=', $user->id)
            ->where('payment_status', '<>', 'Unpaid')
            ->whereNotNull('payment_number')
            ->orderBy('payment_date', 'DESC')
            ->get();

        if (count($payments) > 0):
            foreach ($payments as $payment):
				$payment->amount_paid = number_format($payment->amount_paid,2,',','.');
                if (!empty($payment->memberpayment_paymentmethod)):
                    $payment->setAttribute('payment_method', $payment->memberpayment_paymentmethod->payment_method);
                endif;
            endforeach;
        endif;

        return response()->json(['payments' => $payments], $this->successStatus);
    }


    // JONVITER 
    public function getUsers(Request $request){

        $user_id = $request->query('id');
        if (!empty($user_id)) {
            # code...
            // $user_by_id =  User::where('id', '=', $user_id)->get();
            $user_by_id = DB::table('users')->where('id', $user_id)->first();
            return response()->json([
                'response'   => $user_by_id
            ]);

        } else {
            $offset = $request->query('offset', 0);
            $limit  = $request->query('limit', 10);
            $query = User::query();
            $total = $query->count();
            $users = $query->offset($offset)
                ->limit($limit)
                ->get();
            return response()->json([
                'offset' => (int) $offset,
                'limit'  => (int) $limit,
                'total'  => $total,
                'response'   => $users
            ]);
        }
       

    }

    public function member_club(Request $request, $club_id){
        // '$id' tidak otomatis masuk ke 'whereHas' harus menggunakan 'use'
        $offset_val = (int) $request->input('offset', 0);
        $limit_val = (int) $request->input('limit', 10);
        $member = User::where('role_id', '=', '7')->whereHas('user_club', function ($q) use ($club_id){
            $q->where('id', '=', $club_id);
        })->orderBy('id', 'asc')->offset($offset_val)->limit($limit_val)->get();
        $total = $member->count();

        return response()->json([
            'response' => $member,
            'total'  => $total,
        ]);
    }

    public function data_usermember(Request $request){
        // '$request->input('offset', 0)' => diakses menggunakan query
        $offset_val = (int) $request->input('offset', 0);
        $limit_val = (int) $request->input('limit', 10);
        $user_id = (int) $request->input('user_id');
        $phone  = $request->input('phone');
        $username =  $request->input('username');
         if (!empty($username)){
            $user_by_username = DB::table('usermember_v')->where('username', $username)->first();
            if ($user_by_username) {
                return response()->json([
                    'response'   => $user_by_username
                ]);
            } 
            $user_by_email = DB::table('usermember_v')->where('email', $username)->first();
            return response()->json([
                'response'   => $user_by_email
            ]);
            
        }
        if (!empty($phone)){
            $user_by_id = DB::table('usermember_v')->where('phone', $phone)->first();
            return response()->json([
                'response'   => $user_by_id
            ]);
        }
        if (!empty($user_id)) {
            $user_by_id = DB::table('usermember_v')->where('id', $user_id)->first();
            return response()->json([
                'response'   => $user_by_id
            ]);
        } 
        $query = DB::table('usermember_v');
        $total = $query->count();

        $member = $query->offset($offset_val)->limit($limit_val)->get();
        return response()->json([
            'response' => $member->values(),
            'total'  => $total,
        ]);
        
        
    }

    public function data_clubs(){
        $clubs = ClubInfo::all()->sortBy('name');
        $total = $clubs->count();
        return response()->json([
            'response'   => $clubs->values(),
            'total'  => $total,
        ]);
    }

    public function data_roles(){
        $roles = Role::orderBy('role', 'asc')->get();
        $total = $roles->count();
        // 'values()' => untuk membuat array agar di parse sesuai format JSON
        return response()->json([
            'response'   => $roles->values(),
            'total'  => $total,
        ]);
    }

}
