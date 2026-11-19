<?php

namespace App\Http\Controllers;

use App\Model\GuestMembership\GuestMemberPayment;
use App\Model\Locker\LockerPayment;
use App\Model\Log\LogMemberStatus;
use App\Model\MasterData\MembershipType;
use App\Model\Member\MemberDeposit;
use App\Model\Payment\MemberPayment;
use App\Model\Product\ProductPayment;
use App\Model\Product\ProductPaymentDetail;
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

class FinancialManagementReportController extends Controller
{
    public function daily_sales(Request $request) {
        $payment_date = Carbon::today();
        if (!empty($request->d)):
            $payment_date = $request->d;
        endif;

        //Check Transfer History Added 28/07/2019
        $user1 = User::where('club_id','=',Auth::user()->club_id)
                        ->whereDoesntHave('user_transferhistory')->pluck('id');
        // $user2_temp = DB::select("SELECT user_id
        //             FROM transfer_histories
        //             WHERE created_at IN (SELECT min(created_at) FROM transfer_histories GROUP BY user_id)
        //               AND created_at > '".$payment_date."'
        //             AND club_id = ".Auth::user()->club_id);

        $user2_temp = DB::select("SELECT user_id
                    FROM transfer_histories
                    WHERE from_date IN (SELECT min(from_date) FROM transfer_histories GROUP BY user_id)
                      AND from_date > '" . $payment_date . "'
                    AND club_id = " . Auth::user()->club_id);

        $user3_temp = DB::select("SELECT transfer_histories.user_id
                    FROM transfer_histories, log_member_memberships
                    WHERE transfer_histories.created_at IN (SELECT min(created_at) FROM transfer_histories GROUP BY user_id)
                      AND transfer_histories.created_at > '".$payment_date."'
                    AND transfer_histories.club_id != ".Auth::user()->club_id."
                    AND transfer_histories.user_id = log_member_memberships.user_id
                    AND log_member_memberships.club_id = ".Auth::user()->club_id."
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
                    AND transfer_histories.club_id != " . Auth::user()->club_id . "
                    AND log_member_memberships.club_id = " . Auth::user()->club_id . "
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
            })->get();

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
        $product_payments = ProductPayment::where('club_id', '=', Auth::user()->club_id)
            ->whereDate('payment_date', '=', Carbon::parse($payment_date)->toDateString())
            ->where('is_reward', '=', false)
            ->get();

        //Locker
//        $locker_payments = LockerPayment::whereHas('lockerpayment_user', function ($q) {
//            $q->where('club_id', '=', Auth::user()->club_id);
//        })
//            ->whereDate('payment_date', '=', Carbon::parse($payment_date)->toDateString())
//            ->get();
        $locker_payments = LockerPayment::whereHas('lockerpayment_memberlocker', function ($q) use ($listArr){
            $q->whereIn('user_id',$listArr);
        })->whereDate('payment_date', '=', Carbon::parse($payment_date)->toDateString())
            ->get();


        //MEMBER DEPOSIT
        $member_deposits = MemberDeposit::where('status', '=', 'Done')
            ->where('payment_type', '=', 'Payment')
            ->where('debet', '<>', '0')
            ->whereDate('payment_date', '=', Carbon::parse($payment_date))
            ->whereHas('memberdeposit_user', function ($club) {
                $club->where('club_id', '=', Auth::user()->club_id);
            })
            ->get();

        //PROMO MEMBERSHIP
        $promo_members = GuestMemberPayment::whereDate('payment_date', '=', Carbon::parse($payment_date)->toDateString())
            ->whereHas('gmpayment_gmmember', function ($q) {
                $q->whereHas('gmmember_guest', function ($r) {
                    $r->where('club_id', '=', Auth::user()->club_id);
                });
            })->get();

        return view('cf_lite.financial-management.daily-sales-report', compact('member_payments', 'product_payments', 'locker_payments', 'member_deposits', 'promo_members'));
    }

    public function daily_sales_test(Request $request) {
        $payment_date = Carbon::today();
        if (!empty($request->d)):
            $payment_date = $request->d;
        endif;

        //Check Transfer History Added 28/07/2019
        $user1 = User::where('club_id','=',Auth::user()->club_id)
                        ->whereDoesntHave('user_transferhistory')->pluck('id');
        // $user2_temp = DB::select("SELECT user_id
        //             FROM transfer_histories
        //             WHERE created_at IN (SELECT min(created_at) FROM transfer_histories GROUP BY user_id)
        //               AND created_at > '".$payment_date."'
        //             AND club_id = ".Auth::user()->club_id);

        $user2_temp = DB::select("SELECT user_id
                    FROM transfer_histories
                    WHERE from_date IN (SELECT min(from_date) FROM transfer_histories GROUP BY user_id)
                      AND from_date > '" . $payment_date . "'
                    AND club_id = " . Auth::user()->club_id);

        $user3_temp = DB::select("SELECT transfer_histories.user_id
                    FROM transfer_histories, log_member_memberships
                    WHERE transfer_histories.created_at IN (SELECT min(created_at) FROM transfer_histories GROUP BY user_id)
                      AND transfer_histories.created_at > '".$payment_date."'
                    AND transfer_histories.club_id != ".Auth::user()->club_id."
                    AND transfer_histories.user_id = log_member_memberships.user_id
                    AND log_member_memberships.club_id = ".Auth::user()->club_id."
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
                    AND transfer_histories.club_id != " . Auth::user()->club_id . "
                    AND log_member_memberships.club_id = " . Auth::user()->club_id . "
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
            })->get();

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
        $product_payments = ProductPayment::where('club_id', '=', Auth::user()->club_id)
            ->whereDate('payment_date', '=', Carbon::parse($payment_date)->toDateString())
            ->where('is_reward', '=', false)
            ->get();

        //Locker
//        $locker_payments = LockerPayment::whereHas('lockerpayment_user', function ($q) {
//            $q->where('club_id', '=', Auth::user()->club_id);
//        })
//            ->whereDate('payment_date', '=', Carbon::parse($payment_date)->toDateString())
//            ->get();
        $locker_payments = LockerPayment::whereHas('lockerpayment_memberlocker', function ($q) use ($listArr){
            $q->whereIn('user_id',$listArr);
        })->whereDate('payment_date', '=', Carbon::parse($payment_date)->toDateString())
            ->get();


        //MEMBER DEPOSIT
        $member_deposits = MemberDeposit::where('status', '=', 'Done')
            ->where('payment_type', '=', 'Payment')
            ->where('debet', '<>', '0')
            ->whereDate('payment_date', '=', Carbon::parse($payment_date))
            ->whereHas('memberdeposit_user', function ($club) {
                $club->where('club_id', '=', Auth::user()->club_id);
            })
            ->get();

        //PROMO MEMBERSHIP
        $promo_members = GuestMemberPayment::whereDate('payment_date', '=', Carbon::parse($payment_date)->toDateString())
            ->whereHas('gmpayment_gmmember', function ($q) {
                $q->whereHas('gmmember_guest', function ($r) {
                    $r->where('club_id', '=', Auth::user()->club_id);
                });
            })->get();

        return view('cf_lite.financial-management.daily-sales-report-test', compact('member_payments', 'product_payments', 'locker_payments', 'member_deposits', 'promo_members'));
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
        $user1 = User::where('club_id','=',Auth::user()->club_id)->whereDoesntHave('user_transferhistory')->pluck('id');
        $user2_temp = DB::select("SELECT user_id
                    FROM transfer_histories
                    WHERE created_at IN (SELECT min(created_at) FROM transfer_histories GROUP BY user_id)
                      AND created_at > '".$payment_date."'
                    AND club_id = ".Auth::user()->club_id);

        $user3_temp = DB::select("SELECT transfer_histories.user_id
                    FROM transfer_histories, log_member_memberships
                    WHERE transfer_histories.created_at IN (SELECT min(created_at) FROM transfer_histories GROUP BY user_id)
                      AND transfer_histories.created_at > '".$payment_date."'
                    AND transfer_histories.club_id != ".Auth::user()->club_id."
                    AND transfer_histories.user_id = log_member_memberships.user_id
                    AND log_member_memberships.club_id = ".Auth::user()->club_id."
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
                    AND transfer_histories.club_id != " . Auth::user()->club_id . "
                    AND log_member_memberships.club_id = " . Auth::user()->club_id . "
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
        $product_payments = ProductPayment::where('club_id', '=', Auth::user()->club_id)
            ->whereDate('payment_date', '=', Carbon::parse($payment_date)->toDateString())
            ->get();

        //Locker
        $locker_payments = LockerPayment::whereHas('lockerpayment_memberlocker', function ($q) use ($listArr){
            $q->whereIn('user_id',$listArr);
        })->whereDate('payment_date', '=', Carbon::parse($payment_date)->toDateString())
            ->whereHas('lockerpayment_locker', function ($locker) {
                $locker->where('club_id', '=', Auth::user()->club_id);
            })
            ->get();

        $promo_payments = GuestMemberPayment::whereDate('payment_date','=',Carbon::parse($payment_date)->toDateString())
                            ->whereHas('gmpayment_gmmember', function($q){
                                $q->whereHas('gmmember_guest',function($r){
                                    $r->where('club_id','=',Auth::user()->club_id);
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
        $user1 = User::where('club_id','=',Auth::user()->club_id)->whereDoesntHave('user_transferhistory')->pluck('id');
        $user2_temp = DB::select("SELECT user_id
                    FROM transfer_histories
                    WHERE created_at IN (SELECT min(created_at) FROM transfer_histories GROUP BY user_id)
                      AND created_at > '".$payment_date."'
                    AND club_id = ".Auth::user()->club_id);
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
        $product_payments = ProductPayment::where('club_id', '=', Auth::user()->club_id)
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
                    $r->where('club_id','=',Auth::user()->club_id);
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
                    ->where('club_id', '=', Auth::user()->club_id)
                    ->where('pay', '=', 0)
                    ->whereDate('payment_date', '=', Carbon::parse($date)->toDateString());
            })
            ->get();

        return view('cf_lite.financial-management.daily-rewards-report', compact('products'));
    }
}
