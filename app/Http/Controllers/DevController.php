<?php

namespace App\Http\Controllers;

use App\Model\Accounting\TempJournal;
use App\Model\DigitalProduct\DigitalProductPayment;
use App\Model\Locker\LockerPayment;
use App\Model\Locker\MemberLocker;
use App\Model\Log\LogMemberStatus;
use App\Model\Member\CoachMember;
use App\Model\Member\ContinueMembership;
use App\Model\Member\MemberPersonal;
use App\Model\Member\MemberStatus;
use App\Model\Payment\MemberDebtPayment;
use App\Model\Payment\MemberPayment;
use App\Model\Payment\MemberPaymentVoucher;
use App\Model\Payment\MembershipChangingDeposit;
use App\Model\Product\ProductPayment;
use App\Model\Product\ProductPaymentDetail;
use App\Model\Refund\ContinueMembershipRefund;
use App\Model\Workout\Workout;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DevController extends Controller
{
    public function delete_user_index(){
        $users = User::where('role_id','7')->with('user_club')->get()->pluck('pretty_name_club','id');

        return view('cf_lite.developer.delete-user', compact('users'));
    }

    public function delete_user(Request $request){
        $user = User::findOrFail($request->user_id);
        $log_text = $user->name .' => '.$user->id;

        DB::beginTransaction();

        try{
            $log_text .= $this->delete_member_payments($request->user_id);
            $log_text .= $this->delete_log_member_memberships($request->user_id);
            $log_text .= $this->delete_member_status($request->user_id);
            $log_text .= $this->delete_member_personals($request->user_id);
            $log_text .= $this->delete_member_workouts($request->user_id);
            $log_text .= $this->delete_member_coaches($request->user_id);
            $log_text .= $this->delete_product_payments($request->user_id);
            $log_text .= '<br/>USER<br/>------------<br/>';
            $log_text .= $user->toJson();
            $user->delete();

            DB::commit();
        }catch(Exception $e){
            $log_text .= '<br/>'.$e;
            info($e);
            DB::rollBack();
        }

        return view('cf_lite.developer.delete-user-result', compact('log_text'));
    }

    private function delete_member_payments($user_id){
        $mps = MemberPayment::where('user_id',$user_id)->get();

        $tj_header = '<br/>TEMP JOURNALS<br/>------------';
        $mp_header = '<br/>MEMBER_PAYMENTS<br/>------------';

        $tj_content = '';
        $mp_content = '';

        if(count($mps) <= 0) return '';

        foreach($mps as $mp){
            if($mp->payment_number != null){
                $tj_content .= $this->delete_temp_journals($mp->payment_number);
            }
            $mp_content .= $mp->toJson();
            $mp->delete();
        }

        return '<br/><br/>'.$tj_header.'<br/><br/>'.$tj_content.'<br/><br/>'.$mp_header.'<br/><br/>'.$mp_content;
    }

    private function delete_temp_journals($payment_number){
        $temp_journals = TempJournal::where('invoice_number','ILIKE','%'.$payment_number.'%')->get();
        $temp_log = '';

        if(count($temp_journals) <= 0) return '';

        foreach($temp_journals as $temp_journal){
            $temp_log .= '<br/><br/>'.$temp_journal->toJson();
            $temp_journal->delete();
        }

        return $temp_log;
    }

    private function delete_log_member_memberships($user_id){
        $lm = LogMemberStatus::where('user_id', $user_id)->first();

        if(empty($lm)) return '';

        $lm_header = '<br/>LOG MEMBER MEMBERSHIPS<br/>------------';
        $lm_content = '';

        $lm_content .= $lm->toJson();
        $lm->delete();

        return '<br/>'.$lm_header.'<br/><br/>'.$lm_content;
    }

    private function delete_member_status($user_id){
        $ms = MemberStatus::where('user_id',$user_id)->first();

        if(empty($ms)) return '';

        $ms_header = '<br/>MEMBER STATUS<br/>------------';
        $ms_content = '';

        $ms_content .= $ms->toJson();
        $ms->delete();

        return '<br/>'.$ms_header.'<br/><br/>'.$ms_content;
    }

    private function delete_member_personals($user_id){
        $mp = MemberPersonal::where('user_id', $user_id)->first();

        if(empty($mp)) return '';

        $mp_header = '<br/>MEMBER PERSONAL<br/>------------';
        $mp_content = '';

        $mp_content .= $mp->toJson();
        $mp->delete();

        return '<br/>'.$mp_header.'<br/><br/>'.$mp_content;
    }

    private function delete_member_workouts($user_id){
        $workouts = Workout::where('user_id', $user_id)->get();

        if(count($workouts) <= 0) return '';

        $w_header = '<br/>WORKOUT<br/>------------';
        $w_content = '';

        foreach ($workouts as $workout) {
            $w_content .= $workout->toJson();
            $workout->delete();
        }

        return '<br/><br/>'.$w_header.'<br/><br/>'.$w_content;
    }

    private function delete_member_coaches($user_id){
        $cm = CoachMember::where('member_id', $user_id)->first();
        
        if(empty($cm)) return '';

        $cm_header = '<br/>COACH MEMBER<br/>------------';
        $cm_content = '';

        $cm_content .= $cm->toJson();
        $cm->delete();

        return '<br/>'.$cm_header.'<br/><br/>'.$cm_content;
    }

    private function delete_product_payments($user_id){
        $pps = ProductPayment::where('member_id', $user_id)->get();

        $pp_header = '<br/>PRODUCT PAYMENTS<br/>------------';
        $ppd_header = '<br/>PRODUCT PAYMENT DETAILS<br/>------------';
        $tj_header = '<br/>TEMP JOURNALS<br/>------------';

        $pp_content = '';
        $ppd_content = '';
        $tj_content = '';

        foreach ($pps as $pp) {
            if ($pp->payment_number != null) {
                $tj_content .= $this->delete_temp_journals($pp->payment_number);
            }
            $ppd_content .= $this->delete_product_payment_details($pp->id);
            $pp_content .= $pp->toJson();
            $pp->delete();
        }

        return '<br/><br/>'.$tj_header.'<br/><br/>'.$tj_content.'<br/><br/>'.$pp_header.'<br/><br/>'.$pp_content.'<br/><br/>'.$ppd_header.'<br/><br/>'.$ppd_content;
    }

    private function delete_product_payment_details($product_payment_id){
        $ppds = ProductPaymentDetail::where('product_payment_id',$product_payment_id)->get();

        $ppd_content = '';

        foreach ($ppds as $ppd) {
            $ppd_content .= $ppd->toJson();
            $ppd->delete();
        }

        return '<br/>'.$ppd_content;
    }

    public function delete_locker_index(){
        $users = User::where('role_id', '7')->with('user_club')->get()->pluck('pretty_name_club', 'id');

        return view('cf_lite.developer.delete-locker', compact('users'));
    }

    public function delete_locker(Request $request){
        $user = User::findOrFail($request->user_id);
        $member_locker = MemberLocker::findOrFail($request->memberlocker_id);
        $log_text = '(LOCKER) '. $user->name . ' => ' . $member_locker->id;

        DB::beginTransaction();

        try {
            $log_text .= $this->delete_locker_payment($request->memberlocker_id);
            $log_text .= '<br/>MEMBER LOCKER<br/>------------<br/>';
            $log_text .= $member_locker->toJson();
            $member_locker->delete();

            DB::commit();
        } catch (Exception $e) {
            $log_text .= '<br/>' . $e;
            info($e);
            DB::rollBack();
        }

        return view('cf_lite.developer.delete-locker-result', compact('log_text'));
    }

    private function delete_locker_payment($memberlocker_id){
        $lps = LockerPayment::where('memberlocker_id', $memberlocker_id)->get();

        $tj_header = '<br/>TEMP JOURNALS<br/>------------';
        $lp_header = '<br/>LOCKER_PAYMENTS<br/>------------';

        $tj_content = '';
        $lp_content = '';

        if (count($lps) <= 0) return '';

        foreach ($lps as $lp) {
            if ($lp->payment_number != null) {
                $tj_content .= $this->delete_temp_journals($lp->payment_number);
            }
            $lp_content .= $lp->toJson();
            $lp->delete();
        }

        return '<br/><br/>' . $tj_header . '<br/><br/>' . $tj_content . '<br/><br/>' . $lp_header . '<br/><br/>' . $lp_content;
    }

    public function delete_product_index(){
        $users = User::where('role_id', '7')->with('user_club')->get()->pluck('pretty_name_club', 'id');

        return view('cf_lite.developer.delete-product', compact('users'));
    }

    public function delete_product(Request $request){
        $user = User::findOrFail($request->user_id);
        $product_payment = ProductPayment::findOrFail($request->product_payment_id);
        $log_text = '(PRODUCT) '. $user->name . ' => ' . $user->id;

        DB::beginTransaction();
        
        try {
            $log_text .= $this->delete_product_payment_details($product_payment->id);
            $log_text = '<br/>TEMP JOURNALS<br/>------------';
            $log_text .= $this->delete_temp_journals($product_payment->payment_number);
            $log_text .= '<br/>PRODUCT PAYMENT<br/>------------<br/>';
            $log_text .= $product_payment->toJson();
            $product_payment->delete();

            DB::commit();
        } catch (Exception $e) {
            $log_text .= '<br/>' . $e;
            info($e);
            DB::rollBack();
        }

        return view('cf_lite.developer.delete-product-result', compact('log_text'));
    }

    public function delete_member_payment_index(){
        return view('cf_lite.developer.delete-member-payment');
    }

    public function get_member_payment_data(Request $request){
        $member_payments = MemberPayment::where('payment_number','ILIKE','%'.$request->search.'%')->get();

        return response()->json($member_payments);
    }

    public function get_member_payment_detail(Request $request){
        $member_payment = MemberPayment::with(
            'memberpayment_memberpaymentvoucher',
            'memberpayment_membershipchangingdeposit',
            'memberpayment_digitalproductpayment',
            'memberpayment_continuemembershiprefund',
            'memberpayment_memberdebtpayment')->where('id',$request->member_payment_id)->first();

        return response()->json($member_payment);
    }

    public function delete_member_payment(Request $request){
        $member_payment = MemberPayment::find($request->member_payment_id);
        $log_text = '';
        if(empty($member_payment)) view('cf_lite.developer.delete-member-payment');

        DB::beginTransaction();
        try{
            $log_text .= $this->delete_member_payment_voucher($member_payment->id);
            $log_text .= $this->delete_membership_changing_deposit($member_payment->id);
            $log_text .= $this->delete_digital_product_payment($member_payment->id);
            $log_text .= $this->delete_continue_membership_refund($member_payment->id);
            $log_text .= $this->delete_member_debt_payment($member_payment->id);

            $header = '<br/>MEMBER PAYMENT<br/>------------';
            $content = '';

            $content .= $member_payment->toJson();
            $member_payment->delete();

            $log_text .= '<br/>' . $header . '<br/><br/>' . $content;
            DB::commit();
        }catch(\Exception $e){
            $log_text .= '<br/>' . $e;
            info($e);
            DB::rollBack();
        }

        return view('cf_lite.developer.delete-member-payment-result', compact('log_text'));
    }

    private function delete_member_payment_voucher($member_payment_id){
        $mp_voucher = MemberPaymentVoucher::where('member_payment_id',$member_payment_id)->first();

        if (empty($mp_voucher)) return '';

        $header = '<br/>MEMBER PAYMENT VOUCHER<br/>------------';
        $content = '';

        $content .= $mp_voucher->toJson();
        $mp_voucher->delete();

        return '<br/>' . $header . '<br/><br/>' . $content;
    }

    private function delete_membership_changing_deposit($member_payment_id)
    {
        $deposit = MembershipChangingDeposit::where('payment_id', $member_payment_id)->first();

        if (empty($deposit)) return '';

        $header = '<br/>MEMBERSHIP CHANGING DEPOSIT<br/>------------';
        $content = '';

        $content .= $deposit->toJson();
        $deposit->delete();

        return '<br/>' . $header . '<br/><br/>' . $content;
    }

    private function delete_digital_product_payment($member_payment_id)
    {
        $mp_voucher = DigitalProductPayment::where('member_payment_id', $member_payment_id)->first();

        if (empty($mp_voucher)) return '';

        $header = '<br/>DIGITAL PRODUCT PAYMENT<br/>------------';
        $content = '';

        $content .= $mp_voucher->toJson();
        $mp_voucher->delete();

        return '<br/>' . $header . '<br/><br/>' . $content;
    }

    private function delete_continue_membership_refund($member_payment_id)
    {
        $refund = ContinueMembershipRefund::where('payment_id', $member_payment_id)->first();
        $log_text = '';
        if (empty($refund)) return '';

        $log_text .= $this->delete_continue_membership($refund->continue_id);

        $header = '<br/>CONTINUE MEMBERSHIP REFUND<br/>------------';
        $content = '';

        $content .= $refund->toJson();
        $refund->delete();

        return $log_text. '<br/>' . $header . '<br/><br/>' . $content;
    }

    private function delete_continue_membership($continue_id)
    {
        $continue = ContinueMembership::where('id', $continue_id)->first();
        $log_text = '';

        if (empty($continue)) return '';

        $log_text .= $this->delete_log_member_membership_continue($continue->membership_type_id, $continue->from_date, $continue->thru_date, $continue->user_id);

        $header = '<br/>CONTINUE MEMBERSHIP<br/>------------';
        $content = '';

        $content .= $continue->toJson();
        $continue->delete();

        return $log_text . '<br/>' . $header . '<br/><br/>' . $content;
    }

    private function delete_log_member_membership_continue($membership_id, $from_date, $thru_date, $user_id)
    {
        $log_member_memberships = LogMemberStatus::where('membership_type_id', $membership_id)
                                    ->where('from_date',$from_date)
                                    ->where('thru_date',$thru_date)
                                    ->where('user_id', $user_id)
                                    ->first();

        if (empty($log_member_memberships)) return '';

        $header = '<br/>LOG MEMBER MEMBERSHIPS<br/>------------';
        $content = '';

        $content .= $log_member_memberships->toJson();
        $log_member_memberships->delete();

        return '<br/>' . $header . '<br/><br/>' . $content;
    }

    private function delete_member_debt_payment($member_payment_id)
    {
        $debt = MemberDebtPayment::where('member_payment_id', $member_payment_id)->first();

        if (empty($debt)) return '';

        $header = '<br/>MEMBER DEBT PAYMENT<br/>------------';
        $content = '';

        $content .= $debt->toJson();
        $debt->delete();

        return '<br/>' . $header . '<br/><br/>' . $content;

    }

    public function get_user_delete_data(Request $request){
        $user = User::where('id',$request->user_id)
                    ->with(
                        'user_memberpayment',
                        'user_logmemberstatus',
                        'user_memberstatus',
                        'user_memberpersonal',
                        'user_workout',
                        'memberuser_coachmember',
                        'user_productpayment',
                        'user_productpayment.productpayment_productpaymentdetail'
                    )
                    ->first();

        return response()->json(['user' => $user]);
    }

    public function  get_user_locker(Request $request){
        $member_lockers = MemberLocker::where('user_id',$request->user_id)
                            ->with('memberlocker_lockerPayment', 'memberlocker_lockerinfo')
                            ->get()
                            ->pluck('pretty_member_locker','id');

        return response()->json(['member_lockers' => $member_lockers]);
    }

    public function get_locker_delete_data(Request $request){
        $member_locker = MemberLocker::where('id', $request->memberlocker_id)
            ->with(
                'memberlocker_user',
                'memberlocker_lockerPayment',
                'memberlocker_lockerinfo'
            )->first();

        return response()->json(['member_locker' => $member_locker]);
    }

    public function  get_user_product(Request $request){
        if(empty($request->user_id)){
            $product_payments = ProductPayment::whereNull('member_id')
                ->get()
                ->pluck('pretty_product_payment', 'id');
        }else{
            $product_payments = ProductPayment::where('member_id', $request->user_id)
                ->get()
                ->pluck('pretty_product_payment', 'id');
        }

        return response()->json(['product_payments' => $product_payments]);
    }

    public function get_product_delete_data(Request $request){
        $product_payment = ProductPayment::where('id', $request->product_payment_id)
            ->with(
                'productpayment_productpaymentdetail',
                'productpayment_productpaymentdetail.productpaymentdetail_product',
                'productpayment_user',
                'productpayment_bank',
                'productpayment_paymentmethod',
                'productpayment_club'
            )
            ->first();

        return response()->json(['product_payment' => $product_payment]);
    }
}
