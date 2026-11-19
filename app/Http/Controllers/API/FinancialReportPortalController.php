<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

use App\Model\GuestMembership\GuestMemberPayment;
use App\Model\Locker\LockerPayment;
use App\Model\Log\LogMemberStatus;
use App\Model\MasterData\MembershipType;
use App\Model\Member\MemberDeposit;
use App\Model\Payment\MemberPayment;
use App\Model\Product\ProductPayment;
use App\Model\Product\ProductPaymentDetail;
use App\Model\Member\MemberStatus;

use App\Model\Workout\Workout;

use Carbon\Carbon;
use App\Model\Staff\TransferHistory;
use App\User;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\AccurateAuth;
use App\Helpers\Accurate;
use stdClass;
use Storage;
use App\Model\MasterData\Bank;

class FinancialReportPortalController extends Controller
{
    /* public function daily_sales($date, $club, $user) {
        echo $date . ' = '.$club .' = '.$user;
    } */
    public function daily_sales($date, $clubID, $user) {
        //dd($date . ' = '.$clubID .' = '.$user);

        $payment_date = Carbon::today();
        if (!empty($date)):
            $payment_date = $date;
        endif;

        //Check Transfer History Added 28/07/2019
        $user1 = User::where('club_id','=',$clubID)
                        ->whereDoesntHave('user_transferhistory')->pluck('id');

        $user2_temp = DB::select("SELECT user_id
                    FROM transfer_histories
                    WHERE from_date IN (SELECT min(from_date) FROM transfer_histories GROUP BY user_id)
                      AND from_date > '" . $payment_date . "'
                    AND club_id = " . $clubID);

        $user3_temp = DB::select("SELECT transfer_histories.user_id
                    FROM transfer_histories, log_member_memberships
                    WHERE transfer_histories.created_at IN (SELECT min(created_at) FROM transfer_histories GROUP BY user_id)
                      AND transfer_histories.created_at > '".$payment_date."'
                    AND transfer_histories.club_id != ".$clubID."
                    AND transfer_histories.user_id = log_member_memberships.user_id
                    AND log_member_memberships.club_id = ".$clubID."
                    AND log_member_memberships.from_date <= '".$payment_date."'
                    AND log_member_memberships.thru_date >= '".$payment_date."'"
        );

        $user4_temp =DB::select(
            "SELECT transfer_histories.user_id
                    FROM transfer_histories
                    JOIN log_member_memberships
                    ON transfer_histories.user_id = log_member_memberships.user_id
                    WHERE transfer_histories.from_date IN (SELECT min(from_date) FROM transfer_histories GROUP BY user_id)
                      AND transfer_histories.from_date <= '" . $payment_date . "'
                    AND transfer_histories.club_id != " . $clubID . "
                    AND log_member_memberships.club_id = " . $clubID . "
                    AND log_member_memberships.from_date <= '" . $payment_date . "'
                    AND log_member_memberships.thru_date >= '" . $payment_date . "'"
        );

        $user2 = [];
        $user3 = [];
        $user4 = [];
        foreach ($user2_temp as $key => $value) {
            array_push($user2, $value->user_id);
        }
        foreach ($user3_temp as $key => $value) {
            array_push($user3, $value->user_id);
        }
        foreach ($user4_temp as $key => $value) {
            array_push($user4, $value->user_id);
        }
        $listArr = $user1->merge($user2);
        $listArr = $listArr->merge($user3);
        $listArr = $listArr->merge($user4);

        //MEMBER PAYMENTS
        $member_payments = MemberPayment::whereDate('payment_date', '=', Carbon::parse($payment_date)->toDateString())
            ->whereHas('memberpayment_user', function ($u) use ($listArr){
                $u->whereIn('id',$listArr);
            })
            ->with(['memberpayment_memberdebtpayment','memberpayment_user', 'memberpayment_user.user_memberpersonal'])
            ->get();

        foreach ($member_payments as $payment):
            $member_payment = MemberPayment::findOrFail($payment->id);

            $membership_types = LogMemberStatus::where('user_id', '=', $member_payment->user_id)
                ->whereDate('created_at', '<=', Carbon::parse($member_payment->created_at)->toDateString())
                ->orderBy('created_at', 'desc')
                ->get();

            $the_membership_type = '';
            $log_membership_id = '';
            foreach ($membership_types as $membership_type): //membership_types itu dari Log Member Status
                if (empty($the_membership_type)):
                    $the_membership_type = $membership_type->membership_type_id;
                    $log_membership_id = $membership_type->id;
                else:
                    if (Carbon::parse($member_payment->created_at)->format('dmYhi') == Carbon::parse($membership_type->created_at)->format('dmYhi')):
                        $the_membership_type = $membership_type->membership_type_id;
                        $log_membership_id = $membership_type->id;
                    endif;
                endif;
            endforeach;

            $membership = MembershipType::findOrFail($the_membership_type);
            $payment->setAttribute('membership_type_name', $membership->membership_type_name);

            //Get Money Owed
            if ($member_payment->payment_category == 'First'):
                if (($membership->payment_type == 'AD') || ($membership->payment_type == 'Cash')):
                    $money_owed = ($membership->duration - 1) * $member_payment->mf_amount;
                elseif ($membership->payment_type == 'Prepaid'):
//                    $money_owed = $member_payment->mf_amount;
                    //Updated 25032019
                    $money_owed = $member_payment->rest_of_bill;
                endif;
                $payment->setAttribute('money_owed', $money_owed);

            elseif ($member_payment->payment_category == 'Monthly'):
                $log_mt = LogMemberStatus::findOrFail($log_membership_id);

                //First Debt
                if (($membership->payment_type == 'AD') || ($membership->payment_type == 'Cash')):
                    $first_money_owed = 0;

                    $first_payment = MemberPayment::where('user_id', '=', $member_payment->user_id)
                        ->where('payment_category', '=', 'First')
                        ->whereDate('created_at', '>=', Carbon::parse($log_mt->created_at)->toDateString())
                        ->orderBy('payment_date', 'asc')
                        ->first();

                    $first_money_owed = $first_payment->rest_of_bill;

                    if (!empty($first_payment->memberpayment_memberdebtpayment)):
                        foreach ($first_payment->memberpayment_memberdebtpayment as $first_payment_debt):
                            if (strtotime($first_payment_debt->payment_date) > strtotime($payment_date)):
                                $first_money_owed = $first_money_owed + $first_payment_debt->amount_debt;
                            endif;
                        endforeach;
                    endif;
                endif;

                //Monthly Debt
                $monthly_payment = MemberPayment::where('user_id', '=', $member_payment->user_id)
                    ->where('payment_category', '=', 'Monthly')
                    ->whereDate('created_at', '>=', Carbon::parse($log_mt->created_at)->toDateString())
                    ->orderBy('payment_date', 'asc')
                    ->get();

                $monthly_money_owed = 0;
                if (count($monthly_payment) > 0):
                    foreach ($monthly_payment as $monthly_debt):
                        if ($monthly_debt->payment_status != 'Paid'):
                            $monthly_money_owed = $monthly_money_owed + $monthly_debt->rest_of_bill;
                        else:
                            if (strtotime($monthly_debt->payment_date) > strtotime($payment_date)):
                                $monthly_money_owed = $monthly_money_owed + $monthly_debt->amount_due;
                            endif;
                        endif;
                    endforeach;
                endif;

                //Total Debt
                if (($membership->payment_type == 'AD') || ($membership->payment_type == 'Cash')):
                    $total_money_owed = $first_money_owed + $monthly_money_owed;
                elseif ($membership->payment_type == 'Prepaid'):
                    $total_money_owed = $monthly_money_owed;
                endif;
                $payment->setAttribute('total_money_owed', $total_money_owed);
            endif;
        endforeach;

        //PRODUCT PAYMENTS
        //Product
        $product_payments = ProductPayment::where('club_id', '=', $clubID)
            ->whereDate('payment_date', '=', Carbon::parse($payment_date)->toDateString())
            ->where('is_reward', '=', false)
            ->with(['productpayment_productpaymentdetail', 'productpayment_productpaymentdetail.productpaymentdetail_product', 'productpayment_user', 'productpayment_user.user_memberpersonal'])
            ->get();

        //Locker
        $locker_payments = LockerPayment::whereHas('lockerpayment_memberlocker', function ($q) use ($listArr){
            $q->whereIn('user_id',$listArr);
        })->whereDate('payment_date', '=', Carbon::parse($payment_date)->toDateString())
        ->with(['lockerpayment_memberlocker','lockerpayment_memberlocker.memberlocker_user'])
        ->get();


        //MEMBER DEPOSIT
        $member_deposits = MemberDeposit::where('status', '=', 'Done')
            ->where('payment_type', '=', 'Payment')
            ->where('debet', '<>', '0')
            ->whereDate('payment_date', '=', Carbon::parse($payment_date))
            ->whereHas('memberdeposit_user', function ($club) use ($clubID) {
                $club->where('club_id', '=', $clubID);
            })
            ->with(['memberdeposit_user'])
            ->get();

        //PROMO MEMBERSHIP
        $promo_members = GuestMemberPayment::whereDate('payment_date', '=', Carbon::parse($payment_date)->toDateString())
            ->whereHas('gmpayment_gmmember', function ($q) use ($clubID) {
                $q->whereHas('gmmember_guest', function ($r)  use ($clubID) {
                    $r->where('club_id', '=', $clubID);
                });
            })
            ->with(['gmpayment_gmmember', 'gmpayment_gmmember.gmmember_guest'])->get();
        //dd($product_payments);
        /* foreach($product_payments as $product_payment){
            foreach($product_payment->productpayment_productpaymentdetail as $product_payment_detail){
                echo $product_payment_detail->productpaymentdetail_product->item_name . '<br>';
            }
        } */
        if(count($member_payments) > 0 || count($product_payments) > 0 || count($locker_payments) > 0 || count($member_deposits) > 0 || count($promo_members) > 0 ){
            $hasRecord = 1;
        }else{
            $hasRecord = 0;
        }

        /** WORKOUT **/
        $wo = Workout::select('user_id')
            ->where('club_id', '=', $clubID)
            ->whereDate('workout_date', '=', Carbon::today()->toDateString())
            ->whereHas('workout_user', function ($q) use($clubID) {
                $q->where('club_id', '=', $clubID)
                    //Added 03042018
                    ->where('role_id', '=', '7');
            })
            ->with('workout_user.user_appointment', function ($q) {
                $toDate = Carbon::now()->addDays(4)->toDateString();
                $yesterdays = Carbon::now()->subDays(4)->toDateString();
                $q->whereBetween('app_date', [$yesterdays, $toDate]);
                //$q->first();
            })
            ->whereHas('pending_payment')
            ->orderBy('workout_date', 'desc')
            ->get();

        $totalMembers = User::where('club_id', '=', $clubID)
                        ->where('role_id', '=', '7')
                        ->whereHas('user_memberstatus', function($q) {
                            $q->where('status', '=', 'Active')
                              ->orWhere('status', '=', 'Hold');
                        })->get()->count();

        $fromOtherClub = Workout::select('user_id')
            ->where('club_id', '=', $clubID)
            ->whereDate('workout_date', '=', Carbon::today()->toDateString())
            ->whereHas('workout_user', function ($q) use($clubID)  {
                $q->where('club_id', '<>', $clubID)
                    ->where('role_id', '=', '7');
            })
            ->orderBy('workout_date', 'desc')
            ->get();

        //Member Not FWO Yet
        $fwoMembers = MemberStatus::where('status', '=', 'Active')
            ->whereHas('memberstatus_user', function ($q) use ($clubID) {
                $q->where('club_id', '=', $clubID)
                    ->where('role_id', '=', '7')
                    ->whereDoesntHave('user_workout');
            })->orderBy('from_date', 'ASC')->get();



        //temp
        $fromOtherClub = 2;
        $wo = 40;

        return json_encode(['member_payments'=> $this->processMemberPayments($member_payments), 'product_payments'=> $this->processProductPayments($product_payments), 'locker_payments'=> $locker_payments, 'member_deposits'=> $member_deposits, 'promo_members'=> $this->processPromoMembers($promo_members), 'member_renewal'=> $this->processMemberRenewal($member_payments), 'member_conversion'=> $this->processMemberConversion($member_payments), 'member_autodebit'=> $this->processAutoDebit($member_payments), 'hasRecord'=> $hasRecord, 'wo'=> $wo, 'fromOtherClub'=>$fromOtherClub, 'totalMembers'=>$totalMembers, 'fwoMembers'=> count($fwoMembers) ]);
    }

    function processMemberPayments($member_payments){
        $processedTransactions = [];

        $total_daily_cash = 0;
        $total_daily_transfer = 0;
        $total_daily_debet = 0;
        $total_daily_credit = 0;
        $total_daily_owed = 0;
        $running_total = 0;
        $owed_by_member_first = [];
        $owed_by_member_monthly = [];

        if(count($member_payments) > 0){
            foreach($member_payments->where('renewal', false)->where('conversion', false) as $member_payment){
                $cash = 0;
                $transfer = 0;
                $debet = 0;
                $credit = 0;
                $no_method = 0;
                $debt = 0;

                //if($member_payment->payment_category == 'First'):

                    if(!empty($member_payment->payment_method_id)){
                        if($member_payment->payment_method_id == 1){
                            $cash = $member_payment->amount_paid;
                        }elseif($member_payment->payment_method_id == 2){
                            $debet = $member_payment->amount_paid;
                        }elseif($member_payment->payment_method_id == 3){
                            $credit = $member_payment->amount_paid;
                        }elseif($member_payment->payment_method_id == 4){
                            $transfer = $member_payment->amount_paid;
                        }
                    }else{
                        $no_method = $member_payment->amount_paid;
                    }

                    if(!empty($member_payment->memberpayment_memberdebtpayment)){
                        foreach($member_payment->memberpayment_memberdebtpayment as $debt_payment){
                            if(\Carbon\Carbon::parse($debt_payment->payment_date) < \Carbon\Carbon::parse($date)){
                                $debt = $debt + $debt_payment->amount_paid;
                            }
                        }
                    }

                    $running_total = $member_payment->amount_paid - $debt;
                    $member_payment->running_total = $running_total;

                    $member_payment->debet = $debet - $debt;
                    $total_daily_debet = $total_daily_debet + $debet - $debt;
                    $member_payment->total_daily_debet = $total_daily_debet;

                    $member_payment->credit = $credit - $debt;
                    $total_daily_credit = $total_daily_credit + $credit - $debt;
                    $member_payment->total_daily_credit = $total_daily_credit;

                    $member_payment->transfer = $transfer - $debt;
                    $total_daily_transfer = $total_daily_transfer + $transfer - $debt;
                    $member_payment->total_daily_transfer = $total_daily_transfer;

                    $member_payment->cash = $cash - $debt;
                    $total_daily_cash = $total_daily_cash + $cash - $debt;
                    $member_payment->total_daily_cash = $total_daily_cash;

                    if(!empty($member_payment->money_owed)){
                        if(isset($owed_by_member_first[$member_payment->user_id])){
                            // TODO
                        }else{
                            $owed_by_member_first[$member_payment->user_id] = $member_payment->money_owed;
                        }

                        $total_daily_owed = $total_daily_owed + $member_payment->money_owed;
                    }else{
                        $member_payment->money_owed = 0;
                    }
                    $processedTransactions[] = $member_payment;
                //endif;
            }
        }

        return $processedTransactions;
    }

    function processMemberRenewal($member_payments){
        $processedTransactions = [];

        $total_daily_cash = 0;
        $total_daily_transfer = 0;
        $total_daily_debet = 0;
        $total_daily_credit = 0;
        $total_daily_owed = 0;
        $running_total = 0;
        $owed_by_member_first = [];
        $owed_by_member_monthly = [];

        if(count($member_payments) > 0){
            foreach($member_payments->where('renewal', true) as $member_payment){
                $cash = 0;
                $transfer = 0;
                $debet = 0;
                $credit = 0;
                $no_method = 0;
                $debt = 0;

                //if($member_payment->payment_category == 'First'):

                    if(!empty($member_payment->payment_method_id)){
                        if($member_payment->payment_method_id == 1){
                            $cash = $member_payment->amount_paid;
                        }elseif($member_payment->payment_method_id == 2){
                            $debet = $member_payment->amount_paid;
                        }elseif($member_payment->payment_method_id == 3){
                            $credit = $member_payment->amount_paid;
                        }elseif($member_payment->payment_method_id == 4){
                            $transfer = $member_payment->amount_paid;
                        }
                    }else{
                        $no_method = $member_payment->amount_paid;
                    }

                    if(!empty($member_payment->memberpayment_memberdebtpayment)){
                        foreach($member_payment->memberpayment_memberdebtpayment as $debt_payment){
                            if(\Carbon\Carbon::parse($debt_payment->payment_date) < \Carbon\Carbon::parse($date)){
                                $debt = $debt + $debt_payment->amount_paid;
                            }
                        }
                    }

                    $running_total = $member_payment->amount_paid - $debt;
                    $member_payment->running_total = $running_total;

                    $member_payment->debet = $debet - $debt;
                    $total_daily_debet = $total_daily_debet + $debet - $debt;
                    $member_payment->total_daily_debet = $total_daily_debet;

                    $member_payment->credit = $credit - $debt;
                    $total_daily_credit = $total_daily_credit + $credit - $debt;
                    $member_payment->total_daily_credit = $total_daily_credit;

                    $member_payment->transfer = $transfer - $debt;
                    $total_daily_transfer = $total_daily_transfer + $transfer - $debt;
                    $member_payment->total_daily_transfer = $total_daily_transfer;

                    $member_payment->cash = $cash - $debt;
                    $total_daily_cash = $total_daily_cash + $cash - $debt;
                    $member_payment->total_daily_cash = $total_daily_cash;

                    if(!empty($member_payment->money_owed)){

                    }else{
                        $member_payment->money_owed = 0;
                    }
                    $processedTransactions[] = $member_payment;
                //endif;
            }
        }

        return $processedTransactions;
    }

    function processAutoDebit($member_payments){
        $processedTransactions = [];

        $total_daily_cash = 0;
        $total_daily_transfer = 0;
        $total_daily_debet = 0;
        $total_daily_credit = 0;
        $total_daily_owed = 0;
        $running_total = 0;
        $owed_by_member_first = [];
        $owed_by_member_monthly = [];

        if(count($member_payments) > 0){
            foreach($member_payments as $member_payment){
                $cash = 0;
                $transfer = 0;
                $debet = 0;
                $credit = 0;
                $no_method = 0;
                $debt = 0;

                if(($member_payment->payment_category == 'Monthly') && ($member_payment->payment_status != 'Unpaid')):

                    if(!empty($member_payment->payment_method_id)){
                        if($member_payment->payment_method_id == 1){
                            $cash = $member_payment->amount_paid;
                        }elseif($member_payment->payment_method_id == 2){
                            $debet = $member_payment->amount_paid;
                        }elseif($member_payment->payment_method_id == 3){
                            $credit = $member_payment->amount_paid;
                        }elseif($member_payment->payment_method_id == 4){
                            $transfer = $member_payment->amount_paid;
                        }
                    }else{
                        $no_method = $member_payment->amount_paid;
                    }

                    if(!empty($member_payment->memberpayment_memberdebtpayment)){
                        foreach($member_payment->memberpayment_memberdebtpayment as $debt_payment){
                            if(\Carbon\Carbon::parse($debt_payment->payment_date) < \Carbon\Carbon::parse($date)){
                                $debt = $debt + $debt_payment->amount_paid;
                            }
                        }
                    }

                    $running_total = $member_payment->amount_paid - $debt;
                    $member_payment->running_total = $running_total;

                    $member_payment->debet = $debet - $debt;
                    $total_daily_debet = $total_daily_debet + $debet - $debt;
                    $member_payment->total_daily_debet = $total_daily_debet;

                    $member_payment->credit = $credit - $debt;
                    $total_daily_credit = $total_daily_credit + $credit - $debt;
                    $member_payment->total_daily_credit = $total_daily_credit;

                    $member_payment->transfer = $transfer - $debt;
                    $total_daily_transfer = $total_daily_transfer + $transfer - $debt;
                    $member_payment->total_daily_transfer = $total_daily_transfer;

                    $member_payment->cash = $cash - $debt;
                    $total_daily_cash = $total_daily_cash + $cash - $debt;
                    $member_payment->total_daily_cash = $total_daily_cash;

                    if(!empty($member_payment->money_owed)){

                    }else{
                        $member_payment->money_owed = 0;
                    }
                    $processedTransactions[] = $member_payment;
                endif;
            }
        }

        return $processedTransactions;
    }

    function processMemberConversion($member_payments){
        $processedTransactions = [];

        $total_daily_cash = 0;
        $total_daily_transfer = 0;
        $total_daily_debet = 0;
        $total_daily_credit = 0;
        $total_daily_owed = 0;
        $running_total = 0;
        $owed_by_member_first = [];
        $owed_by_member_monthly = [];

        if(count($member_payments) > 0){
            foreach($member_payments->where('conversion', true) as $member_payment){
                $cash = 0;
                $transfer = 0;
                $debet = 0;
                $credit = 0;
                $no_method = 0;
                $debt = 0;

                if(!empty($member_payment->payment_method_id)){
                    if($member_payment->payment_method_id == 1){
                        $cash = $member_payment->amount_paid;
                    }elseif($member_payment->payment_method_id == 2){
                        $debet = $member_payment->amount_paid;
                    }elseif($member_payment->payment_method_id == 3){
                        $credit = $member_payment->amount_paid;
                    }elseif($member_payment->payment_method_id == 4){
                        $transfer = $member_payment->amount_paid;
                    }
                }else{
                    $no_method = $member_payment->amount_paid;
                }

                if(!empty($member_payment->memberpayment_memberdebtpayment)){
                    foreach($member_payment->memberpayment_memberdebtpayment as $debt_payment){
                        if(\Carbon\Carbon::parse($debt_payment->payment_date) < \Carbon\Carbon::parse($date)){
                            $debt = $debt + $debt_payment->amount_paid;
                        }
                    }
                }

                $running_total = $member_payment->amount_paid - $debt;
                $member_payment->running_total = $running_total;

                $member_payment->debet = $debet - $debt;
                $total_daily_debet = $total_daily_debet + $debet - $debt;
                $member_payment->total_daily_debet = $total_daily_debet;

                $member_payment->credit = $credit - $debt;
                $total_daily_credit = $total_daily_credit + $credit - $debt;
                $member_payment->total_daily_credit = $total_daily_credit;

                $member_payment->transfer = $transfer - $debt;
                $total_daily_transfer = $total_daily_transfer + $transfer - $debt;
                $member_payment->total_daily_transfer = $total_daily_transfer;

                $member_payment->cash = $cash - $debt;
                $total_daily_cash = $total_daily_cash + $cash - $debt;
                $member_payment->total_daily_cash = $total_daily_cash;

                if(!empty($member_payment->money_owed)){

                }else{
                    $member_payment->money_owed = 0;
                }
                $processedTransactions[] = $member_payment;
            }
        }

        return $processedTransactions;
    }

    function processPromoMembers($promo_members){
        $processedTransactions = [];

        $total_daily_cash = 0;
        $total_daily_transfer = 0;
        $total_daily_debet = 0;
        $total_daily_credit = 0;
        $running_total = 0;


        if(count($promo_members) > 0){
            foreach($promo_members as $promo){
                $cash = 0;
                $transfer = 0;
                $debet = 0;
                $credit = 0;
                $no_method = 0;
                $debt = 0;

                if(!empty($promo->payment_type_id)){
                    if($promo->payment_type_id == 1){
                        $cash = $promo->amount_paid;
                    }elseif($promo->payment_type_id == 2){
                        $debet = $promo->amount_paid;
                    }elseif($promo->payment_type_id == 3){
                        $credit = $promo->amount_paid;
                    }elseif($promo->payment_type_id == 4){
                        $transfer = $promo->amount_paid;
                    }
                }else{
                    $no_method = $promo->amount_paid;
                }

                $running_total = $promo->amount_paid;
                $promo->running_total = $running_total;

                $promo->debet = $debet;
                $promo->total_daily_debet = $total_daily_debet + $debet;

                $promo->credit = $credit;
                $promo->total_daily_credit = $total_daily_credit + $credit;

                $promo->transfer = $transfer;
                $promo->total_daily_transfer = $total_daily_transfer + $transfer;
                $promo->cash = $cash;
                $promo->total_daily_cash = $total_daily_cash + $cash;

                $promo->promo->amount_due = $promo->amount_due - $promo->amount_paid;

                $processedTransactions[] = $promo;
            }
        }
        return $processedTransactions;
    }

    function processProductPayments($product_payments){
        $processedTransactions = [];

        $running_total = 0;

        $total_product_cash = 0;
        $total_product_transfer = 0;
        $total_product_debet = 0;
        $total_product_credit = 0;
        $total_product_owed = 0;

        if(count($product_payments) > 0){
            foreach($product_payments as $product_payment){
                foreach($product_payment->productpayment_productpaymentdetail as $product_payment_detail){

                    if($product_payment_detail->productpaymentdetail_product->product_producttype->type != 'Air'){
                        $cash = 0;
                        $transfer = 0;
                        $debet = 0;
                        $credit = 0;

                        if($product_payment->payment_method_id == 1){
                            $cash = ($product_payment_detail->price * $product_payment_detail->qty) - ($product_payment_detail->discount_value * $product_payment_detail->qty);
                        }elseif($product_payment->payment_method_id == 2){
                            $debet = ($product_payment_detail->price * $product_payment_detail->qty) - ($product_payment_detail->discount_value * $product_payment_detail->qty);
                        }elseif($product_payment->payment_method_id == 3){
                            $credit = ($product_payment_detail->price * $product_payment_detail->qty) - ($product_payment_detail->discount_value * $product_payment_detail->qty);
                        }elseif($product_payment->payment_method_id == 4){
                           $transfer = ($product_payment_detail->price * $product_payment_detail->qty) - ($product_payment_detail->discount_value * $product_payment_detail->qty);
                        }

                        $running_total = (($product_payment_detail->price * $product_payment_detail->qty) - ($product_payment_detail->discount_value * $product_payment_detail->qty));

                        $product_payment_detail->running_total = $running_total;


                        $product_payment_detail->debet = $debet;
                        $product_payment_detail->total_product_debet = $total_product_debet + $debet;
                        $product_payment_detail->credit = $credit;
                        $product_payment_detail->total_product_credit = $total_product_credit + $credit;
                        $product_payment_detail->transfer = $transfer;
                        $product_payment_detail->total_product_transfer = $total_product_transfer + $transfer;
                        $product_payment_detail->cash = $cash;

                        $product_payment_detail->total_product_cash = $total_product_cash + $cash;

                        $product_payment_detail->money_owed = 0;
                        $product_payment_detail->member_id = $product_payment->member_id;

                        if(!empty($product_payment->member_id)){
                            $product_payment_detail->name = $product_payment->productpayment_user->name;
                            $product_payment_detail->key_tag_id = $product_payment->productpayment_user->user_memberpersonal->key_tag_id;
                        }else{
                            $product_payment_detail->name = '-';
                            $product_payment_detail->key_tag_id = '-';
                        }

                        $processedTransactions[] = $product_payment_detail;
                    }
                }
            }
        }

        return $processedTransactions;
    }


    public function monthly_sales($month, $year, $action='', $club=''){
        //PROMO MEMBERSHIP
        if($club != ''){
            $promo_members = GuestMemberPayment::whereMonth('payment_date', $month)->whereYear('payment_date', $year)
                ->with('gmpayment_gmmember')
                ->with('gmpayment_gmmember.gmmember_guest')
                ->whereHas('gmpayment_gmmember', function ($q) use ($club) {
                    $q->whereHas('gmmember_guest', function ($r) use ($club)  {
                        $r->where('club_id', '=', $club);
                    });
                })
                ->get();
        }else{
            $promo_members = GuestMemberPayment::whereMonth('payment_date', $month)->whereYear('payment_date', $year)
                ->with('gmpayment_gmmember')
                ->with('gmpayment_gmmember.gmmember_guest')
                ->get();
        }
        $data = [];
        $promoId = 41; //750k
        $accurateItem = '750K';

        $paymentMethod = [
            '1'=> 'Tunai',
            '2'=> 'Debet',
            '3'=> 'Kredit',
            '4'=> 'Transfer',
        ];

        //dd($promo_members);
        $csv = [];
        foreach($promo_members as $member){
            $bank = Bank::findOrFail($member->bank_id);
            if($action === 'print'){
                $data[] = [
                    'id' => $member->id,
                    'name' => str_replace("%20", " ", $member->gmpayment_gmmember->gmmember_guest->fullname),
                    'membership_type' => $promoId,
                    'fa_date' => $member->payment_date,
                    'pay' => $member->amount_paid,
                    'club_id' => $member->gmpayment_gmmember->gmmember_guest->club_id,
                    'detailItem'=> [
                        [
                            "itemNo" => $accurateItem,
                            "quantity" => 1,
                            "unitPrice" => $member->amount_paid,
                            'detailNotes'=> $member->payment_status .' 750K Promo | Payment Method: '.$paymentMethod[$member->payment_type_id]. ' | Bank: '.$bank->bank_name .' | Payment Date: '.$member->payment_date
                        ]
                    ]
                ];

                /* $csv[] =
                    $member->id .','.
                    str_replace("%20", " ", $member->gmpayment_gmmember->gmmember_guest->fullname).','.
                    $member->payment_date.','.
                    $member->amount_paid.','.
                    $member->gmpayment_gmmember->gmmember_guest->club_id.','.
                    Accurate::accurateBranch($member->gmpayment_gmmember->gmmember_guest->club_id); */
                $csv[] =
                    $member->id .','.
                    str_replace("%20", " ", $member->gmpayment_gmmember->gmmember_guest->fullname).','.
                    $member->payment_date.','.
                    $member->amount_paid.','.
                    $member->gmpayment_gmmember->gmmember_guest->club_id;


            }elseif($action === 'send'){
                $data = [
                    'id' => $member->id,
                    'name' => str_replace("%20", " ", $member->gmpayment_gmmember->gmmember_guest->fullname),
                    'membership_type' => $promoId,
                    'fa_date' => $member->payment_date,
                    'pay' => $member->amount_paid,
                    'club_id' => $member->gmpayment_gmmember->gmmember_guest->club_id,
                    'detailItem'=> [
                        [
                            "itemNo" => $accurateItem,
                            "quantity" => 1,
                            "unitPrice" => $member->amount_paid,
                            'detailNotes'=> $member->payment_status .' 750K Promo | Payment Method: '.$paymentMethod[$member->payment_type_id]. ' | Bank: '.$bank->bank_name .' | Payment Date: '.$member->payment_date
                        ]
                    ]
                ];
                //$this->store_accurate($member->gmpayment_gmmember->gmmember_guest->club_id, $data);
            }
        }
        if($action === 'print'){
            print_r($data);
        }
    }

    /*****************************/
    /**** ACCURATE SCRIPT ***/
    /*****************************/

    public function store_accurate($club_id, $userData)
    {
        //GET VARIABLES
        $accurate = AccurateAuth::find(1);
        //$databaseID = Accurate::accurateClub($club_id);
        //$openDB = Accurate::AccurateOpenDB($databaseID, $accurate->access_token );
        //$branchID = Accurate::accurateBranch($club_id);

        $accurateClub = Accurate::accurateClub($club_id);
        $openDB = '';
        $branchID = '';
        $databaseID = '';

        if($accurateClub){
            $databaseID = $accurateClub->accurate_id;
            $openDB = Accurate::AccurateOpenDB($databaseID, $accurate->access_token );
            $branchID = $accurateClub->accurate_branch;

            //CHECK IF USER EXIST IN ACCURATE
            //IF USER DO NOT EXIST, CREATE USING THE API
            $accurateUser = Accurate::AccurateCheckMember($openDB, $userData);
            if($accurateUser){
                //do nothing
            }else{
                $accurateUser = Accurate::AccurateCreateMember($openDB, $userData);
            }

            //CHECK IF ITEM CODE EXIST
            //IF ITEM CODE DO NOT EXIST, CREATE USING THE API

            $invoiceData = [
                "customerNo" => $accurateUser['customerNo'],
                "transDate" => date("d/m/Y"),
                "branchId" => $branchID,
                "detailItem" => $userData['detailItem']
            ];

            //CREATE SALES INVOICE ONE CUSTOMER & ITEM IS TRUE
            $accurateInvoice = Accurate::AccurateCreateInvoice($openDB, $invoiceData);

            Storage::put('invoices/promo/'.date("d-m-Y").'_'.$accurateUser['customerNo'].'_750k.txt', json_encode($accurateInvoice));
        }

    }

    public function print_daily_sales(Request $request){
        $payment_date = Carbon::today();
        if (!empty($request->d)):
            $payment_date = $request->d;
        endif;

        //Check Transfer History Added 28/07/2019
        $user1 = User::where('club_id','=',$club)->whereDoesntHave('user_transferhistory')->pluck('id');
        $user2_temp = DB::select("SELECT user_id
                    FROM transfer_histories
                    WHERE created_at IN (SELECT min(created_at) FROM transfer_histories GROUP BY user_id)
                      AND created_at > '".$payment_date."'
                    AND club_id = ".$club);

        $user3_temp = DB::select("SELECT transfer_histories.user_id
                    FROM transfer_histories, log_member_memberships
                    WHERE transfer_histories.created_at IN (SELECT min(created_at) FROM transfer_histories GROUP BY user_id)
                      AND transfer_histories.created_at > '".$payment_date."'
                    AND transfer_histories.club_id != ".$club."
                    AND transfer_histories.user_id = log_member_memberships.user_id
                    AND log_member_memberships.club_id = ".$club."
                    AND log_member_memberships.from_date <= '".$payment_date."'
                    AND log_member_memberships.thru_date >= '".$payment_date."'"
        );

        $user4_temp = DB::select(
            "SELECT transfer_histories.user_id
                    FROM transfer_histories
                    JOIN log_member_memberships
                    ON transfer_histories.user_id = log_member_memberships.user_id
                    WHERE transfer_histories.from_date IN (SELECT min(from_date) FROM transfer_histories GROUP BY user_id)
                      AND transfer_histories.from_date <= '" . $payment_date . "'
                    AND transfer_histories.club_id != " . $club . "
                    AND log_member_memberships.club_id = " . $club . "
                    AND log_member_memberships.from_date <= '" . $payment_date . "'
                    AND log_member_memberships.thru_date >= '" . $payment_date . "'"
        );

        $user2 = [];
        $user3 = [];
        $user4 = [];
        foreach ($user2_temp as $key => $value) {
            array_push($user2, $value->user_id);
        }
        foreach ($user3_temp as $key => $value) {
            array_push($user3, $value->user_id);
        }
        foreach ($user4_temp as $key => $value) {
            array_push($user4, $value->user_id);
        }
        $listArr = $user1->merge($user2);
        $listArr = $listArr->merge($user3);
        $listArr = $listArr->merge($user4);

        //Membership New
        $member_payments = MemberPayment::whereDate('payment_date', '=', Carbon::parse($payment_date)->toDateString())
            ->whereHas('memberpayment_user', function ($u) use($listArr) {
                $u->whereIn('id',$listArr);
            })->get();


        foreach ($member_payments as $payment):
            $member_payment = MemberPayment::findOrFail($payment->id);
            $membership_types = LogMemberStatus::where('user_id', '=', $member_payment->user_id)
                ->whereDate('created_at', '<=', Carbon::parse($member_payment->created_at)->toDateString())
                ->orderBy('created_at', 'desc')
                ->get();

            $the_membership_type = '';
            $log_membership_id = '';
            foreach ($membership_types as $membership_type):
                if (empty($the_membership_type)):
                    $the_membership_type = $membership_type->membership_type_id;
                    $log_membership_id = $membership_type->id;
                else:
                    if (Carbon::parse($member_payment->created_at)->format('dmYhi') == Carbon::parse($membership_type->created_at)->format('dmYhi')):
                        $the_membership_type = $membership_type->membership_type_id;
                        $log_membership_id = $membership_type->id;
                    endif;
                endif;
            endforeach;

            $membership = MembershipType::findOrFail($the_membership_type);
            $payment->setAttribute('membership_type_name', $membership->membership_type_name);

            //Get Money Owed
            if ($member_payment->payment_category == 'First'):
                if (($membership->payment_type == 'AD') || ($membership->payment_type == 'Cash')):
                    $money_owed = ($membership->duration - 1) * $member_payment->mf_amount;
                elseif ($membership->payment_type == 'Prepaid'):
                    $money_owed = $member_payment->mf_amount;
                endif;
                $payment->setAttribute('money_owed', $money_owed);
            elseif ($member_payment->payment_category == 'Monthly'):
                $log_mt = LogMemberStatus::findOrFail($log_membership_id);

                //First Debt
                if (($membership->payment_type == 'AD') || ($membership->payment_type == 'Cash')):
                    $first_money_owed = 0;

                    $first_payment = MemberPayment::where('user_id', '=', $member_payment->user_id)
                        ->where('payment_category', '=', 'First')
                        ->whereDate('created_at', '>=', Carbon::parse($log_mt->created_at)->toDateString())
                        ->orderBy('payment_date', 'asc')
                        ->first();

                    $first_money_owed = $first_payment->rest_of_bill;

                    if (!empty($first_payment->memberpayment_memberdebtpayment)):
                        foreach ($first_payment->memberpayment_memberdebtpayment as $first_payment_debt):
                            if (strtotime($first_payment_debt->payment_date) > strtotime($payment_date)):
                                $first_money_owed = $first_money_owed + $first_payment_debt->amount_debt;
                            endif;
                        endforeach;
                    endif;
                endif;

                //Monthly Debt
                $monthly_payment = MemberPayment::where('user_id', '=', $member_payment->user_id)
                    ->where('payment_category', '=', 'Monthly')
                    ->whereDate('created_at', '>=', Carbon::parse($log_mt->created_at)->toDateString())
                    ->orderBy('payment_date', 'asc')
                    ->get();

                $monthly_money_owed = 0;
                if (count($monthly_payment) > 0):
                    foreach ($monthly_payment as $monthly_debt):
                        if ($monthly_debt->payment_status != 'Paid'):
                            $monthly_money_owed = $monthly_money_owed + $monthly_debt->rest_of_bill;
                        else:
                            if (strtotime($monthly_debt->payment_date) > strtotime($payment_date)):
                                $monthly_money_owed = $monthly_money_owed + $monthly_debt->amount_due;
                            endif;
                        endif;
                    endforeach;
                endif;

                //Total Debt
                if (($membership->payment_type == 'AD') || ($membership->payment_type == 'Cash')):
                    $total_money_owed = $first_money_owed + $monthly_money_owed;
                elseif ($membership->payment_type == 'Prepaid'):
                    $total_money_owed = $monthly_money_owed;
                endif;
                $payment->setAttribute('total_money_owed', $total_money_owed);
            endif;
        endforeach;

        //Product
        $product_payments = ProductPayment::where('club_id', '=', $club)
            ->whereDate('payment_date', '=', Carbon::parse($payment_date)->toDateString())
            ->get();

        //Locker
        $locker_payments = LockerPayment::whereHas('lockerpayment_memberlocker', function ($q) use ($listArr){
            $q->whereIn('user_id',$listArr);
        })->whereDate('payment_date', '=', Carbon::parse($payment_date)->toDateString())
            ->whereHas('lockerpayment_locker', function ($locker) {
                $locker->where('club_id', '=', $club);
            })
            ->get();

        $promo_payments = GuestMemberPayment::whereDate('payment_date','=',Carbon::parse($payment_date)->toDateString())
                            ->whereHas('gmpayment_gmmember', function($q){
                                $q->whereHas('gmmember_guest',function($r){
                                    $r->where('club_id','=',$club);
                                });
                            })->get();

        return view('cf_lite.financial-management.daily-sales-report-print-v2',compact('product_payments','locker_payments','member_payments','promo_payments'));
    }

    public function export_daily_sales(Request $request) {
        $payment_date = Carbon::today();
        if (!empty($request->d)):
            $payment_date = $request->d;
        endif;

        //Check Transfer History Added 28/07/2019
        $user1 = User::where('club_id','=',$club)->whereDoesntHave('user_transferhistory')->pluck('id');
        $user2_temp = DB::select("SELECT user_id
                    FROM transfer_histories
                    WHERE created_at IN (SELECT min(created_at) FROM transfer_histories GROUP BY user_id)
                      AND created_at > '".$payment_date."'
                    AND club_id = ".$club);
        $user2 = [];
        foreach ($user2_temp as $key => $value) {
            array_push($user2, $value->user_id);
        }
        $listArr = $user1->merge($user2);
        //Membership New
        $member_payments = MemberPayment::whereDate('payment_date', '=', Carbon::parse($payment_date)->toDateString())
            ->whereHas('memberpayment_user', function ($u) use($listArr) {
                $u->whereIn('id',$listArr);
            })->get();


        foreach ($member_payments as $payment):
            $member_payment = MemberPayment::findOrFail($payment->id);
            $membership_types = LogMemberStatus::where('user_id', '=', $member_payment->user_id)
                ->whereDate('created_at', '<=', Carbon::parse($member_payment->created_at)->toDateString())
                ->orderBy('created_at', 'desc')
                ->get();

            $the_membership_type = '';
            $log_membership_id = '';
            foreach ($membership_types as $membership_type):
                if (empty($the_membership_type)):
                    $the_membership_type = $membership_type->membership_type_id;
                    $log_membership_id = $membership_type->id;
                else:
                    if (Carbon::parse($member_payment->created_at)->format('dmYhi') == Carbon::parse($membership_type->created_at)->format('dmYhi')):
                        $the_membership_type = $membership_type->membership_type_id;
                        $log_membership_id = $membership_type->id;
                    endif;
                endif;
            endforeach;

            $membership = MembershipType::findOrFail($the_membership_type);
            $payment->setAttribute('membership_type_name', $membership->membership_type_name);

            //Get Money Owed
            if ($member_payment->payment_category == 'First'):
                if (($membership->payment_type == 'AD') || ($membership->payment_type == 'Cash')):
                    $money_owed = ($membership->duration - 1) * $member_payment->mf_amount;
                elseif ($membership->payment_type == 'Prepaid'):
                    $money_owed = $member_payment->mf_amount;
                endif;
                $payment->setAttribute('money_owed', $money_owed);
            elseif ($member_payment->payment_category == 'Monthly'):
                $log_mt = LogMemberStatus::findOrFail($log_membership_id);

                //First Debt
                if (($membership->payment_type == 'AD') || ($membership->payment_type == 'Cash')):
                    $first_money_owed = 0;

                    $first_payment = MemberPayment::where('user_id', '=', $member_payment->user_id)
                        ->where('payment_category', '=', 'First')
                        ->whereDate('created_at', '>=', Carbon::parse($log_mt->created_at)->toDateString())
                        ->orderBy('payment_date', 'asc')
                        ->first();

                    $first_money_owed = $first_payment->rest_of_bill;

                    if (!empty($first_payment->memberpayment_memberdebtpayment)):
                        foreach ($first_payment->memberpayment_memberdebtpayment as $first_payment_debt):
                            if (strtotime($first_payment_debt->payment_date) > strtotime($payment_date)):
                                $first_money_owed = $first_money_owed + $first_payment_debt->amount_debt;
                            endif;
                        endforeach;
                    endif;
                endif;

                //Monthly Debt
                $monthly_payment = MemberPayment::where('user_id', '=', $member_payment->user_id)
                    ->where('payment_category', '=', 'Monthly')
                    ->whereDate('created_at', '>=', Carbon::parse($log_mt->created_at)->toDateString())
                    ->orderBy('payment_date', 'asc')
                    ->get();

                $monthly_money_owed = 0;
                if (count($monthly_payment) > 0):
                    foreach ($monthly_payment as $monthly_debt):
                        if ($monthly_debt->payment_status != 'Paid'):
                            $monthly_money_owed = $monthly_money_owed + $monthly_debt->rest_of_bill;
                        else:
                            if (strtotime($monthly_debt->payment_date) > strtotime($payment_date)):
                                $monthly_money_owed = $monthly_money_owed + $monthly_debt->amount_due;
                            endif;
                        endif;
                    endforeach;
                endif;

                //Total Debt
                if (($membership->payment_type == 'AD') || ($membership->payment_type == 'Cash')):
                    $total_money_owed = $first_money_owed + $monthly_money_owed;
                elseif ($membership->payment_type == 'Prepaid'):
                    $total_money_owed = $monthly_money_owed;
                endif;
                $payment->setAttribute('total_money_owed', $total_money_owed);
            endif;
        endforeach;

        //Product
        $product_payments = ProductPayment::where('club_id', '=', $club)
            ->whereDate('payment_date', '=', Carbon::parse($payment_date)->toDateString())
            ->get();

        //Locker
        $locker_payments = LockerPayment::whereHas('lockerpayment_memberlocker', function ($q) use ($listArr){
            $q->whereIn('user_id',$listArr);
        })->whereDate('payment_date', '=', Carbon::parse($payment_date)->toDateString())
            ->get();

        $promo_payments = GuestMemberPayment::whereDate('payment_date','=',Carbon::parse($payment_date)->toDateString())
            ->whereHas('gmpayment_gmmember', function($q){
                $q->whereHas('gmmember_guest',function($r){
                    $r->where('club_id','=',$club);
                });
            })->get();

        return view('cf_lite.financial-management.export-daily-sales-report',compact('product_payments','locker_payments','member_payments','promo_payments'));
    }

    public function daily_rewards(Request $request) {
        // Initialize Date
        $date = Carbon::today();
        if (!empty($request->d)):
            $date = Carbon::parse($request->d);
        endif;

        $products = ProductPaymentDetail::where('discount_percent', '=', 100)
            ->whereHas('productpaymentdetail_productpayment', function ($header) use ($date) {
                $header->where('is_reward', '=', true)
                    ->where('club_id', '=', $club)
                    ->where('pay', '=', 0)
                    ->whereDate('payment_date', '=', Carbon::parse($date)->toDateString());
            })
            ->get();

        return view('cf_lite.financial-management.daily-rewards-report', compact('products'));
    }
}
