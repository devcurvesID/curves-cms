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
use Illuminate\Support\Facades\DB;
use App\Model\MasterData\MembershipType;
use App\Model\Member\MemberStatus;
use App\Model\MasterData\Bank;
use App\Model\MasterData\PaymentMethod;
use App\Model\MasterData\Challenge;
use App\Model\Curves\Advertise\Publicity;
use App\Model\Member\CoachMember;
use App\Http\Requests\WMAddNewDataRequest;
use App\Model\Member\MemberHold;
use App\Model\Guest\BuddyReferralLead;
use App\Model\Product\ProductPayment;

class SyncDataToMongoDBController extends Controller
{
    //
    public function data_weighmeasuresbyuserid(Request $request){
        $user_id = (int) $request->input('user_id');
        if (empty($user_id)) {
            return response()->json([
                'error' => 'user id tidak boleh kosong'
            ], 404);
        }
        $measures_user = DB::table('weigh_measures')->where('user_id', $user_id)->get();
        return response()->json([
            'response'   => $measures_user
        ]);
    } 



    public function data_workoutbyuserid(Request $request){
        $user_id = (int) $request->input('user_id');
        $club_id = (int) $request->input('club_id');
        if (!empty($club_id)) {
            $offset = $request->query('offset', 0);
            $limit  = $request->query('limit', 10);
            $query = Workout::query()->where('club_id', $club_id);
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

        
        if (empty($user_id)) {
            return response()->json([
                'error' => 'user id tidak boleh kosong'
            ], 404);
        }
        $month = $request->input('month');
        $year = $request->input('year');
        if ($month && $year) {
            $start = Carbon::create($year, $month, 1)->startOfDay();
            $end   = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();
            $query_date = Workout::query();
            $query_date->where('user_id', $user_id)->get();
            $query_date->whereBetween('workout_date', [$start, $end]);
            $data_workoutdate = $query_date->get();
            return response()->json([
                'response'   => $data_workoutdate
            ]);
        }
        $workout_user = DB::table('workouts')->where('user_id', $user_id)->get();
        return response()->json([
            'response'   => $workout_user
        ]);
        
    } 


    public function data_memberships(){
        $membership = MembershipType::where('status', '=', 'Active')->orderBy('id', 'desc');
        $total = $membership->count();
        return response()->json([
            'response'   => $membership->get(),
            'total'  => $total,
        ]);
    }


    public function data_challenges(){
        $challenge = Challenge::where('status', '=', 'aktif')->orderBy('id', 'desc');
        $total = $challenge->count();
        return response()->json([
            'response'   => $challenge->get(),
            'total'  => $total,
        ]);
    }

    public function data_publicities(){
        $publicity = Publicity::where('status', '=', 'publish')->orderBy('id', 'desc');
        $total = $publicity->count();
        return response()->json([
            'response'   => $publicity->get(),
            'total'  => $total,
        ]);
    }



    public function data_memberstatus(Request $request){
        $user_id = (int) $request->input('user_id');
        if (empty($user_id)) {
            return response()->json([
                'error' => 'user id tidak boleh kosong'
            ], 404);
        }
        $memberstatus = DB::table('member_status')->where('user_id', $user_id)
        ->orderBy('created_at', 'asc')
        ->first();
        return response()->json([
            'response'   => $memberstatus
        ]);
    }

    public function data_memberpayment(Request $request){
        $payment_id = (int) $request->input('payment_id');
        if (!empty($payment_id)) {
            $data_payment = MemberPayment::findOrFail($payment_id)->toArray();
            return response()->json([
                'response'   => $data_payment
            ]);
        } 
        $user_id = (int) $request->input('user_id');
        if (empty($user_id)) {
            return response()->json([
                'error' => 'user id tidak boleh kosong'
            ], 404);
        }
        $member_payment = DB::table('member_payments')->where('user_id', $user_id)
        ->orderBy('id', 'desc')->get();
        return response()->json([
            'response'   => $member_payment
        ]);
    }

    public function data_bankclub(Request $request){
         $club_id = (int) $request->input('club_id');
         $bank_id = (int) $request->input('id');
         if (!empty($bank_id)) {
            $bank = Bank::where('id', '=', $bank_id)->first();
            return response()->json([
                'response'   => $bank,
            ]);
         }
        if (!empty($club_id)) {
            $bank_club = Bank::whereHas('bank_club', function ($q) use($request){
                $q->where('id', '=', $request->club_id);
            })->get();
            $total = $bank_club->count();
            return response()->json([
                'response'   => $bank_club,
                'total'  => $total,
            ]);
        }
        $banks = Bank::whereHas('bank_club')->get();
        $total = $banks->count();
        return response()->json([
                'response'   => $banks,
                'total'  => $total,
            ]);
       
    }

    public function data_paymentmethod(Request $request){
        $payment = PaymentMethod::all()->sortBy('id');
        $total = $payment->count();
        return response()->json([
            'response'   => $payment->values(),
            'total'  => $total,
        ]);
    }


    public function data_usernonmember(Request $request){
        // '$request->input('offset', 0)' => diakses menggunakan query
        $offset_val = (int) $request->input('offset', 0);
        $limit_val = (int) $request->input('limit', 10);
        $user_id = (int) $request->input('user_id');
        $phone  = $request->input('phone');
        $username =  $request->input('username');
         if (!empty($username)){
            $user_by_username = DB::table('usernonmember_v')->where('username', $username)->first();
            if ($user_by_username) {
                return response()->json([
                    'response'   => $user_by_username
                ]);
            } 
            $user_by_email = DB::table('usernonmember_v')->where('email', $username)->first();
            return response()->json([
                'response'   => $user_by_email
            ]);
            
        }
        if (!empty($phone)){
            $user_by_id = DB::table('usernonmember_v')->where('phone', $phone)->first();
            return response()->json([
                'response'   => $user_by_id
            ]);
        }
        if (!empty($user_id)) {
            $user_by_id = DB::table('usernonmember_v')->where('id', $user_id)->first();
            return response()->json([
                'response'   => $user_by_id
            ]);
        } 
        $query = DB::table('usernonmember_v');
        $total = $query->count();

        $member = $query->offset($offset_val)->limit($limit_val)->get();
        return response()->json([
            'response' => $member->values(),
            'total'  => $total,
        ]);
        
        
    }


      public function data_usermobilecurves(Request $request){
        // '$request->input('offset', 0)' => diakses menggunakan query
        $offset_val = (int) $request->input('offset', 0);
        $limit_val = (int) $request->input('limit', 10);
        $user_id = (int) $request->input('user_id');
        $phone  = $request->input('phone');
        $username =  $request->input('username');
         if (!empty($username)){
            $user_by_username = DB::table('usermobilecurves_v')->where('username', $username)->first();
            if ($user_by_username) {
                return response()->json([
                    'response'   => $user_by_username
                ]);
            } 
            $user_by_email = DB::table('usermobilecurves_v')->where('email', $username)->first();
            return response()->json([
                'response'   => $user_by_email
            ]);
            
        }
        if (!empty($phone)){
            $user_by_id = DB::table('usermobilecurves_v')->where('phone', $phone)->first();
            return response()->json([
                'response'   => $user_by_id
            ]);
        }
        if (!empty($user_id)) {
            $user_by_id = DB::table('usermobilecurves_v')->where('id', $user_id)->first();
            return response()->json([
                'response'   => $user_by_id
            ]);
        } 
        $query = DB::table('usermobilecurves_v');
        $total = $query->count();

        $member = $query->offset($offset_val)->limit($limit_val)->get();
        return response()->json([
            'response' => $member->values(),
            'total'  => $total,
        ]);
        
        
    }


    public function data_coachmember(Request $request){
        // '$request->input('offset', 0)' => diakses menggunakan query
        $offset_val = (int) $request->input('offset', 0);
        $limit_val = (int) $request->input('limit', 10);
        $coach_id = $request->input('coach_id');
        $member_id = $request->input('member_id');
        if (!empty($member_id)) {
           $membercoach = CoachMember::where('member_id', '=', $member_id)->first();
           return response()->json([
                'response'   => $membercoach
           ]);
        }
        if (!empty($coach_id)) {
           $list_coachmember = CoachMember::where('coach_id', '=', $coach_id)->orderBy('created_at', 'ASC')->get();
           $total = $list_coachmember->count();
           return response()->json([
                'response'   => $list_coachmember->values(),
                'total'  => $total,
           ]);
        }
        $query = DB::table('coach_members');
        $total = $query->count();
        $member = $query->offset($offset_val)->limit($limit_val)->get();
        return response()->json([
            'response' => $member->values(),
            'total'  => $total,
        ]);
        
        
    }


    public function store_weigh_measure(Request $request){
        $weigh_measure = new WeighMeasure;
        $weigh_measure->user_id = $request->user_id;
        $weigh_measure->wm_date = $request->wm_date;
        $weigh_measure->age = $request->age;
        $weigh_measure->height = $request->height;
        $weigh_measure->bp_high = $request->bp_high;
        $weigh_measure->bp_low = $request->bp_low;
        $weigh_measure->rhr = $request->rhr;
        $weigh_measure->chest = $request->chest;
        $weigh_measure->waist = $request->waist;
        $weigh_measure->abdomen = $request->abdomen;
        $weigh_measure->hip = $request->hip;
        $weigh_measure->thigh = $request->thigh;
        $weigh_measure->arm = $request->arm;
        $weigh_measure->weight = $request->weight;
        $weigh_measure->body_fat = $request->body_fat;
        $weigh_measure->muscle_mass = $request->muscle_mass;
        $weigh_measure->bone_mass = $request->bone_mass;
        $weigh_measure->bmi = $request->bmi;
        $weigh_measure->dci = $request->dci;
        $weigh_measure->metabolic = $request->metabolic;
        $weigh_measure->body_water = $request->body_water;
        $weigh_measure->visceral = $request->visceral;
        $weigh_measure->three_times_a_week = $request->three_times_a_week;
        $weigh_measure->total_wo_per_month = $request->total_wo_per_month;
        $weigh_measure->total_measurement = $request->chest + $request->waist + $request->abdomen + $request->hip + (2 * $request->thigh) + (2 * $request->arm);
        $weigh_measure->total_body_fat = $request->body_fat;
        $weigh_measure->total_hydration = $request->body_water;
        $weigh_measure->recommendation = $request->recommendation;
        $weigh_measure->measured_by = $request->measured_by;
        $weigh_measure->pro_workout = $request->pro_workout;
        $weigh_measure->save();
        $weigh_measureData = $weigh_measure->toArray();
        return response()->json([
            'response' => $weigh_measureData
        ]);
        
        
    }


      public function data_communitypartners(Request $request){
        $offset_val = (int) $request->input('offset', 0);
        $limit_val = (int) $request->input('limit', 10);
        $query = DB::table('communitypartners_v')->where('status', 'active');
        $total = $query->count();
        $community = $query->offset($offset_val)->limit($limit_val)->get();
        return response()->json([
            'response' => $community->values(),
            'total'  => $total,
        ]);
        
        
    }

   public function data_memberhold(Request $request)
    {
        $offset_val = max((int) $request->input('offset', 0), 0);
        $limit_val = max((int) $request->input('limit', 10), 1);

        $user_id = $request->input('user_id');
        $club_id = $request->input('club_id');

        // Jika mencari berdasarkan user_id
        if (!empty($user_id)) {
            $memberHold = MemberHold::where('user_id', $user_id)
                ->whereDate('thru_date', '>=', Carbon::today())
                ->where('status', 'OnHold')
                ->orderBy('thru_date', 'asc')
                ->first();

            return response()->json([
                'response' => $memberHold
            ]);
        }

        // Base query
        $query = MemberHold::whereDate('thru_date', '>=', Carbon::today())
            ->where('status', 'OnHold');

        // Filter berdasarkan club_id jika tersedia
        if (!empty($club_id)) {
            $query->where('club_id', $club_id);
        }
        $total = (clone $query)->count();
        $memberHolds = $query
            ->orderBy('thru_date', 'asc')
            ->offset($offset_val)
            ->limit($limit_val)
            ->get();

        return response()->json([
            'response' => $memberHolds,
            'total' => $total,
            'limit' => $limit_val,
            'offset' => $offset_val,
        ]);
    }




    public function data_listnonmemberstatus(Request $request, int $club_id)
    {
    $request->validate([
        'offset' => ['nullable', 'integer', 'min:0'],
        'limit'  => ['nullable', 'integer', 'min:1', 'max:100'],
        'status' => ['nullable', 'in:Stopped,Inactive'],
        'year'   => ['nullable', 'integer', 'min:1900', 'max:2100'],
        'name'   => ['nullable', 'string', 'max:255'],
    ]);

    $offset = (int) $request->input('offset', 0);
    $limit  = (int) $request->input('limit', 10);

    $status = $request->input('status');
    $year   = $request->input('year');
    $name   = trim((string) $request->input('name', ''));

    $query = DB::table('listnonmemberstatus_v')
        ->where('club_id', $club_id);

    if (!empty($status)) {
        $query->where('status', $status);
    } else {
        $query->whereIn('status', ['Stopped', 'Inactive']);
    }

    if (!empty($year)) {
        $startDate = Carbon::create((int) $year, 1, 1)->startOfDay();
        $endDate   = $startDate->copy()->addYear();

        $query->where('from_date', '>=', $startDate)
              ->where('from_date', '<', $endDate);
    }

    if ($name !== '') {
        $query->where('name', 'ILIKE', '%' . $name . '%');
    }

    $total = (clone $query)->count();

    $nonmembers = $query
        ->orderByDesc('from_date')
        ->orderBy('name')
        ->offset($offset)
        ->limit($limit)
        ->get();

    return response()->json([
        'response' => $nonmembers,
        'total'    => $total,
        'offset'   => $offset,
        'limit'    => $limit,
    ]);
 }


  public function data_listmemberholdbyclubid(Request $request, int $club_id)
    {
    $request->validate([
        'offset' => ['nullable', 'integer', 'min:0'],
        'limit'  => ['nullable', 'integer', 'min:1', 'max:100'],
        'name'   => ['nullable', 'string', 'max:255'],
    ]);

    $offset = (int) $request->input('offset', 0);
    $limit  = (int) $request->input('limit', 10);
    $name   = trim((string) $request->input('name', ''));

    $query = DB::table('listmemberhold_v')
        ->where('club_id', $club_id);

    if ($name !== '') {
        $query->where('name', 'ILIKE', '%' . $name . '%');
    }

    $total = (clone $query)->count();

    $memberholds = $query
        ->orderByDesc('from_date')
        ->orderBy('name')
        ->offset($offset)
        ->limit($limit)
        ->get();

    return response()->json([
        'response' => $memberholds,
        'total'    => $total,
        'offset'   => $offset,
        'limit'    => $limit,
    ]);
 }


   public function data_memberhasntworkoutyet(Request $request, int $club_id)
    {
    $request->validate([
        'offset' => ['nullable', 'integer', 'min:0'],
        'limit'  => ['nullable', 'integer', 'min:1', 'max:100'],
        'name'   => ['nullable', 'string', 'max:255'],
    ]);

    $offset = (int) $request->input('offset', 0);
    $limit  = (int) $request->input('limit', 10);
    $name   = trim((string) $request->input('name', ''));

    $query = DB::table('memberhasntworkoutyet_v')
        ->where('club_id', $club_id);

    if ($name !== '') {
        $query->where('name', 'ILIKE', '%' . $name . '%');
    }

    $total = (clone $query)->count();

    $memberholds = $query
        ->orderByDesc('joined')
        ->orderBy('name')
        ->offset($offset)
        ->limit($limit)
        ->get();

    return response()->json([
        'response' => $memberholds,
        'total'    => $total,
        'offset'   => $offset,
        'limit'    => $limit,
    ]);
 }




    public function data_lspa(Request $request, int $user_id)
    {


    $acquireDate = trim((string) $request->input('acquire_date'));
    $date = Carbon::createFromFormat('Y-m-d', $acquireDate)
            ->format('Y-m-d');
    $data = DB::table('log_staff_point_acquisitions as lspa')
        ->join('mst_points as mp', 'lspa.point_id', '=', 'mp.id')
        ->where('lspa.user_id', $user_id)
        ->whereDate('lspa.acquire_date', $date)
        ->select([
            'lspa.id',
            'lspa.acquire_date',
            'lspa.amount',
            'lspa.user_id',
            'lspa.point_id',
            'lspa.created_at',
            'lspa.updated_at',
            'mp.name as point_name',
            'mp.type as point_type',
            'mp.type_detail as point_type_detail',
        ])
        ->orderByDesc('lspa.acquire_date')
        ->get();
    
    return response()->json([
        'success' => true,
        'data' => $data,
    ]);
 }


     public function data_buddyReferralLead(int $staff_id, $created_at){
        $date = Carbon::createFromFormat('Y-m-d', $created_at)
            ->format('Y-m-d');
        $product_payment = BuddyReferralLead::where('staff_id', $staff_id)
             ->whereDate('created_at', $date)
            ->orderBy('id', 'desc');
        $total = $product_payment->count();
        return response()->json([
            'response'   => $buddy_refferal->get(),
            'total'  => $total,
        ]);
    }
    public function data_productPayment(int $cashier_id, $created_at){
        $date = Carbon::createFromFormat('Y-m-d', $created_at)
            ->format('Y-m-d');
        $product_payment = ProductPayment::where('cashier_id', $cashier_id)
             ->whereDate('created_at', $date)
            ->orderBy('id', 'desc');
        $total = $product_payment->count();
        return response()->json([
            'response'   => $product_payment->get(),
            'total'  => $total,
        ]);
    }

    public function data_productLocker(int $staff_id, $created_at){
        $date = Carbon::createFromFormat('Y-m-d', $created_at)
            ->format('Y-m-d');
        $locker_payment = LockerPayment::where('staff_id', $staff_id)
             ->whereDate('created_at', $date)
            ->orderBy('id', 'desc');
        $total = $locker_payment->count();
        return response()->json([
            'response'   => $locker_payment->get(),
            'total'  => $total,
        ]);
    }

    
}
