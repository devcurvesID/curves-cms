<?php

namespace App\Http\Controllers;

use App\Model\MasterData\Bank;
use Illuminate\Support\Facades\DB;
use Session;
use App\User;
use App\AutoNumber;
use App\Model\Club\ClubInfo;
use App\Model\Locker\MemberLocker;
use App\Model\Locker\LockerPayment;
use App\Model\Locker\LockerInfo;
use App\Model\MasterData\Locker;
use App\Model\MasterData\PaymentMethod;
use App\Http\Requests\AddBulkLockerInfoRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use App\AccurateAuth;
use App\Helpers\Accurate;
use stdClass;
use Storage;


require_once __DIR__.'/../../WebClientPrint/WebClientPrint.php';
use Neodynamic\SDK\Web\WebClientPrint;

class LockerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('wm-only');
    }

    public function locker_list()
    {
        $lockers = Locker::where('club_id','=',Auth::user()->club_id)->paginate(10);

        return view('cf_lite.locker.locker-list',compact('lockers'));
    }

    public function locker_list_filter(Request $request)
    {
        $lockers = Locker::whereNotNull('name');

        $lockers->where('name', 'LIKE', '%'.$request->locker_name.'%');

        $lockers = $lockers->orderBy('id', 'asc')->paginate(100);

        return view('cf_lite.locker.locker-list', compact('lockers'));
    }

    public function store_locker(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'duration' => 'required|integer',
            'duration_type' => 'required',
            'price' => 'required|integer',
        ]);

        if($request->addoredit == 'add'){
            $locker = new Locker;
            $locker->name = $request->name;
            $locker->duration = $request->duration;
            $locker->duration_type = $request->duration_type;
            $locker->price = $request->price;
            $locker->club_id = Auth::user()->club_id;
            $locker->save();

            Session::flash('flash_message', trans('messages.locker_has_been_added'));

            return redirect('cf-lite/locker/locker-list');
        }else if($request->addoredit == 'edit'){
            $locker = Locker::findOrFail($request->editid);
            $locker->name = $request->name;
            $locker->duration = $request->duration;
            $locker->duration_type = $request->duration_type;
            $locker->price = $request->price;

            $locker->save();

            Session::flash('flash_message', trans('messages.locker_has_been_updated'));

            return redirect('cf-lite/locker/locker-list');
        }
    }

    public function delete_locker($id, Request $request){
        $locker = LockerInfo::findOrFail($id);
        $memberlocker = MemberLocker::where('locker_info_id','=',$id)->get();
        if(count($memberlocker) == 0){
            $locker->delete();
        }else{
            Session::flash('flash_message', trans('messages.cant_delete_because_exist'));
        }

        return redirect('cf-lite/locker/locker-info-list/'.Auth::user()->club_id);
    }

    //SALES LOCKER
    public function sales_locker_list($id) {
        $lockers = Locker::where('club_id','=',Auth::user()->club_id)->get();
        $memberlockers = MemberLocker::where('locker_info_id','=',$id)->whereDate('thru_date','>=',Carbon::now())->orderBy('from_date','asc')->get();
        $lockerinfo_id = $id;

        return view('cf_lite.locker.sales-locker.locker-sale-list', compact('lockers','memberlockers','lockerinfo_id'));
    }

    public function locker_sale_list_filtered(Request $request)
    {
        $id = $request->lockerinfo_id;

        $lockers = Locker::whereNotNull('name');
        $memberlockers = MemberLocker::where('locker_info_id','=',$id)->whereDate('thru_date','>=',Carbon::now())->orderBy('from_date','asc')->get();
        $lockerinfo_id = $request->lockerinfo_id;

        if ($request->duration_type != 'All'):
            $duration_type = $request->duration_type;
            $lockers = $lockers->where('duration_type','=',$duration_type);
        endif;

        if (!empty($request->s)):
            $search = $request->s;
            $lockers = $lockers->where('name', 'LIKE', '%'.ucwords($search).'%');
        endif;

        $lockers = $lockers->get();

        return view('cf_lite.locker.sales-locker.locker-sale-list', compact('lockers','memberlockers','lockerinfo_id'));
    }

    public function locker_cart(Request $request)
    {
        $locker = Locker::findOrFail($request->id);
        $members = User::where('role_id','=','7')->get();
        $qty = $request->qty;
        $lockerinfo_id = $request->lockerinfo_id;

        return view('cf_lite.locker.sales-locker.locker-cart', compact('locker','members','qty','lockerinfo_id'));
    }

    public function cart_payment(Request $request) {
        $date = Carbon::now();
        $locker = Locker::findOrFail($request->locker_id);
        $qty = $request->qty;
        $member = User::findOrFail($request->member);
        $payment_methods = PaymentMethod::availableForPayment()->pluck('payment_method', 'id');
        $memberslocker = MemberLocker::where('user_id','=',$request->member)->whereRaw('? BETWEEN from_date AND thru_date',[$date])->get();
        $staff = User::where('role_id','<>','7')->orderBy('name','asc')->pluck('name','id');
        $lockerinfo_id = $request->lockerinfo_id;

        $club_id = Auth::user()->club_id;
        /*$taxes = Taxtype::whereHas('taxtype_taxconfig', function($q) use ($club_id) {
            $q->where('club_id', '=', $club_id)
                ->where('thru_date', null);
        })->pluck('tax_type', 'id');*/

        //Payment Number added 190218
        $temp_auto_number = AutoNumber::findOrFail('1');

        return view('cf_lite.payments.locker-payment-detail', compact('payment_methods', 'temp_auto_number', 'club_id','locker','qty','member','memberslocker','staff','lockerinfo_id'/*, 'taxes'*/));
    }

    public function store_locker_payment(Request $request) {

        $this->validate($request, [
            'staff' => 'required',
            'neworrenew' => 'required',
            'payment_method' => 'required',
            'pay' => 'required|numeric',
            'start_date' => 'required_if:neworrenew,0',
            'payment_date' => 'required',
        ]);

        //Update Auto Number
        $temp_auto_number = AutoNumber::findOrFail('1');
        $payment_number = $temp_auto_number->current_number;

        $temp_auto_number->current_number = $temp_auto_number->current_number + 1;
        $temp_auto_number->save();

        //if the customer wants to rent a new locker
        if($request->neworrenew == 0){
            $memberlocker = new MemberLocker;
            $memberlocker->user_id = $request->user_id;
            $memberlocker->from_date = $request->start_date;

            //check what package did the customer bought
            $choosenlocker = Locker::findOrFail($request->locker);
            if($choosenlocker->duration_type=='monthly'){
                $memberlocker->thru_date = Carbon::parse($request->start_date)->addMonths($choosenlocker->duration * $request->qty);
            }else if($choosenlocker->duration_type=='daily'){
                $memberlocker->thru_date = Carbon::parse($request->start_date)->addDays($choosenlocker->duration * $request->qty);
            }else if($choosenlocker->duration_type=='yearly'){
                $memberlocker->thru_date = Carbon::parse($request->start_date)->addYears($choosenlocker->duration * $request->qty);
            }
            $memberlocker->locker_info_id = $request->lockerinfo_id;
            $memberlocker->is_key_returned = 'No';
            $memberlocker->save();

            //Add to locker payment
            $locker_payment = new LockerPayment;
            $locker_payment->memberlocker_id = $memberlocker->id;
            $locker_payment->locker_id = $request->locker;
            $locker_payment->payment_number = sprintf('%08d', $payment_number).$request->payment_number;
            $locker_payment->staff_id = $request->staff;
            $locker_payment->price = $choosenlocker->price;
            $locker_payment->quantity = $request->qty;
            $locker_payment->subtotal = round(10 / 11 * $request->input('billing-total'));
            $locker_payment->pay = $request->pay;
            $locker_payment->paymentmethod_id = $request->payment_method;
            $locker_payment->payment_date = $request->payment_date;
            $locker_payment->taxtype_id = 1;
            $locker_payment->tax_amount = $request->input('billing-total') - $locker_payment->subtotal;
            $locker_payment->save();

        }else{
            $memberlocker = MemberLocker::findOrFail($request->neworrenew);
            $memberlocker->user_id = $request->user_id;
            $memberlocker->from_date = $memberlocker->from_date;
            $memberlocker->is_key_returned = 'No';

            $parsedFromDate = Carbon::parse($memberlocker->thru_date);
            //check what package did the customer bought
            $choosenlocker = Locker::findOrFail($request->locker);
            if($choosenlocker->duration_type=='monthly'){
                $memberlocker->thru_date = $parsedFromDate->addMonths($choosenlocker->duration * $request->qty);
            }else if($choosenlocker->duration_type=='daily'){
                $memberlocker->thru_date =$parsedFromDate->addDays($choosenlocker->duration * $request->qty);
            }else if($choosenlocker->duration_type=='yearly'){
                $memberlocker->thru_date =$parsedFromDate->addYears($choosenlocker->duration * $request->qty);
            }

            $memberlocker->save();

            //Add to locker payment
            $locker_payment = new LockerPayment;
            $locker_payment->memberlocker_id = $memberlocker->id;
            $locker_payment->locker_id = $request->locker;
            $locker_payment->payment_number = sprintf('%08d', $payment_number).$request->payment_number;
            $locker_payment->staff_id = $request->staff;
            $locker_payment->price = $choosenlocker->price;
            $locker_payment->quantity = $request->qty;
            $locker_payment->subtotal = round(10 / 11 * $request->input('billing-total'));
            $locker_payment->pay = $request->pay;
            $locker_payment->paymentmethod_id = $request->payment_method;
            $locker_payment->payment_date = $request->payment_date;
            $locker_payment->taxtype_id = 1;
            $locker_payment->tax_amount = $request->input('billing-total') - $locker_payment->subtotal;
            $locker_payment->save();
        }

        // Added 25102019
        app('App\Http\Controllers\PointStaffController')->locker_point($request->staff, $locker_payment->id);

        return redirect('cf-lite/locker/cart/payment/'.$locker_payment->id.'/detail');
    }

    public function locker_payment_detail($id) {
        $locker_payment = LockerPayment::findOrFail($id);

        return view('cf_lite.payments.locker-payment-detail-history', compact('locker_payment'));
    }

    public function choose_locker_menu(){
        return view('cf_lite.locker.locker-setting-choose');
    }

    public function club_locker_list(){
//        $clubs = ClubInfo::get();
//        return view('cf_lite.locker.club-locker-list',compact('clubs'));
        return redirect('cf-lite/locker/locker-info-list/'.Auth::user()->club_id);
    }

    public function locker_info_list($id){
        $club = ClubInfo::findOrFail($id);
        $lockers = LockerInfo::where('club_id','=',$id)->orderByraw('substr(locker_name,1,1), length(locker_name), locker_name')->get();

        $member_lockers = MemberLocker::whereDate('from_date','<=',Carbon::now())->whereDate('thru_date','>=',Carbon::now())->orderBy('thru_date','asc')->get();

        $locker_start = range('A','Z');
        // $locker_start = [];
        // for ($c = ord("A"); $c <= ord("Z"); $c++){
        //     array_push($locker_start, [chr($c)=>chr($c)]);
        // }

        foreach ($lockers as $locker) {
            $isAvailable = 1;
            $rent_name = '';
            $rent_availability = Carbon::now();
            foreach ($member_lockers as $memlock) {
                if($locker->id == $memlock->locker_info_id){
                    $isAvailable = 0;
                    $rent_name = $memlock->memberlocker_user->name;
                    $rent_availability = Carbon::parse($memlock->thru_date)->format('d-m-Y');
                }
            }
            if($isAvailable == 0){
                $locker->setAttribute('isAvailable','0');
                $locker->setAttribute('rent_name',$rent_name);
                $locker->setAttribute('rent_availability',$rent_availability);
            }else{
                $locker->setAttribute('isAvailable','1');
            }
        }

        return view('cf_lite.locker.locker-info-list',compact('lockers','club','locker_start'));
    }

    public function store_lockers(AddBulkLockerInfoRequest $request){

        $letter_start = $request->locker_name_start;
        $letter_end = $request->locker_name_end;
        $number_start = $request->number_start;
        $number_end = $request->number_end;
        $club_id = $request->club_id;

        for($i = 65+$letter_start; $i <= 65+$letter_end ; $i++){
            for ($j=$number_start; $j <= $number_end; $j++) {
                $locker_info = new LockerInfo;
                $locker_info->locker_name = chr($i)."".$j;
                $locker_info->club_id = $club_id;

                $lockers = LockerInfo::where('locker_name','=',$locker_info->locker_name)->where('club_id','=',$club_id)->get();
                if(count($lockers)==0){
                    $locker_info->save();
                }
            }
        }

        return redirect('cf-lite/locker/locker-info-list/'.$club_id);
    }

    public function choose_locker_sale($id){
        $club = ClubInfo::findOrFail($id);
        $lockers = LockerInfo::selectRaw('*,
            (SELECT thru_date FROM member_lockers WHERE locker_info_id = locker_infos.id ORDER BY thru_date ASC LIMIT 1) as max_thru,
            (SELECT is_key_returned FROM member_lockers WHERE locker_info_id = locker_infos.id ORDER BY thru_date ASC LIMIT 1),
            (SELECT name FROM member_lockers JOIN users ON member_lockers.user_id = users.id WHERE locker_info_id = locker_infos.id ORDER BY thru_date ASC LIMIT 1),
            (SELECT id FROM member_lockers WHERE locker_info_id = locker_infos.id ORDER BY thru_date ASC LIMIT 1) as memberlocker_id
        ')
            ->where('club_id','=',$id)
            ->orderByraw('substr(locker_name,1,1), length(locker_name), locker_name')
            ->get();

        $member_lockers = MemberLocker::whereDate('from_date','<=',Carbon::now())->whereDate('thru_date','>=',Carbon::now())->orderBy('thru_date','asc')->get();

        foreach ($lockers as $locker) {
            $isAvailable = 1;
            $rent_name = '';
            $rent_availability = Carbon::now();
            foreach ($member_lockers as $memlock) {
                if($locker->id == $memlock->locker_info_id){
                    $isAvailable = 0;
                    $rent_name = $memlock->memberlocker_user->name;
                    $rent_availability = Carbon::parse($memlock->thru_date)->format('d-m-Y');
                }
            }
            if($isAvailable == 0){
                $locker->setAttribute('isAvailable','0');
                $locker->setAttribute('rent_name',$rent_name);
                $locker->setAttribute('rent_availability',$rent_availability);
            }else{
                $locker->setAttribute('isAvailable','1');
            }
        }

        return view('cf_lite.locker.sales-locker.choose-locker-sale',compact('lockers','club'));
    }

    public function locker_rent_list(){
        $memberlockers = MemberLocker::whereHas('memberlocker_user', function ($q) {
            $q->where('club_id', '=', Auth::user()->club_id);
        })
            ->whereDate('from_date','<=',Carbon::now())
            ->whereDate('thru_date','>=',Carbon::now())
            ->get();

        $lockers = LockerInfo::where('club_id','=', Auth::user()->club_id)->pluck('locker_name','id');

        return view('cf_lite.locker.locker-rent-management',compact('memberlockers','lockers'));
    }

    public function update_memberlocker(Request $request){
        $this->validate($request, [
            'locker_name' => 'sometimes',
            'from_date' => 'required|date',
            'editid'=>'required',
            'edituserid' => 'required',
        ]);

        $selectedLocker = MemberLocker::where('locker_info_id','=',$request->locker_name)->whereDate('thru_date','>=',$request->from_date)->where('user_id','<>',$request->edituserid)->get();

        $lockerpayments = LockerPayment::where('memberlocker_id','=',$request->editid)->get();

        if($selectedLocker->count()){
            Session::flash('flash_message',trans('messages.locker_has_been_rent_by_others'));
        }else{
            $thru_date = $request->from_date;
            foreach ($lockerpayments as $lp ) {
                $parsedFromDate = Carbon::parse($thru_date);
                if($lp->lockerpayment_locker->duration_type=='monthly'){
                    $thru_date = $parsedFromDate->addMonths($lp->lockerpayment_locker->duration * $lp->quantity);
                }else if($lp->lockerpayment_locker->duration_type=='daily'){
                    $thru_date =$parsedFromDate->addDays($lp->lockerpayment_locker->duration * $lp->quantity);
                }else if($lp->lockerpayment_locker->duration_type=='yearly'){
                    $thru_date =$parsedFromDate->addYears($lp->lockerpayment_locker->duration * $lp->quantity);
                }
            }
            $memberlocker = MemberLocker::findOrFail($request->editid);
            $memberlocker->locker_info_id = $request->locker_name;
            $memberlocker->from_date = $request->from_date;
            $memberlocker->thru_date = $thru_date;
            $memberlocker->save();

            Session::flash('flash_message',trans('messages.locker_updated'));
        }

        return redirect('cf-lite/locker/locker-rent-management');
    }

    public function key_returned($id){
        $staff_id = Auth::user()->club_id;

        $memberlocker = MemberLocker::findOrFail($id);
        $memberlocker->is_key_returned = 'Yes';
        $memberlocker->save();

        return redirect('cf-lite/locker/locker-sale-list/'.$staff_id);
    }

    //New Locker Sales
    public function new_locker_sales(){
        // $lockers = LockerInfo::leftJoin('member_lockers','locker_infos.id','=','member_lockers.locker_info_id')->whereRaw('locker_infos.id NOT IN (SELECT locker_info_id FROM member_lockers WHERE from_date <= \''.Carbon::today().'\' AND thru_date >= \''.Carbon::today().'\')')->where('club_id','=',Auth::user()->club_id)->pluck('locker_name','locker_infos.id');
        $lockers = LockerInfo::where('id','=',0)->pluck('locker_name','id');

        $packets = Locker::where('club_id','=',Auth::user()->club_id)->get();

        $staffs = User::where('role_id','<>',7)->where('club_id','=',Auth::user()->club_id)->pluck('name','id');

        $members = User::where('role_id','=',7)->where('club_id','=',Auth::user()->club_id)->pluck('name','id');

        $payment_methods = PaymentMethod::availableForPayment()->pluck('payment_method', 'id');

        $banks = Bank::where('id', '<>', 1)
            ->whereHas('bank_club', function ($q){
                $q->where('club_id', '=', Auth::user()->club_id);
            })->get();

        $wcpScript = WebClientPrint::createScript(action('WebClientPrintController@processRequest'), action('PrintESCPOSController@cashierLocker'), Session::getId());

        //Payment Number added 190218
        $temp_auto_number = AutoNumber::findOrFail('1');

        $club_id = Auth::user()->club_id;

        //ACCURATE
        /* $accurate = AccurateAuth::find(1);

        $redirectURI = env('ACCURATE_REDIRECT_URI_LOCKER');
        //$databaseID = Auth::user() ? Accurate::accurateClub(Auth::user()->club_id) : env('ACCURATE_DATABASE_ID');
        //$openDB = Accurate::AccurateOpenDB($databaseID, $accurate->access_token );

        $accurateClub = Auth::user() ? Accurate::accurateClub(Auth::user()->club_id) : '';
        $openDB = '';
        $branchID = '';
        $databaseID = '';

        if($accurateClub){
            $databaseID = $accurateClub->accurate_id;
            $openDB = Accurate::AccurateOpenDB($databaseID, $accurate->access_token );
            $branchID = $accurateClub->accurate_branch;


            $code = '';
            if(isset($_GET['code'])){
                $code = $_GET["code"];
            }
            if($openDB){
                //echo 'DB opened..';
            }else{
                Accurate::oauthAOL($code, $accurate, $databaseID, $redirectURI);
            }
        } */

        return view('cf_lite.cashiers.locker-cashier',compact('lockers','packets','staffs','members','payment_methods','temp_auto_number','club_id', 'banks', 'wcpScript'));
    }

    public function get_locker_list(Request $request){
        // $lockers = LockerInfo::leftJoin('member_lockers','locker_infos.id','=','member_lockers.locker_info_id')->whereRaw('locker_infos.id NOT IN (SELECT locker_info_id FROM member_lockers WHERE (from_date <= \''.Carbon::today().'\' AND thru_date >= \''.Carbon::today().'\') AND user_id <> '.$request->selected_user_id.')')->where('club_id','=',Auth::user()->club_id)->pluck('locker_name','locker_infos.id');

        $lockers = LockerInfo::selectRaw('
            id,
            CASE WHEN (SELECT COUNT(user_id) FROM member_lockers WHERE locker_info_id = locker_infos.id AND user_id = '.$request->selected_user.' AND (from_date <= \''.Carbon::today().'\' AND thru_date >= \''.Carbon::today().'\')) > 0
            THEN CONCAT(locker_name, \' ( \',(SELECT thru_date FROM member_lockers WHERE locker_info_id = locker_infos.id AND user_id = '.$request->selected_user.' AND (from_date <= \''.Carbon::today().'\' AND thru_date >= \''.Carbon::today().'\') ORDER BY thru_date DESC LIMIT 1),\' )\')
            ELSE locker_name
            END as locker_name_edited
        ')->whereRaw('id NOT IN (SELECT locker_info_id FROM member_lockers WHERE (from_date <= \''.Carbon::today().'\' AND thru_date >= \''.Carbon::today().'\') AND user_id <> '.$request->selected_user.') AND club_id = '.Auth::user()->club_id.'')->get();

        return response()->json($lockers, 200);
    }

    public function store_locker_payment_cashier(Request $request) {

        $this->validate($request, [
            'staff' => 'required',
            'member' => 'required',
            'payment_date' => 'required',
            'locker' => 'required',
            'payment_method' => 'required',
            'bank' => 'required_unless:payment_method,1',
            'pay' => 'required|numeric',
            'start_date' => 'required',
            'packet' => 'required',
            'amount' => 'required',
            'billingtotal' => 'required'
        ]);

        //Update Auto Number
        $temp_auto_number = AutoNumber::findOrFail('1');
        $payment_number = $temp_auto_number->current_number;

        $temp_auto_number->current_number = $temp_auto_number->current_number + 1;
        //$temp_auto_number->save();

        //if the customer wants to rent a new locker
        $memberlocker = new MemberLocker;
        $memberlocker->user_id = $request->member;
        $memberlocker->from_date = $request->start_date;

        //check what package did the customer bought
        $choosenlocker = Locker::findOrFail($request->packet);
        if($choosenlocker->duration_type=='monthly'){
            $memberlocker->thru_date = Carbon::parse($request->start_date)->addMonths($choosenlocker->duration * $request->amount);
        }else if($choosenlocker->duration_type=='daily'){
            $memberlocker->thru_date = Carbon::parse($request->start_date)->addDays($choosenlocker->duration * $request->amount);
        }else if($choosenlocker->duration_type=='yearly'){
            $memberlocker->thru_date = Carbon::parse($request->start_date)->addYears($choosenlocker->duration * $request->amount);
        }
        $memberlocker->locker_info_id = $request->locker;
        $memberlocker->is_key_returned = 'No';
        //$memberlocker->save();

        //Add to locker payment
        $locker_payment = new LockerPayment;
        $locker_payment->memberlocker_id = $memberlocker->id;
        $locker_payment->locker_id = $request->packet;
        $locker_payment->payment_number = sprintf('%08d', $payment_number).$request->payment_number;
        $locker_payment->staff_id = $request->staff;
        $locker_payment->price = $choosenlocker->price;
        $locker_payment->quantity = $request->amount;
        $locker_payment->subtotal = round(10 / 11 * $request->billingtotal);
        $locker_payment->pay = $request->pay;
        $locker_payment->paymentmethod_id = $request->payment_method;
        $locker_payment->payment_date = $request->payment_date;
        $locker_payment->taxtype_id = 1;
        $locker_payment->tax_amount = $request->billingtotal - $locker_payment->subtotal;
        //$locker_payment->save();

        /* $accurate = AccurateAuth::find(1);

        //$databaseID = Auth::user() ? Accurate::accurateClub(Auth::user()->club_id) : env('ACCURATE_DATABASE_ID');
        //$openDB = Accurate::AccurateOpenDB($databaseID, $accurate->access_token );
        //$branchID = Accurate::accurateBranch(Auth::user()->club_id);

        $accurateClub = Auth::user() ? Accurate::accurateClub(Auth::user()->club_id) : '';
        $openDB = '';
        $branchID = '';
        $databaseID = '';

        if($accurateClub){
            $databaseID = $accurateClub->accurate_id;
            $openDB = Accurate::AccurateOpenDB($databaseID, $accurate->access_token );
            $branchID = $accurateClub->accurate_branch;
        }


        $buyer = User::find($request->member);
        $userData = [
            'name' => str_replace("%20", " ", $buyer->name),
            'locker_type' => 'LOKER',
            'locker_id' => $request->locker,
            'description' => $choosenlocker->name .' | '. $request->start_date .' - '. Carbon::parse($memberlocker->thru_date)->format('Y-m-d'),
            'payment_date' => $request->payment_date,
            'price' => $choosenlocker->price,
            'qty' => $request->amount,
            'pay' => $request->pay,
        ];


        //CREATE SALES INVOICE ONE CUSTOMER & ITEM IS TRUE
        $this->store_accurate(Auth::user()->club_id, $userData); */

        //return redirect('cf-lite/locker/cart/payment/'.$locker_payment->id.'/detail');
    }
    //End of New Locker Sales

    /*****************************/
    /**** ACCURATE INVOICE SCRIPT ***/
    /*****************************/

    public function store_accurate($club_id, $userData)
    {
        //GET VARIABLES
        $accurate = AccurateAuth::find(1);
        //$databaseID = Accurate::accurateClub($club_id);
        //$openDB = Accurate::AccurateOpenDB($databaseID, $accurate->access_token );
        //$branchID = Accurate::accurateBranch($club_id);
        $accurateClub =Accurate::accurateClub($club_id);
        $openDB = '';
        $branchID = '';
        $databaseID = '';

        if($accurateClub){
            $databaseID = $accurateClub->accurate_id;
            $openDB = Accurate::AccurateOpenDB($databaseID, $accurate->access_token );
            $branchID = $accurateClub->accurate_branch;
        }
        $itemList = [];

        if($accurateClub){
            //CHECK IF USER EXIST IN ACCURATE
            //IF USER DO NOT EXIST, CREATE USING THE API
            $accurateUser = Accurate::AccurateCheckMember($openDB, $userData);
            if($accurateUser){
                //do nothing
            }else{
                $accurateUser = Accurate::AccurateCreateMember($openDB, $userData);
            }


            //$accurateItem = Accurate::accurateCheckProduct($openDB, ['item'=>'LOCKER'], 'SERVICE');
            $accurateItem = 'LOKER';


            $itemList[] = [
                "itemNo" => $accurateItem,
                "quantity" => 1,
                "unitPrice" => $userData['price'],
                'detailNotes'=> $userData['description']
            ];

            $invoiceData = [
                "customerNo" => $accurateUser['customerNo'],
                "transDate" => date("d/m/Y"),
                "branchId" => $branchID,
                "detailItem" => $itemList
            ];

            //CREATE SALES INVOICE ONE CUSTOMER & ITEM IS TRUE
            $accurateInvoice = Accurate::AccurateCreateInvoice($openDB, $invoiceData);
            Storage::put('invoices/locker/'.date("d-m-Y").'_'.$accurateUser['customerNo'].'.txt', json_encode($accurateInvoice));
        }

    }

    /*******************/
    /* TEST ACCURATE ***/
    /*******************/

    public function new_locker_sales_test(){
        // $lockers = LockerInfo::leftJoin('member_lockers','locker_infos.id','=','member_lockers.locker_info_id')->whereRaw('locker_infos.id NOT IN (SELECT locker_info_id FROM member_lockers WHERE from_date <= \''.Carbon::today().'\' AND thru_date >= \''.Carbon::today().'\')')->where('club_id','=',Auth::user()->club_id)->pluck('locker_name','locker_infos.id');
        $lockers = LockerInfo::where('id','=',0)->pluck('locker_name','id');

        $packets = Locker::where('club_id','=',Auth::user()->club_id)->get();

        $staffs = User::where('role_id','<>',7)->where('club_id','=',Auth::user()->club_id)->pluck('name','id');

        $members = User::where('role_id','=',7)->where('club_id','=',Auth::user()->club_id)->pluck('name','id');

        $payment_methods = PaymentMethod::availableForPayment()->pluck('payment_method', 'id');

        $banks = Bank::where('id', '<>', 1)
            ->whereHas('bank_club', function ($q){
                $q->where('club_id', '=', Auth::user()->club_id);
            })->get();

        $wcpScript = WebClientPrint::createScript(action('WebClientPrintController@processRequest'), action('PrintESCPOSController@cashierLocker'), Session::getId());

        //Payment Number added 190218
        $temp_auto_number = AutoNumber::findOrFail('1');

        $club_id = Auth::user()->club_id;

        //ACCURATE
        /* $accurate = AccurateAuth::find(1);

        $redirectURI = env('ACCURATE_REDIRECT_URI_LOCKER');
        $databaseID = Auth::user() ? Accurate::accurateClub(Auth::user()->club_id) : env('ACCURATE_DATABASE_ID');
        $openDB = Accurate::AccurateOpenDB($databaseID, $accurate->access_token );

        $code = '';
        if(isset($_GET['code'])){
            $code = $_GET["code"];
        }
        if($openDB){
            //echo 'DB opened..';
        }else{
            Accurate::oauthAOL($code, $accurate, $databaseID, $redirectURI);
        } */

        return view('cf_lite.cashiers.locker-cashier-test',compact('lockers','packets','staffs','members','payment_methods','temp_auto_number','club_id', 'banks', 'wcpScript'));
    }

    //LOCKER TRANSFER
    public function locker_transfer_index() {
        $member_lockers = MemberLocker::whereDate('from_date', '<=', Carbon::today()->toDateString())
            ->whereDate('thru_date', '>', Carbon::today()->toDateString())
            ->whereHas('memberlocker_user', function ($user) {
                $user->where('club_id', '<>', Auth::user()->club_id);
            })
            ->whereHas('memberlocker_lockerinfo', function ($locker) {
                $locker->where('club_id', '=', Auth::user()->club_id);
            })
            ->get();

        return view('cf_lite.locker.locker-transfer', compact('member_lockers'));
    }

    public function locker_transfer_ajax(Request $request) {
        $member_locker = MemberLocker::findOrFail($request->memberlocker_id);

        if (!empty($member_locker)):
            $lockers = LockerInfo::where('club_id', '=', $member_locker->memberlocker_user->club_id)
                ->where(function ($x) use ($member_locker) {
                    $x->whereDoesntHave('lockerinfo_memberlocker')
                        ->orWhere(function ($q) use ($member_locker) {
                            $q->whereHas('lockerinfo_memberlocker', function ($r) use ($member_locker) {
                                $r->whereNotIn('locker_info_id', MemberLocker::whereDate('thru_date', '>', Carbon::today()->toDateString())
                                    ->pluck('locker_info_id'));
                            });
                        });
                })
                ->orderBy('id', 'asc')
                ->get();

            return response()->json(['member' => $member_locker->memberlocker_user, 'lockers' => $lockers]);
        endif;
    }

    public function locker_transfer(Request $request) {
        $this->validate($request, [
            'member_locker' => 'required',
            'from_date' => 'required|date',
            'locker_name' => 'required',
        ]);

        $old_member_locker = MemberLocker::findOrFail($request->member_locker);
        if (!empty($old_member_locker)):
            $new_member_locker = new MemberLocker;
            $new_member_locker->user_id = $old_member_locker->user_id;
            $new_member_locker->from_date = $request->from_date;
            $new_member_locker->thru_date = $old_member_locker->thru_date;
            $new_member_locker->locker_info_id = $request->locker_name;
            $new_member_locker->is_key_returned = 'No';
            $new_member_locker->save();

            $old_member_locker->thru_date = $request->from_date;
            $old_member_locker->is_key_returned = 'Yes';
            $old_member_locker->save();

            Session::flash('flash_message', trans('messages.locker_has_been_successfully_transfered'));
        else:
            Session::flash('flash_message', trans('messages.locker_failed_to_transfer'));
        endif;

        return redirect('cf-lite/locker/locker-transfer');
    }
}
