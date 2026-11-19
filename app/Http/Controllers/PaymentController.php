<?php

namespace App\Http\Controllers;

use App\AutoNumber;
use App\Model\Appointment\GuestAppointmentStatus;
use App\Model\Club\ClubInfo;
use App\Model\DigitalProduct\DigitalProductInventory;
use App\Model\DigitalProduct\DigitalProductPayment;
use App\Model\GuestMembership\GuestMembership;
use App\Model\MasterData\Bank;
use App\Model\MasterData\MemberSource;
use App\Model\Member\CoachMember;
use App\Model\Member\ContinueMembership;
use App\Model\Member\MemberDeposit;
use App\Model\Member\MemberDepositInformation;
use App\Model\Payment\MemberPaymentVoucher;
use App\Model\Payment\MembershipChangingDeposit;
use App\Model\Staff\CoachSequence;
use App\Model\Voucher\Voucher;
use App\Model\Workout\Workout;
use function Couchbase\basicDecoderV1;
use phpDocumentor\Reflection\DocBlock\Tags\Source;
use Session;
use App\Http\Requests\FirstPaymentRequest;
use App\Http\Requests\MemberDebtPaymentRequest;
use App\Model\Appointment\GuestAppointment;
use App\Model\Club\ClubServiceFee;
use App\Model\DigitalProduct\DigitalProduct;
use App\Model\Guest\BuddyReferralLead;
use App\Model\Guest\Referrer;
use App\Model\Log\LogMemberStatus;
use App\Model\MasterData\MembershipType;
use App\Model\MasterData\PaymentMethod;
use App\Model\Member\MemberPersonal;
use App\Model\Member\MemberStatus;
use App\Model\Payment\MemberDebtPayment;
use App\Model\Payment\MemberPayment;
use App\Model\Tax\TaxConfig;
use App\Model\Tax\Taxtype;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Keygen\Keygen;

use App\AccurateAuth;
use App\Helpers\Accurate;
use stdClass;
use Storage;
use App\Services\ApiClientService;
use Illuminate\Support\Facades\DB;

require_once __DIR__.'/../../WebClientPrint/WebClientPrint.php';
use Neodynamic\SDK\Web\WebClientPrint;
class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('wm-only');
    }

    public function index() {
        $member_payments = MemberPayment::where('payment_category', '=', 'First')
            ->where('rest_of_bill', '>', '0')
            ->where('payment_status', '=', 'Halfpaid')
            ->whereHas('memberpayment_user', function ($q) {
                $q->where('club_id', '=', Auth::user()->club_id);
            })
            ->orderBy('payment_date', 'asc')
            ->take(6)
            ->get();

        return view('cf_lite.payments.debt-member-list', compact('member_payments'));
    }


    //Fitness Assessment Payment
    public function payment_fa_detail($id) {
        if (!empty(session('premember'))):
            session(['payment_type' => 'premember']);
            $membership_type = MembershipType::findOrFail($id);
            $premember = BuddyReferralLead::findOrFail(session('premember'));

            $premember_club = GuestAppointment::where('guest_id', '=', $premember->id)
                ->where('guest_status_after', '=', 'Lead')
                ->whereHas('guestappointment_gastatus', function($q) {
                    $q->where('status_id', '=', '5')
                        ->whereNotNull('sa_date');
                })->orderBy('created_at', 'desc')->first();
            $club_id = $premember_club->club_id;

            session(['club' => $club_id]);

            $csf = ClubServiceFee::where('club_id', '=', $club_id)
                ->where('membership_type_id', '=', $id)
                ->where('thru_date', null)
                ->first();

            $payment_methods = PaymentMethod::availableForPayment()->orderBy('payment_method', 'desc')->pluck('payment_method', 'id');

//            $tax_types = Taxtype::whereHas('taxtype_taxconfig', function($q) use ($club_id) {
//                $q->where('club_id', '=', $club_id)
//                    ->where('thru_date', null);
//            })->pluck('tax_type', 'id');

            //Payment Number added 140218
            $temp_auto_number = AutoNumber::findOrFail('1');

            //FA Coaches added 01032018
            $fa_coaches = User::where('club_id', '=', Auth::user()->club_id)
                ->where('role_id', '<>', '7')
                ->orderBy('name', 'asc')
                ->pluck('name', 'id');

            $banks = Bank::where('id', '<>', 1)
                ->whereHas('bank_club', function ($q){
                    $q->where('club_id', '=', Auth::user()->club_id);
                })->get();

            return view('cf_lite.payments.payment_detail-v2', compact('membership_type', 'premember', 'csf', 'payment_methods', 'temp_auto_number', 'club_id', 'fa_coaches'/*, 'tax_types'*/, 'banks'));
        endif;
    }

    public function store_first_payment($id, FirstPaymentRequest $request) {
        if (!empty(session('premember'))):
            //Adding Member (to users table)
            $premember = BuddyReferralLead::findOrFail(session('premember'));

            $user = new User;
            $user->name = $premember->fullname;
            $user->email = date('dmYhis', strtotime(Carbon::now())).'@curvesmember.com';
            $user->password = bcrypt('curvesindonesia');
            $user->role_id = '7';
            $user->club_id = session('club');
            $user->username = date('dnygi', strtotime(Carbon::now())).'.cm.'.$premember->id;
            $user->save();


            //Update Auto Number
            $temp_auto_number = AutoNumber::findOrFail('1');
            $payment_number = $temp_auto_number->current_number;

            $temp_auto_number->current_number = $temp_auto_number->current_number + 1;
            $temp_auto_number->save();


            //Adding Member Payment
            $member_payment = new MemberPayment;
//            $member_payment->payment_number = $request->payment_number;
            $member_payment->payment_number = sprintf('%08d', $payment_number).$request->payment_number;
            $member_payment->user_id = $user->id;
            $member_payment->sf_amount = $request->sf_amount;
            $member_payment->mf_amount = $request->mf_amount;
            $member_payment->dc_amount = $request->discount;

            /*if (!empty($request->tax_type)):
                $member_payment->tax_type_id = $request->tax_type;
            endif;*/
            $member_payment->tax_type_id = '1';

            /*$member_payment->tax_amount = $request->tax_amount;
            $member_payment->tax_percentage = $request->tax_percentage;*/
            $member_payment->tax_amount = round($request->total / 11);
            $member_payment->tax_percentage = 10;

            $member_payment->amount_due = $request->total;
            $member_payment->payment_category = 'First';

            if (strtotime($request->pay_date) == strtotime(Carbon::today())):
                $member_payment->payment_date = Carbon::createFromFormat('Y-m-d H:i', Carbon::now()->format('Y-m-d H:i'), session('timezone'));
            else:
                $member_payment->payment_date = $request->pay_date;
            endif;

            $member_payment->payment_method_id = $request->payment_method;
            $member_payment->amount_paid = $request->total_pay;

            if (($request->total_bill - $request->total_pay) > 0):
                if ($request->rest_of_bill == ($request->total_bill - $request->total_pay)):
                    $member_payment->rest_of_bill = $request->rest_of_bill;
                else:
                    $member_payment->rest_of_bill = ($request->total_bill - $request->total_pay);
                endif;
                $member_payment->payment_status = 'Halfpaid';
            elseif (($request->total_bill - $request->total_pay) == 0):
                $member_payment->payment_status = 'Paid';
            endif;

            /* if ($request->payment_method != 1):
                $member_payment->bank_id = $request->bank;
            elseif ($request->payment_method == 1):
                $member_payment->bank_id = 1;
            endif; */

            if ($request->payment_method == 1 || $request->payment_method == 6 || $request->payment_method == 7 ){
                $member_payment->bank_id = 1;
            }else {
                $member_payment->bank_id = $request->bank;
            }

            $member_payment->save();


            //Adding Member Payment Voucher if using Voucher
            if (!empty($request->voucher_code) && !empty($request->voucher_value)):
                $check_voucher = Voucher::where('identity_number', '=', $request->voucher_code)
                    ->whereDate('from_date', '<=', Carbon::now()->toDateString())
                    ->whereDate('thru_date', '>=', Carbon::now()->toDateString())
                    ->where('status', '=', 'Out')
                    ->first();

                if (count($check_voucher) > 0):
                    $member_payment_voucher = new MemberPaymentVoucher;
                    $member_payment_voucher->voucher_code = $request->voucher_code;

                    if ($check_voucher->type_id != '2'):
                        if ($check_voucher->voucher_base == 'value'):
                            if (($request->sf_amount + $request->mf_amount - $request->discount) < $check_voucher->value):
                                $member_payment_voucher->voucher_value = $request->sf_amount + $request->mf_amount - $request->discount;
                            else:
                                $member_payment_voucher->voucher_value = $check_voucher->value;
                            endif;
                        elseif ($check_voucher->voucher_base == 'percent'):
                            $actual_voucher = ($request->sf_amount + $request->mf_amount - $request->discount) * $check_voucher->percent * 0.01;
                            if ($actual_voucher > $check_voucher->value):
                                $actual_voucher = $check_voucher->value;
                            endif;

                            if (($request->sf_amount + $request->mf_amount - $request->discount) < $actual_voucher):
                                $member_payment_voucher->voucher_value = $request->sf_amount + $request->mf_amount - $request->discount;
                            else:
                                $member_payment_voucher->voucher_value = $actual_voucher;
                            endif;
                        endif;
                    endif;
                    $member_payment->memberpayment_memberpaymentvoucher()->save($member_payment_voucher);

                    $voucher = Voucher::findOrFail($check_voucher->id);
                    $voucher->status = 'Used';
                    $voucher->save();
                endif;
            endif;


            //Edit buddy_referral_leads table (status_gm)
            $premember->status_gm = 'Member';
            $premember->save();


            //Edit guest_appointments table (guest_status_after)
            $premember_guest_appointment = GuestAppointment::where('guest_id', '=', $premember->id)
                ->where('guest_status_after', '=', 'Lead')
                ->whereHas('guestappointment_gastatus', function($q) {
                    $q->where('status_id', '=', '5')
                        ->whereNotNull('sa_date');
                })->orderBy('created_at', 'desc')->first();

            $premember_guest_appointment->guest_status_after = 'Member';
            $premember_guest_appointment->save();


            //Adding MemberStatus (member_status table)
            $membership_type = MembershipType::findOrFail($id);

            $member_status = new MemberStatus;
            $member_status->status = 'Active';
            $member_status->membership_type_id = $id;

            if ($membership_type->is_vip == 'general'):
                $the_duration = $membership_type->duration;
            elseif ($membership_type->is_vip == 'vip'):
                $the_duration = 0;
            endif;

            if (strtotime($request->pay_date) == strtotime(Carbon::today())):
                $member_status->from_date = Carbon::createFromFormat('Y-m-d H:i', Carbon::now()->format('Y-m-d H:i'), session('timezone'));
                $thru_date_temp = date('Y-m-d', strtotime("+".$the_duration." months", strtotime($member_status->from_date)));
            else:
                $member_status->from_date = $request->pay_date;
                $thru_date_temp = date('Y-m-d', strtotime("+".$the_duration." months", strtotime($request->pay_date)));
            endif;

            //VIP Member added 02032018
            if (!empty($request->use_vip) && !empty($request->vip_duration)):
                if ($request->vip_option == 'day'):
                    $vip_option = 'days';
                elseif ($request->vip_option == 'month'):
                    $vip_option = 'months';
                elseif ($request->vip_option == 'year'):
                    $vip_option = 'years';
                endif;

                $member_status->thru_date = date('Y-m-d', strtotime("+".$request->vip_duration." ".$vip_option, strtotime($thru_date_temp)));
                $member_status->member_vip_status = 'VIP';
            else:
                $member_status->thru_date = $thru_date_temp;
            endif;

            //Edited 31102018
            if ($membership_type->payment_type == 'Prepaid'):
                if ($request->rest_of_bill == 0):
                    $member_status->payment_status = 'Set';
                endif;
            elseif ($membership_type->payment_type == 'Cash'):
                $member_status->payment_status = 'Set';
            endif;

            $user->user_memberstatus()->save($member_status);


            //Adding LogMemberStatus (Log for Member's Membership History)
            $log_member = new LogMemberStatus;
            $log_member->user_id = $user->id;
            $log_member->membership_type_id = $id;
            $log_member->from_date = $member_status->from_date;
            $log_member->thru_date = $member_status->thru_date;
            $log_member->club_id = session('club');
            $log_member->save();


            //Adding MemberPersonal (member_personals table)
            $member_personal = new MemberPersonal;
            $member_personal->phone = $premember->phone;
            $member_personal->key_tag_id = '9999'.Keygen::numeric(5)->generate();

//            $member_personal->sales_person_id = $premember_guest_appointment->staff_id;
            if (!empty($request->fa_coach)):
                $member_personal->sales_person_id = $request->fa_coach;
            else:
                if (!empty(session('staff_session'))):
                    $member_personal->sales_person_id = session('staff_session');
                else:
                    $member_personal->sales_person_id = Auth::id();
                endif;
            endif;

            $member_personal->guest_id = $premember->id;

            if (strtotime($request->pay_date) == strtotime(Carbon::today())):
                $member_personal->joined = Carbon::createFromFormat('Y-m-d H:i', Carbon::now()->format('Y-m-d H:i'), session('timezone'));
            else:
                $member_personal->joined = $request->pay_date;
            endif;

            $member_personal->created_by = Auth::id();
            $user->user_memberpersonal()->save($member_personal);

            //Adding Member to Coach based on Sequence 08102018
            $club = ClubInfo::findOrFail(session('club'));
            $coach_sequences = CoachSequence::where('club_id', '=', $club->id)
                ->orderBy('sequence', 'desc')
                ->get();

            $max_seq = $coach_sequences[0]->sequence;
            foreach ($coach_sequences->where('sequence', $club->current_sequence) as $coach_seq):
                //Insert to coach member table
                $coach_member = new CoachMember;
                $coach_member->coach_id = $coach_seq->coach_id;
                $user->memberuser_coachmember()->save($coach_member);

                //Update club current sequence test
                if ($club->current_sequence < $max_seq):
                    $club->current_sequence = $club->current_sequence + 1;
                elseif ($club->current_sequence == $max_seq):
                    $club->current_sequence = 1;
                endif;
                $club->save();
            endforeach;
            //Adding Member to Coach based on Sequence 08102018 test

            // Jonviter DEV
            // $member_payment->payment_date = $member_payment->payment_date->toDateTimeString(); 
            // $paymentData = $member_payment->toArray();
            // $apiClient = new ApiClientService();
            // $mobile_curves_api = $apiClient->post("/mobile-curves/payment/{$member_personal->sales_person_id}", $paymentData);
            // Jonviter DEV

            // Amortisasi 30012020
            app('App\Http\Controllers\AccountingAmortizationController')->create_amortization($member_payment, $membership_type, null);

            // Akunting 30012020
            app('App\Http\Controllers\TempJournalController')->membership_payment($member_payment, false);

            //Staff Point Added 21012019
            app('App\Http\Controllers\PointStaffController')->membership_sales_point($user->id, $id, $member_payment->id);
//            return redirect('cf-lite/new-member');
            return redirect('cf-lite/payment/invoice/'.$member_payment->id.'/member');
        endif;
    }


    //Payment Debt
    public function payment_debt_detail($id) {
        $member_payment = MemberPayment::findOrFail($id);
        $payment_methods = PaymentMethod::availableForPayment()->orderBy('payment_method', 'desc')->pluck('payment_method', 'id');

        //Added 130318
        $temp_auto_number = AutoNumber::findOrFail('1');

        //Added 20032019
        $banks = Bank::whereHas('bank_club', function ($q) use ($member_payment) {
            $q->where('club_id', '=', $member_payment->memberpayment_user->club_id);
        })->orderBy('bank_name', 'asc')
            ->get();

        //Added 22032019
        $staffs = User::where('club_id', '=', Auth::user()->club_id)
            ->where('role_id', '<>', '7')
            ->where(function ($q) {
                $q->whereDoesntHave('user_staffinactive')
                    ->orWhere(function ($r) {
                        $r->whereHas('user_staffinactive', function ($s) {
                            $s->where('status', '=', 'Reactivated')
                                ->where('thru_date', null);
                        });
                    });
            })
            ->orderBy('name', 'asc')
            ->pluck('name', 'id');

        $wcpScript = WebClientPrint::createScript(action('WebClientPrintController@processRequest'), action('PrintESCPOSController@firstPaymentInstallment'), Session::getId());

        return view('cf_lite.payments.payment_debt_detail', compact('member_payment', 'payment_methods', 'temp_auto_number', 'banks', 'staffs', 'wcpScript'));
    }

    public function store_payment_debt($id, MemberDebtPaymentRequest $request) {
        //Member Payment
        $member_payment = MemberPayment::findOrFail($id);

        //Update Auto Number
        $temp_auto_number = AutoNumber::findOrFail('1');
        $payment_number = $temp_auto_number->current_number;

        $temp_auto_number->current_number = $temp_auto_number->current_number + 1;
        $temp_auto_number->save();

        $member_payment->amount_paid = $member_payment->amount_paid + $request->total_pay;

        if (($request->total_bill - $request->total_pay) == $request->rest_of_bill):
            $member_payment->rest_of_bill = $request->rest_of_bill;
        else:
            $member_payment->rest_of_bill = $request->total_bill - $request->total_pay;
        endif;

        if (($request->total_bill - $request->total_pay) == '0'):
            $member_payment->payment_status = 'Paid';
        endif;
        $member_payment->save();


        
        //Member Debt Payment
        $member_debt_payment = new MemberDebtPayment;
        $member_debt_payment->member_payment_id = $member_payment->id;
        $member_debt_payment->payment_number = sprintf('%08d', $payment_number).$request->payment_number;
        $member_debt_payment->amount_due = $request->total_bill;
        $member_debt_payment->amount_paid = $request->total_pay;

        if (($request->total_bill - $request->total_pay) == $request->rest_of_bill):
            $member_debt_payment->rest_of_bill = $request->rest_of_bill;
        else:
            $member_debt_payment->rest_of_bill = $request->total_bill - $request->total_pay;
        endif;

        if (!empty($request->payment_date)):
            $member_debt_payment->payment_date = $request->payment_date;
        else:
            $member_debt_payment->payment_date = Carbon::now();
        endif;
        $member_debt_payment->payment_method_id = $request->payment_method;

        //Added 20032019
        /* if ($request->payment_method == 1):
            $member_debt_payment->bank_id = 1;
        else:
            $member_debt_payment->bank_id = $request->bank;
        endif; */

        if ($request->payment_method == 1 || $request->payment_method == 6 || $request->payment_method == 7 ){
            $member_debt_payment->bank_id = 1;
        }else {
            $member_debt_payment->bank_id = $request->bank;
        }

        $member_debt_payment->save();

        // Jonviter DEV
        $member_payment->payment_date = $member_payment->payment_date->toDateTimeString(); 
        $paymentData = $member_payment->toArray();
        $apiClient = new ApiClientService();
        $mobile_curves_api = $apiClient->post('/mobile-curves/payment/' . Auth::id(), $paymentData);
        // Jonviter DEV


        Session::flash('cf_lite.includes.flash-message', trans('messages.member_debt_payment_paid'));

        return redirect('cf-lite/payment/invoice/'.$member_payment->id.'/member');
    }

    //Installment Setting
    public function installment_member_list() {
        /*$members = MemberPayment::where('payment_status', '=', 'Unpaid')
            ->select('user_id')
            ->where('payment_category', '=', 'Monthly')
            ->groupBy('user_id')
            ->orderBy('')
            ->get();*/

        /*$members = DB::table('member_payments')
            ->where('payment_status', '=', 'Unpaid')
            ->join('users', 'member_payments.user_id', '=', 'users.id')
            ->select('member_payments.user_id', 'users.name')
            ->groupBy('member_payments.user_id', 'users.name')
            ->orderBy('users.name', 'asc')
            ->get();*/

        /*$members = User::whereHas('user_memberpayment', function ($q) {
            $q->where('payment_status', '=', 'Unpaid')
                ->where('payment_category', '=', 'Monthly');
        })->orderBy('name', 'asc')->get();*/

        $members = User::whereHas('user_memberstatus', function ($q) {
            $q->where('status', '=', 'Active')
                ->where('payment_status', '=', 'NotSet');
        })
            ->where('club_id', '=', Auth::user()->club_id)
            ->orderBy('name', 'asc')->get();

        return view('cf_lite.payments.installment-member-list', compact('members'));
    }

    public function installment_member_detail($id) {
        $member = User::findOrFail($id);

        $member_payments = MemberPayment::where('user_id', '=', $id)
            ->where('payment_category', '=', 'Monthly')
            ->whereHas('memberpayment_user', function ($q) {
                $q->whereHas('user_memberstatus', function ($r) {
                    $r->where('from_date', '<=', Carbon::now())
                        ->where('thru_date', '>', Carbon::now());
                });
            })->get();

        return view('cf_lite.payments.installment-member-detail', compact('member', 'member_payments'));
    }

    public function new_installment_setting($id) {
        $member = User::findOrFail($id);

        if (!empty($member->user_memberstatus)):
            if ($member->user_memberstatus->payment_status == 'NotSet'):
                $member_payment = MemberPayment::where('user_id', '=', $id)
                    ->whereDate('payment_date', '>=', $member->user_memberstatus->from_date)
                    ->where('payment_category', '=', 'First')
                    ->orderBy('created_at', 'desc')
                    ->first();

                //Added 16042018
                if (empty($member_payment)):
                    $member_payment = MemberPayment::where('user_id', '=', $id)
                        ->where('payment_category', '=', 'First')
                        ->orderBy('created_at', 'desc')
                        ->first();
                        //dd(MemberPayment::where('user_id', '=', $id)->where('payment_category', '=', 'First')->orderBy('created_at', 'desc')->toSql());
                endif;

                $club_sf = ClubServiceFee::query();
                $club_sf = $club_sf->where('club_id', '=', $member->club_id);

                if($member_payment && isset($member_payment->payment_date) && $member_payment->payment_date != null){
                    $club_sf = $club_sf->whereDate('from_date', '<=', $member_payment->payment_date);
                }
                $club_sf = $club_sf->orderBy('created_at', 'desc')->first();
                //dd($club_sf);

                /* $club_sf = ClubServiceFee::where('club_id', '=', $member->club_id)
                    ->where('membership_type_id', '=', $member->user_memberstatus->membership_type_id)
                    ->whereDate('from_date', '<=', $member_payment->payment_date)
//                    ->where('thru_date', null)
                    ->orderBy('created_at', 'desc')->first(); */
//                $club_id = $member->club_id;

//            $tax_types = Taxtype::whereHas('taxtype_taxconfig', function($q) use ($club_id) {
//                $q->where('club_id', '=', $club_id)
//                    ->where('thru_date', null);
//            })->pluck('tax_type', 'id');

                //If AD, Check Monthly Member Payment
                $monthly_member_payments = MemberPayment::where('user_id', '=', $id)
                    ->whereDate('payment_date', '>=', $member->user_memberstatus->from_date)
                    ->where('payment_category', '=', 'Monthly')
                    ->where('payment_status', '<>', 'Unpaid')
                    ->orderBy('created_at', 'desc')
                    ->get();

                // Digital Product Added 07042021
                $digital_product = DigitalProductInventory::where('club_id', '=', Auth::user()->club_id)
                    ->where('status', '=', true)
                    ->whereHas('digitalproductinventory_club', function ($query) {
                        $query->where('digital_product', '=', true);
                    })
                    ->first();
                /* dd(DigitalProductInventory::where('club_id', '=', Auth::user()->club_id)
                ->where('status', '=', true)
                ->whereHas('digitalproductinventory_club', function ($query) {
                    $query->where('digital_product', '=', true);
                })->toSql()); */

                return view('cf_lite.payments.new-installment-setting-v3', compact('member_payment', 'club_sf', 'member', 'monthly_member_payments', 'digital_product'/*, 'tax_types'*/));

            elseif ($member->user_memberstatus->payment_status == 'Set'):
                return redirect('cf-lite/payment/bill-list/'.$member->id);
            endif;
        endif;
    }

    public function store_installment_setting($id, Request $request) {
        $member = User::findOrFail($id);

        //Check Installment Status
        if ($member->user_memberstatus->payment_status == 'NotSet'):
            $member_payment = MemberPayment::where('user_id', '=', $id)
                ->whereDate('payment_date', '>=', $member->user_memberstatus->from_date)
                ->where('payment_category', '=', 'First')
                ->orderBy('created_at', 'desc')
                ->first();

            //Added 23052018
            if (empty($member_payment)):
                $member_payment = MemberPayment::where('user_id', '=', $id)
                    ->where('payment_category', '=', 'First')
                    ->orderBy('created_at', 'desc')
                    ->first();
            endif;

            // Digital Product Added 07042021
            $digital_product = DigitalProductInventory::where('club_id', '=', Auth::user()->club_id)
                ->where('status', '=', true)
                ->first();

            if ($member->user_memberstatus->memberstatus_membershiptype->payment_type == 'Prepaid'):
                if (($request->total - $request->cicilan1) > 0):
                    $cicilan2_req = 'required|numeric';
                    $cicilan2date_req = 'required|date';
                else:
                    $cicilan2_req = 'sometimes';
                    $cicilan2date_req = 'sometimes';
                endif;

                $this->validate($request, [
                    'ins_counter' => 'required|numeric',
                    'cicilan1' => 'required|numeric',
                    'cicilan1_date' => 'required|date',
                    'cicilan2' => $cicilan2_req,
                    'cicilan2_date' => $cicilan2date_req,
                ]);

                for ($i = 3; $i <= $request->ins_counter; $i++):
                    if (!empty($request->input('cicilan'.$i))):
                        $this->validate($request, [
                            'cicilan'.$i => 'required|numeric',
                            'cicilan'.$i.'_date' => 'required|date',
                        ]);
                    else:
                        $this->validate($request, [
                            'cicilan'.$i => 'sometimes',
                            'cicilan'.$i.'_date' => 'sometimes',
                        ]);
                    endif;
                endfor;

                //Update Member Status
                $member_status = $member->user_memberstatus;
                $member_status->payment_status = 'Set';
                $member->user_memberstatus()->save($member_status);

                //Add Member Payment
                if ($request->cicilan2 > 0):
                    $turn = 2;
                else:
                    $turn = 1;
                endif;

                //Added 04092018 Modified
                if ($request->ins_counter > 2):
                    $turn = $request->ins_counter;
                endif;

                for ($i = 1; $i <= $turn; $i++):
                    if (!empty($request->input('cicilan'.$i))):
                        $member_payment_1 = new MemberPayment;
                        $member_payment_1->user_id = $id;
//                $member_payment_1->mf_amount = (100 - $member_payment->tax_percentage) * 0.01 * $request->input('cicilan'.$i);
//                $member_payment_1->mf_amount = round((100 / (100 + $member_payment->tax_percentage)) * $request->input('cicilan'.$i));
                        $member_payment_1->mf_amount = $request->input('cicilan'.$i);
                        $member_payment_1->tax_type_id = $member_payment->tax_type_id;
//                $member_payment_1->tax_amount = $member_payment->tax_percentage * 0.01 * $request->input('cicilan'.$i);
                        $member_payment_1->tax_amount = round(($member_payment->tax_percentage / (100 + $member_payment->tax_percentage)) * $request->input('cicilan'.$i));
                        $member_payment_1->tax_percentage = $member_payment->tax_percentage;
                        $member_payment_1->amount_due = $request->input('cicilan'.$i);
                        $member_payment_1->payment_category = 'Monthly';
                        $member_payment_1->payment_date = $request->input('cicilan'.$i.'_date');
                        $member_payment_1->rest_of_bill = $request->input('cicilan'.$i);
                        $member_payment_1->payment_status = 'Unpaid';
                        $member_payment_1->save();
                        app(\App\Services\EzypayMembershipSubscriptionService::class)->syncAddMemberPaymentMobile($member_payment_1->toArray(), Auth::id());
                        if (!empty($request->input('digiprice'.$i)) && !empty($digital_product)):
                            // TODO: INSERT digital_product_payments
                            $digiproduct_payment = new DigitalProductPayment;
                            $digiproduct_payment->digital_product_inventory_id = $digital_product->id;
                            $digiproduct_payment->price = $request->input('digiprice'.$i);
                            $digiproduct_payment->qty = 1;
                            $member_payment_1->memberpayment_digitalproductpayment()->save($digiproduct_payment);

                            // TODO: UPDATE digital_product_inventories
                            $digital_product_inventory = DigitalProductInventory::findOrFail($digital_product->id);
                            $digital_product_inventory->qty = $digital_product_inventory->qty - 1;
                            $digital_product_inventory->save();
                        endif;
                    endif;
                endfor;

//            if (!empty($member_payment->rest_of_bill)):
//                $member_debt_payment = new MemberDebtPayment;
//                $member_debt_payment->member_payment_id = $member_payment->id;
//                $member_debt_payment->payment_number = 'PAY/P/'.date('YmdHis', strtotime(Carbon::now())).'/nomorpembayaran';
//                $member_debt_payment->amount_due = $member_payment->rest_of_bill;
//                $member_debt_payment->amount_paid = $member_payment->rest_of_bill;
//                $member_debt_payment->payment_date = $request->cicilan1_date;
//                $member_debt_payment->save();
//
//                $member_payment->amount_paid = $member_payment->amount_due;
//                $member_payment->rest_of_bill = '0';
//                $member_payment->payment_status = 'Paid';
//                $member_payment->save();
//            endif;

                $member_payment->payment_status = 'Paid';
                $member_payment->save();
            elseif ($member->user_memberstatus->memberstatus_membershiptype->payment_type == 'AD'):
                foreach ($request->ad as $key => $value):
                    $this->validate($request, [
                        'ad.'.$key => 'required|numeric',
                        'ad_date.'.$key => 'required|date'
                    ]);
                endforeach;

                //Update Member Status
                $member_status = $member->user_memberstatus;
                $member_status->payment_status = 'Set';
                $member->user_memberstatus()->save($member_status);

                foreach ($request->ad as $key => $value):
                    $member_payment_1 = new MemberPayment;
                    $member_payment_1->user_id = $id;
//                $member_payment_1->mf_amount =  $request->ad[$key] / ((100 + $member_payment->tax_percentage) * 0.01);
//                $member_payment_1->mf_amount = round((100 / (100 + $member_payment->tax_percentage)) * $request->ad[$key]);
                    $member_payment_1->mf_amount = $request->ad[$key];
                    $member_payment_1->tax_type_id = $member_payment->tax_type_id;
//                $member_payment_1->tax_amount = $member_payment->tax_percentage * 0.01 * $member_payment_1->mf_amount;
                    $member_payment_1->tax_amount = round(($member_payment->tax_percentage / (100 + $member_payment->tax_percentage)) * $request->ad[$key]);
                    $member_payment_1->tax_percentage = $member_payment->tax_percentage;
                    $member_payment_1->tax_type_id = '1';
                    $member_payment_1->tax_amount = round($request->ad[$key] / 11);
                    $member_payment_1->tax_percentage = '10';
                    $member_payment_1->amount_due = $request->ad[$key];
                    $member_payment_1->payment_category = 'Monthly';
                    $member_payment_1->payment_date = $request->ad_date[$key];
                    $member_payment_1->rest_of_bill = $request->ad[$key];
                    $member_payment_1->payment_status = 'Unpaid';
                    $member_payment_1->save();
                    app(\App\Services\EzypayMembershipSubscriptionService::class)->syncAddMemberPaymentMobile($member_payment_1->toArray(), Auth::id());
                endforeach;
            endif;

            Session::flash('cf_lite.includes.flash-message', trans('messages.installment_has_been_set'));
        endif;

        return redirect('cf-lite/payment/bill-list/'.$id);
    }

    public function bill_list($id) {
        $member = User::findOrFail($id);

        $bills = MemberPayment::where('user_id', '=', $member->id)
            ->where('payment_status', '<>', 'Paid')
            ->orderBy('payment_date', 'asc')
            ->get();
        // Jonviter DEV
        // $user_by_id = DB::table('usermobilecurves_v')->where('id', $member->id)->first();
        // if ($user_by_id):
        //     $apiClientOne = new ApiClientService();
        //     $fetch_usercurves = $apiClientOne->post("/mobile-curves/users/{$member->id}", (array) $user_by_id);
        // endif;
        // Jonviter DEV

        $payment_methods = PaymentMethod::availableForPayment()->pluck('payment_method', 'id');

        $banks = Bank::where('id', '<>', 1)
            ->whereHas('bank_club', function ($q){
                $q->where('club_id', '=', Auth::user()->club_id);
            })->get();

        $staffs = User::where('club_id', '=', Auth::user()->id)
            ->where('role_id', '<>', '7')
            ->where(function ($q) {
                $q->whereDoesntHave('user_staffinactive')
                    ->orWhere(function ($r) {
                        $r->whereHas('user_staffinactive', function ($s) {
                            $s->where('status', '=', 'Reactivated')
                                ->where('thru_date', null);
                        });
                    });
            })
            ->orderBy('name', 'asc')
            ->pluck('name', 'id');

        // Jika membership berjalan adalah MM
        $next_membership = null;
        $is_mm = MembershipType::findOrFail($member->user_memberstatus->membership_type_id);
        if (($is_mm->duration == 1) && ($is_mm->payment_type == 'Cash') && ($is_mm->is_vip == 'general')):
            $logs = LogMemberStatus::where('user_id', '=', $member->id)
                ->whereDate('from_date', '>=', Carbon::now()->toDateString())
                ->first();

            if (!empty($logs)):
                $next_membership = MembershipType::findOrFail($logs->membership_type_id);
            endif;
        endif;

        // Jonviter DEV
        // if (count($bills) > 0):
        //     $apiClient = new ApiClientService();
        //     $mobile_curves_api = $apiClient->post("/mobile-curves/payment-member/{$member->id}", $bills->toArray());
        // endif;
        // Jonviter DEV
      
        //ACCURATE
        /* $accurate = AccurateAuth::find(1);

        $redirectURI = env('ACCURATE_REDIRECT_URI_MEMBERSHIP');
        //$databaseID = Auth::user() ? Accurate::accurateClub(Auth::user()->club_id) : env('ACCURATE_DATABASE_ID');
        $accurateClub = Auth::user() ? Accurate::accurateClub(Auth::user()->club_id) : '';

        $openDB = '';
        $branchID = '';
        $databaseID = '';

        if($accurateClub){
            $databaseID = $accurateClub->accurate_id;
            $openDB = Accurate::AccurateOpenDB($databaseID, $accurate->access_token );
            $branchID = $accurateClub->accurate_branch;
        }

        $accurateItems = [];

        if($accurateClub){
            if($openDB){
                $x = 1;

                while($x <= 100) {
                    $_accurateItems = Accurate::accurateCheckItems($openDB, 'SERVICE', $x, 100);

                    if($_accurateItems){
                        //do nothing
                        $accurateItems = array_replace_recursive($accurateItems, $_accurateItems);
                    }else{
                        break;
                    }
                    $x++;
                }
            }
        }
        return view('cf_lite.payments.bill-list', compact('member', 'bills', 'payment_methods', 'banks', 'staffs', 'next_membership', 'accurateItems')); */

        return view('cf_lite.payments.bill-list', compact('member', 'bills', 'payment_methods', 'banks', 'staffs', 'next_membership'));
    }

    public function bill_detail($id) {
        $bill = MemberPayment::findOrFail($id);

        $fwo = Workout::where('user_id', '=', $bill->user_id)
            ->whereDate('workout_date', '>=', Carbon::parse($bill->memberpayment_user->user_memberstatus->from_date)->toDateString())
            ->first();

        $club = ClubInfo::findOrFail(Auth::user()->club_id);
 

        return view('cf_lite.payments.bill-detail', compact('bill', 'fwo', 'club'));
    }

    //Added 25062018
    public function update_bill_detail($id, Request $request) {
        $this->validate($request, [
            'payment_date_temp' => 'required|date',
            'bill' => 'required',
        ]);

        $bill = MemberPayment::findOrFail($id);

        if ($bill->payment_status != 'Paid'):
            $bill->mf_amount = $request->bill;
            $bill->tax_amount = round($request->bill / 11);
            $bill->amount_due = $request->bill;
            $bill->payment_date = $request->payment_date_temp;
            $bill->rest_of_bill = $request->bill;
            $bill->save();
        endif;

        $tempDigInventory = DigitalProductInventory::where('club_id', '=', Auth::user()->club_id)
            ->where('status', '=', true)
            ->first();

        if ($request->digital_product > 0) {
            if (!empty($bill->memberpayment_digitalproductpayment)) {
                $tempDigPayment = $bill->memberpayment_digitalproductpayment;
                $tempDigPayment->price = $request->digital_product;
                $bill->memberpayment_digitalproductpayment()->save($tempDigPayment);
            } else {
                $tempDigPayment = new DigitalProductPayment();
                $tempDigPayment->member_payment_id = $bill->id;
                $tempDigPayment->digital_product_inventory_id = $tempDigInventory->id;
                $tempDigPayment->price = $request->digital_product;
                $tempDigPayment->qty = 1;
                $bill->memberpayment_digitalproductpayment()->save($tempDigPayment);
            }
        } else {
            if (!empty($bill->memberpayment_digitalproductpayment)) {
                $tempDigPayment = $bill->memberpayment_digitalproductpayment;
                $tempDigPayment->delete();

                $tempDigInventory->qty = $tempDigInventory->qty + 1;
                $tempDigInventory->save();
            }
        }

        Session::flash('flash_message', trans('messages.installment_has_been_updated'));
        return redirect('cf-lite/payment/invoice/'.$bill->id.'/detail');
    }

    public function paid_list($id) {
        $member = User::findOrFail($id);
        $type = 'paid';

        $bills = MemberPayment::where('user_id', '=', $member->id)
            ->where('payment_status', '=', 'Paid')
            ->orderBy('payment_date', 'desc')
            ->get();

        // Jonviter DEV
        // $user_by_id = DB::table('usermobilecurves_v')->where('id', $member->id)->first();
        // if ($user_by_id):
        //     $apiClientOne = new ApiClientService();
        //     $fetch_usercurves = $apiClientOne->post("/mobile-curves/users/{$member->id}", (array) $user_by_id);
        // endif;
        // Jonviter DEV
        $payment_methods = PaymentMethod::availableForPayment()->pluck('payment_method', 'id');

        $banks = Bank::where('id', '<>', 1)
            ->whereHas('bank_club', function ($q){
                $q->where('club_id', '=', Auth::user()->club_id);
            })->get();

        $staffs = User::where('club_id', '=', Auth::user()->id)
            ->where('role_id', '<>', '7')
            ->where(function ($q) {
                $q->whereDoesntHave('user_staffinactive')
                    ->orWhere(function ($r) {
                        $r->whereHas('user_staffinactive', function ($s) {
                            $s->where('status', '=', 'Reactivated')
                                ->where('thru_date', null);
                        });
                    });
            })
            ->orderBy('name', 'asc')
            ->pluck('name', 'id');

        // Jonviter DEV
        // if (count($bills) > 0):
        //     $apiClient = new ApiClientService();
        //     $mobile_curves_api = $apiClient->post("/mobile-curves/payment-member/{$member->id}", $bills->toArray());
        // endif;
        // Jonviter DEV
            //ACCURATE
        /* $accurate = AccurateAuth::find(1);

        $redirectURI = env('ACCURATE_REDIRECT_URI_MEMBERSHIP');
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
        }

        $accurateItems = [];

        if($accurateClub){
            if($openDB){
                $x = 1;

                while($x <= 100) {
                    $_accurateItems = Accurate::accurateCheckItems($openDB, 'SERVICE', $x, 100);

                    if($_accurateItems){
                        //do nothing
                        $accurateItems = array_replace_recursive($accurateItems, $_accurateItems);
                    }else{
                        break;
                    }
                    $x++;
                }
            }
        }
        return view('cf_lite.payments.bill-list', compact('member', 'bills', 'type', 'payment_methods', 'banks', 'staffs', 'accurateItems')); */

        return view('cf_lite.payments.bill-list', compact('member', 'bills', 'type', 'payment_methods', 'banks', 'staffs'));
    }

    private function validate_payment_status($member_payment, $request){
        if (($member_payment->payment_status != 'Unpaid')) return 0;
        if ($member_payment->memberpayment_user->user_memberstatus->memberstatus_membershiptype->payment_type == 'Prepaid') return 1;
        if ($member_payment->memberpayment_user->user_memberstatus->memberstatus_membershiptype->payment_type == 'AD') :
            if (strtotime($member_payment->payment_date) <= strtotime(Carbon::now())) :
                //Added 06042018 Payment Date Validation
                if (!empty($request->payment_date)) :
                    $this->validate($request, [
                        'payment_date' => 'required|after:min_payment_date',
                    ]);
                endif;

                return 1;
            endif;
        endif;
    }

    public function accept_installment_payment($id, Request $request) {
        $member_payment = MemberPayment::findOrFail($id);
        $temp_amount = $member_payment->amount_due;

        if ($member_payment->ezypay_checkout_amount !== null) {
            return redirect('cf-lite/payment/bill-list/'.$member_payment->user_id)->withErrors([
                'payment_method' => 'Tagihan ini sudah diajukan ke EZYPAY. Gunakan tautan yang tersedia atau periksa status pengajuannya.',
            ]);
        }

        $accept_payment = 0;
        if ($request->amount <= $temp_amount) {
            $accept_payment = $this->validate_payment_status($member_payment, $request);
        }

        if ($accept_payment === 1) {
            $paymentMethod = PaymentMethod::find($request->input('payment_method'));
            if ($paymentMethod && strcasecmp(trim($paymentMethod->payment_method), 'EZYPAY') === 0) {
                if (!ApiClientService::isEnabled()) {
                    return redirect('cf-lite/payment/bill-list/'.$member_payment->user_id)->withErrors([
                        'payment_method' => 'Integrasi mobile-curves sedang dinonaktifkan. Pilih metode pembayaran lain.',
                    ]);
                }
                $member = $member_payment->memberpayment_user;
                abort_unless((int) $member->club_id === (int) Auth::user()->club_id, 403);

                return $this->createBillEzypayCheckout($member_payment, $member, $paymentMethod, $request);
            }
        }

        // Cek amount
        if ($request->amount == $member_payment->amount_due): // Amount paid sama dengan amount due
            if ($accept_payment == 1):
                //Update Auto Number
                $temp_auto_number = AutoNumber::findOrFail('1');
                $payment_number = $temp_auto_number->current_number;

                $temp_auto_number->current_number = $temp_auto_number->current_number + 1;
                $temp_auto_number->save();

                //Member Payment
                $month_roman = array(1=>'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');
                $member_payment->payment_number = sprintf('%08d', $payment_number).'/PY/CIND/'.sprintf('%03d', Auth::user()->club_id).'/'.$month_roman[\Carbon\Carbon::now()->format('n')].'/'.Carbon::now()->format('Y');
                //Added 26012018, Updated 20032018
                if (empty($member_payment->payment_date)):
                    $member_payment->payment_date = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::now()->format('Y-m-d H:i:s'), session('timezone'));
                endif;

                //Added 06042018, Installment Payment Date
                if (!empty($request->payment_date)):
                    $member_payment->payment_date = Carbon::parse($request->payment_date);
                endif;

                //Added 26012018
                $member_payment->amount_paid = $member_payment->amount_due;
                $member_payment->rest_of_bill = '0';
                $member_payment->payment_status = 'Paid';
                $member_payment->payment_method_id = $request->payment_method;

                if ($request->payment_method == 1 || $request->payment_method == 6 || $request->payment_method == 7 ){
                    $member_payment->bank_id = 1;
                }else {
                    $member_payment->bank_id = $request->bank;
                }

                $member_payment->save();

                 // Jonviter DEV
                // $member_payment->payment_date = $member_payment->payment_date->toDateTimeString(); 
                // $paymentData = $member_payment->toArray();
                // $apiClient = new ApiClientService();
                // $mobile_curves_api = $apiClient->post('/mobile-curves/payment/' . Auth::id(), $paymentData);
                // $member_payment->payment_date = $member_payment->payment_date->toDateTimeString(); 
                // $paymentData = $member_payment->toArray();
                // $apiClient = new ApiClientService();
                // $mobile_curves_api = $apiClient->post("/mobile-curves/payment-bill", $paymentData);
                // Jonviter DEV

                //ACCURATE
                /* $user = User::find($member_payment->user_id);
                $userData = [
                    'name' => str_replace("%20", " ", $user->name),
                    'accurate_code' => $request->accurate_code,
                    'fa_date' => $request->payment_date,
                    'pay' => $request->amount,
                ];

                $this->store_accurate_installment($user->club_id, $userData); */

                // Amortisasi Added 31012020
                app('App\Http\Controllers\AccountingAmortizationController')->update_amortization($member_payment);

                //Akunting
                // Added 31012020
                app('App\Http\Controllers\TempJournalController')->membership_monthly_payment($member_payment, false);
//                app('App\Http\Controllers\AccountingJournalController')->installment_payment($member_payment);
                $member_payment->payment_date = $member_payment->payment_date->toDateTimeString();
                app(\App\Services\EzypayMembershipSubscriptionService::class)->syncToUpdateMemberPaymentMobile($member_payment->toArray(), Auth::id());


                Session::flash('flash_message', trans('messages.installment_has_been_paid'));



                return redirect('cf-lite/payment/invoice/'.$member_payment->id.'/member');
            endif;

        elseif ($request->amount < $member_payment->amount_due): // Amount Paid lebih kecil dari amount due
            if ($accept_payment == 1):
                //Update Auto Number
                $temp_auto_number = AutoNumber::findOrFail('1');
                $payment_number = $temp_auto_number->current_number;

                $temp_auto_number->current_number = $temp_auto_number->current_number + 1;
                $temp_auto_number->save();

                //Member Payment
                $month_roman = array(1=>'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');
                $member_payment->payment_number = sprintf('%08d', $payment_number).'/PY/CIND/'.sprintf('%03d', Auth::user()->club_id).'/'.$month_roman[\Carbon\Carbon::now()->format('n')].'/'.Carbon::now()->format('Y');
                //Added 26012018, Updated 20032018
                if (empty($member_payment->payment_date)):
                    $member_payment->payment_date = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::now()->format('Y-m-d H:i:s'), session('timezone'));
                endif;

                //Added 06042018, Installment Payment Date
                if (!empty($request->payment_date)):
                    $member_payment->payment_date = Carbon::parse($request->payment_date);
                endif;

                //Added 26012018
                $member_payment->mf_amount = $request->amount; // set value from user input
                $member_payment->tax_amount = round($request->amount / 11); // new
                $member_payment->amount_due = $request->amount; // set value from user input
                $member_payment->amount_paid = $request->amount; // set value from user input
                $member_payment->rest_of_bill = '0';
                $member_payment->payment_status = 'Paid';
                $member_payment->payment_method_id = $request->payment_method;

                /* if ($request->payment_method != 1):
                    $member_payment->bank_id = $request->bank;
                elseif ($request->payment_method == 1):
                    $member_payment->bank_id = 1;
                endif; */

                if ($request->payment_method == 1 || $request->payment_method == 6 || $request->payment_method == 7 ){
                    $member_payment->bank_id = 1;
                }else {
                    $member_payment->bank_id = $request->bank;
                }

                if ($member_payment->save()):
                    // Set new row installment
                    $mp = new MemberPayment;
                    $mp->user_id = $member_payment->user_id;
                    $mp->mf_amount = $temp_amount - $request->amount;
                    $mp->tax_type_id = $member_payment->tax_type_id;
                    $mp->tax_amount = round(($temp_amount - $request->amount) / 11);
                    $mp->tax_percentage = $member_payment->tax_percentage;
                    $mp->amount_due = $temp_amount - $request->amount;
                    $mp->payment_category = $member_payment->payment_category;
                    $mp->payment_date = $member_payment->payment_date;
                    $mp->rest_of_bill = $temp_amount - $request->amount;
                    $mp->payment_status = 'Unpaid';
                    $mp->save();
                    // Jonviter DEV
                    $mp->payment_date = $mp->payment_date->toDateTimeString(); 
                    // $paymentData = $mp->toArray();
                    // $apiClient = new ApiClientService();
                    // $mobile_curves_api = $apiClient->post("/mobile-curves/payment-member/{$member_payment->user_id}", $paymentData);
                    app(\App\Services\EzypayMembershipSubscriptionService::class)->syncAddMemberPaymentMobile($mp->toArray(), Auth::id());

                    // Jonviter DEV
                endif;

                //ACCURATE
                /* $user = User::find($member_payment->user_id);
                $userData = [
                    'name' => str_replace("%20", " ", $user->name),
                    'accurate_code' => $request->accurate_code,
                    'fa_date' => $request->payment_date,
                    'pay' => $request->amount,
                ];

                $this->store_accurate_installment($user->club_id, $userData); */

                // Amortisasi Added 31012020
                app('App\Http\Controllers\AccountingAmortizationController')->update_amortization($member_payment);

                //Akunting
                // Added 31012020
                app('App\Http\Controllers\TempJournalController')->membership_monthly_payment($member_payment, false);
//                app('App\Http\Controllers\AccountingJournalController')->installment_payment($member_payment);

                Session::flash('flash_message', trans('messages.installment_has_been_paid'));

                return redirect('cf-lite/payment/invoice/'.$member_payment->id.'/member');
            endif;

        else: // Amount paid lebih besar dari mount due
            Session::flash('flash_message', trans('messages.amount_paid_higher_than_amount_due'));

            return redirect('cf-lite/payment/bill-list/'.$member_payment->user_id);
        endif;

////        return redirect('cf-lite/payment/bill-list/'.$member_payment->user_id);
//        return redirect('cf-lite/payment/invoice/'.$member_payment->id.'/member');
    }

    private function createBillEzypayCheckout(MemberPayment $payment, User $member, PaymentMethod $method, Request $request)
    {
        $billList = redirect('cf-lite/payment/bill-list/'.$member->id);
        abort_unless($payment->payment_category === 'Monthly', 422);

        $data = $request->validate([
            'amount' => 'required|integer|min:1',
            'paymentMethodToken' => 'required|string|max:255',
        ]);
        if (!$member->customer_id_ezypay) {
            return $billList->withErrors(['payment_method' => 'Siapkan customer EZYPAY terlebih dahulu.']);
        }

        try {
            $api = app(ApiClientService::class);
            $options = $this->loadEzypayPaymentOptions($member->customer_id_ezypay, $api);
            if (!in_array($data['paymentMethodToken'], array_column($options, 'token'), true)) {
                return $billList->withErrors(['payment_method' => 'Pilih akun pembayaran EZYPAY yang aktif untuk anggota ini.']);
            }
        } catch (\Throwable $e) {
            return $billList->withErrors(['payment_method' => 'Gagal memeriksa akun pembayaran EZYPAY. Silakan coba lagi.']);
        }

        $reference = $payment->payment_number ?: 'EZYPAY-INSTALLMENT-'.$payment->id;
        $amount = (int) $data['amount'];
        if ((float) $payment->amount_due !== (float) $amount) {
            return $billList->withErrors(['payment_method' => 'Checkout EZYPAY harus mencakup seluruh nominal tagihan cicilan ini.']);
        }

        $reserved = MemberPayment::whereKey($payment->id)
            ->where('payment_status', 'Unpaid')
            ->where('amount_due', $amount)
            ->whereNull('ezypay_checkout_amount')
            ->whereNull('ezypay_checkout_id')
            ->whereNull('ezypay_invoice_id')
            ->update([
                'payment_number' => $reference,
                'payment_method_id' => $method->id,
                'ezypay_checkout_amount' => $amount,
            ]);
        if (!$reserved) {
            return $billList->withErrors(['payment_method' => 'Tagihan sudah diproses atau memiliki pengajuan EZYPAY. Periksa status tagihan.']);
        }

        try {
            $response = $api->post('/mobile-curves/ezypay/checkout', [
                'customerId' => $member->customer_id_ezypay,
                'paymentMethodToken' => $data['paymentMethodToken'],
                'amountValue' => $amount,
                'accountingCode' => 'INSTALLEMENT',
                'description' => 'Pembayaran cicilan keanggotaan',
                'externalInvoiceId' => $reference,
            ])->throw();
            $checkoutId = $response->json('response.id');
            $checkoutUrl = $response->json('response.checkoutUrl');
            if (!is_string($checkoutId) || trim($checkoutId) === '' || strlen($checkoutId) > 255
                || !is_string($checkoutUrl) || !filter_var($checkoutUrl, FILTER_VALIDATE_URL)
                || parse_url($checkoutUrl, PHP_URL_SCHEME) !== 'https') {
                throw new \UnexpectedValueException('Invalid EZYPAY checkout response.');
            }

            $payment->ezypay_checkout_id = $checkoutId;
            $payment->ezypay_checkout_url = $checkoutUrl;
            $payment->save();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('EZYPAY installment checkout requires review.', [
                'member_payment_id' => $payment->id,
                'external_reference' => $reference,
                'http_status' => $e instanceof \Illuminate\Http\Client\RequestException ? $e->response->status() : null,
                'exception_type' => get_class($e),
            ]);

            return $billList->withErrors(['payment_method' => 'Hasil pembuatan tautan belum dapat dipastikan. Periksa status checkout di EZYPAY.']);
        }

        return $billList;
    }

    public function prepareInstallmentEzypayCustomer($id)
    {
        $member = User::where('club_id', Auth::user()->club_id)->findOrFail($id);

        try {
            $this->ensureInstallmentEzypayCustomer($member);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to create EZYPAY customer for installment.', [
                'user_id' => $member->id,
                'http_status' => $e instanceof \Illuminate\Http\Client\RequestException ? $e->response->status() : null,
                'exception_type' => get_class($e),
            ]);

            return response()->json([
                'message' => 'Gagal menyiapkan customer EZYPAY. Periksa nama, email, dan nomor telepon anggota sebelum mencoba lagi.',
            ], 502);
        }

        try {
            $customerId = $member->refresh()->customer_id_ezypay;
            $options = $this->loadEzypayPaymentOptions($customerId, app(ApiClientService::class));
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Gagal memuat metode pembayaran EZYPAY. Silakan coba lagi.'], 502);
        }

        return response()->json(['data' => $options])->header('Cache-Control', 'no-store');
    }

    private function ensureInstallmentEzypayCustomer(User $member): void
    {
        if ($member->customer_id_ezypay !== null) {
            return;
        }

        DB::transaction(function () use ($member) {
            $lockedMember = User::lockForUpdate()->findOrFail($member->id);
            if ($lockedMember->customer_id_ezypay !== null) {
                return;
            }

            $personal = $lockedMember->user_memberpersonal;
            $mobilePhone = $personal?->cellphone ?: $personal?->phone;
            $homePhone = $personal?->phone ?: $mobilePhone;
            if (!$mobilePhone || !filter_var($lockedMember->email, FILTER_VALIDATE_EMAIL)) {
                throw new \UnexpectedValueException('Missing EZYPAY customer contact details.');
            }

            $response = app(ApiClientService::class)
                ->post("/mobile-curves/ezypay/customers/{$lockedMember->club_id}", [
                    'name' => $lockedMember->name,
                    'email' => $lockedMember->email,
                    'mobilePhone' => $mobilePhone,
                    'homePhone' => $homePhone,
                ])->throw();

            $customerId = $response->json('response.id');
            if (!is_string($customerId) || trim($customerId) === '' || strlen($customerId) > 255) {
                throw new \UnexpectedValueException('Missing EZYPAY customer ID.');
            }

            $lockedMember->customer_id_ezypay = $customerId;
            $lockedMember->save();
        });
    }

    public function accept_installment_payment_test($id, Request $request) {
        $accept_payment = 0;
        $member_payment = MemberPayment::findOrFail($id);
        $temp_amount = $member_payment->amount_due;

        // Cek amount
        if ($request->amount == $member_payment->amount_due): // Amount paid sama dengan amount due
            $accept_payment = $this->validate_payment_status($member_payment, $request);

            if ($accept_payment == 1):
                //Update Auto Number
                $temp_auto_number = AutoNumber::findOrFail('1');
                $payment_number = $temp_auto_number->current_number;

                $temp_auto_number->current_number = $temp_auto_number->current_number + 1;
                //$temp_auto_number->save();

                //Member Payment
                $month_roman = array(1=>'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');
                $member_payment->payment_number = sprintf('%08d', $payment_number).'/PY/CIND/'.sprintf('%03d', Auth::user()->club_id).'/'.$month_roman[\Carbon\Carbon::now()->format('n')].'/'.Carbon::now()->format('Y');
                //Added 26012018, Updated 20032018
                if (empty($member_payment->payment_date)):
                    $member_payment->payment_date = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::now()->format('Y-m-d H:i:s'), session('timezone'));
                endif;

                //Added 06042018, Installment Payment Date
                if (!empty($request->payment_date)):
                    $member_payment->payment_date = Carbon::parse($request->payment_date);
                endif;

                //Added 26012018
                $member_payment->amount_paid = $member_payment->amount_due;
                $member_payment->rest_of_bill = '0';
                $member_payment->payment_status = 'Paid';
                $member_payment->payment_method_id = $request->payment_method;

                /* if ($request->payment_method != 1):
                    $member_payment->bank_id = $request->bank;
                elseif ($request->payment_method == 1):
                    $member_payment->bank_id = 1;
                endif; */

                if ($request->payment_method == 1 || $request->payment_method == 6 || $request->payment_method == 7 ){
                    $member_payment->bank_id = 1;
                }else {
                    $member_payment->bank_id = $request->bank;
                }

                //$member_payment->save();

                //ACCURATE
                /* $user = User::find($member_payment->user_id);
                $userData = [
                    'name' => str_replace("%20", " ", $user->name),
                    'accurate_code' => $request->accurate_code,
                    'fa_date' => $request->payment_date,
                    'pay' => $request->amount,
                ];
                dd($userData); */
                //$this->store_accurate_installment($user->club_id, $userData);

                // Amortisasi Added 31012020
                //app('App\Http\Controllers\AccountingAmortizationController')->update_amortization($member_payment);

                //Akunting
                // Added 31012020
                //app('App\Http\Controllers\TempJournalController')->membership_monthly_payment($member_payment, false);
                //app('App\Http\Controllers\AccountingJournalController')->installment_payment($member_payment);


                //Session::flash('flash_message', trans('messages.installment_has_been_paid'));

                //return redirect('cf-lite/payment/invoice/'.$member_payment->id.'/member');
            endif;

        elseif ($request->amount < $member_payment->amount_due): // Amount Paid lebih kecil dari amount due
            $accept_payment = $this->validate_payment_status($member_payment, $request);

            if ($accept_payment == 1):
                //Update Auto Number
                $temp_auto_number = AutoNumber::findOrFail('1');
                $payment_number = $temp_auto_number->current_number;

                $temp_auto_number->current_number = $temp_auto_number->current_number + 1;
                //$temp_auto_number->save();

                //Member Payment
                $month_roman = array(1=>'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');
                $member_payment->payment_number = sprintf('%08d', $payment_number).'/PY/CIND/'.sprintf('%03d', Auth::user()->club_id).'/'.$month_roman[\Carbon\Carbon::now()->format('n')].'/'.Carbon::now()->format('Y');
                //Added 26012018, Updated 20032018
                if (empty($member_payment->payment_date)):
                    $member_payment->payment_date = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::now()->format('Y-m-d H:i:s'), session('timezone'));
                endif;

                //Added 06042018, Installment Payment Date
                if (!empty($request->payment_date)):
                    $member_payment->payment_date = Carbon::parse($request->payment_date);
                endif;

                //Added 26012018
                $member_payment->mf_amount = $request->amount; // set value from user input
                $member_payment->tax_amount = round($request->amount / 11); // new
                $member_payment->amount_due = $request->amount; // set value from user input
                $member_payment->amount_paid = $request->amount; // set value from user input
                $member_payment->rest_of_bill = '0';
                $member_payment->payment_status = 'Paid';
                $member_payment->payment_method_id = $request->payment_method;

                /* if ($request->payment_method != 1):
                    $member_payment->bank_id = $request->bank;
                elseif ($request->payment_method == 1):
                    $member_payment->bank_id = 1;
                endif; */

                if ($request->payment_method == 1 || $request->payment_method == 6 || $request->payment_method == 7 ){
                    $member_payment->bank_id = 1;
                }else {
                    $member_payment->bank_id = $request->bank;
                }

                /* if ($member_payment->save()):
                    // Set new row installment
                    $mp = new MemberPayment;
                    $mp->user_id = $member_payment->user_id;
                    $mp->mf_amount = $temp_amount - $request->amount;
                    $mp->tax_type_id = $member_payment->tax_type_id;
                    $mp->tax_amount = round(($temp_amount - $request->amount) / 11);
                    $mp->tax_percentage = $member_payment->tax_percentage;
                    $mp->amount_due = $temp_amount - $request->amount;
                    $mp->payment_category = $member_payment->payment_category;
                    $mp->payment_date = $member_payment->payment_date;
                    $mp->rest_of_bill = $temp_amount - $request->amount;
                    $mp->payment_status = 'Unpaid';
                    $mp->save();
                endif; */

                //ACCURATE
                /* $user = User::find($member_payment->user_id);
                $userData = [
                    'name' => str_replace("%20", " ", $user->name),
                    'accurate_code' => $request->accurate_code,
                    'fa_date' => $request->payment_date,
                    'pay' => $request->amount,
                ];
                dd($userData); */
                //$this->store_accurate_installment($user->club_id, $userData);

                // Amortisasi Added 31012020
                //app('App\Http\Controllers\AccountingAmortizationController')->update_amortization($member_payment);

                //Akunting
                // Added 31012020
                //app('App\Http\Controllers\TempJournalController')->membership_monthly_payment($member_payment, false);
//                app('App\Http\Controllers\AccountingJournalController')->installment_payment($member_payment);

                //Session::flash('flash_message', trans('messages.installment_has_been_paid'));

                //return redirect('cf-lite/payment/invoice/'.$member_payment->id.'/member');
            endif;

        else: // Amount paid lebih besar dari mount due
            //Session::flash('flash_message', trans('messages.amount_paid_higher_than_amount_due'));

            //return redirect('cf-lite/payment/bill-list-test/'.$member_payment->user_id);
        endif;
    }

    //end of TEST

    //Continue Membership Payment
    public function store_continue_payment($id, FirstPaymentRequest $request) {
        if (!empty(session('continue_member'))):
            $user = User::findOrFail(session('continue_member'));

            //Update Auto Number
            $temp_auto_number = AutoNumber::findOrFail('1');
            $payment_number = $temp_auto_number->current_number;

            $temp_auto_number->current_number = $temp_auto_number->current_number + 1;
            $temp_auto_number->save();

            //Adding Member Payment
            $member_payment = new MemberPayment;
            $member_payment->payment_number = sprintf('%08d', $payment_number).$request->payment_number;
            $member_payment->user_id = $user->id;
            $member_payment->sf_amount = $request->sf_amount;
            $member_payment->mf_amount = $request->mf_amount;
            $member_payment->dc_amount = $request->discount;
            $member_payment->tax_type_id = '1';
            $member_payment->tax_amount = round($request->total / 11);
            $member_payment->tax_percentage = 10;
            $member_payment->amount_due = $request->total;
            $member_payment->payment_category = 'First';

            if (strtotime($request->pay_date) == strtotime(Carbon::today())):
                $member_payment->payment_date = Carbon::createFromFormat('Y-m-d H:i', Carbon::now()->format('Y-m-d H:i'), session('timezone'));
            else:
                $member_payment->payment_date = $request->pay_date;
            endif;

            $member_payment->payment_method_id = $request->payment_method;
            $member_payment->amount_paid = $request->total_pay;

            if (($request->total_bill - $request->total_pay) > 0):
                if ($request->rest_of_bill == ($request->total_bill - $request->total_pay)):
                    $member_payment->rest_of_bill = $request->rest_of_bill;
                else:
                    $member_payment->rest_of_bill = ($request->total_bill - $request->total_pay);
                endif;
                $member_payment->payment_status = 'Halfpaid';
            elseif (($request->total_bill - $request->total_pay) == 0):
                $member_payment->payment_status = 'Paid';
            endif;

            //Bank Added 28062019
            /* if ($request->payment_method != '1'):
                $member_payment->bank_id = $request->bank;
            endif; */

            if ($request->payment_method == 1 || $request->payment_method == 6 || $request->payment_method == 7 ){
                $member_payment->bank_id = 1;
            }else {
                $member_payment->bank_id = $request->bank;
            }

            // Renewal Status Added 20022020
            $member_payment->renewal = true;

            $member_payment->save();

            //Adding Member Payment Voucher if using Voucher
            if (!empty($request->voucher_code) && !empty($request->voucher_value)):
                $check_voucher = Voucher::where('identity_number', '=', $request->voucher_code)
                    ->whereDate('from_date', '<=', Carbon::now()->toDateString())
                    ->whereDate('thru_date', '>=', Carbon::now()->toDateString())
                    ->where('status', '=', 'Out')
                    ->first();

                if (count($check_voucher) > 0):
                    $member_payment_voucher = new MemberPaymentVoucher;
                    $member_payment_voucher->voucher_code = $request->voucher_code;

                    if ($check_voucher->type_id != '2'):
                        if ($check_voucher->voucher_base == 'value'):
                            if (($request->sf_amount + $request->mf_amount - $request->discount) < $check_voucher->value):
                                $member_payment_voucher->voucher_value = $request->sf_amount + $request->mf_amount - $request->discount;
                            else:
                                $member_payment_voucher->voucher_value = $check_voucher->value;
                            endif;
                        elseif ($check_voucher->voucher_base == 'percent'):
                            $actual_voucher = ($request->sf_amount + $request->mf_amount - $request->discount) * $check_voucher->percent * 0.01;
                            if ($actual_voucher > $check_voucher->value):
                                $actual_voucher = $check_voucher->value;
                            endif;

                            if (($request->sf_amount + $request->mf_amount - $request->discount) < $actual_voucher):
                                $member_payment_voucher->voucher_value = $request->sf_amount + $request->mf_amount - $request->discount;
                            else:
                                $member_payment_voucher->voucher_value = $actual_voucher;
                            endif;
                        endif;
                    endif;
                    $member_payment->memberpayment_memberpaymentvoucher()->save($member_payment_voucher);

                    $voucher = Voucher::findOrFail($check_voucher->id);
                    $voucher->status = 'Used';
                    $voucher->save();
                endif;
            endif;

            //Check if this member has Pending Continued Membership
            $check_continue = ContinueMembership::where('user_id', '=', $user->id)
                ->whereDate('from_date', '>=', Carbon::parse($user->user_memberstatus->thru_date))
                ->get();

            $start_from_date = Carbon::parse($user->user_memberstatus->thru_date)->addDay();

            if (count($check_continue) > 0):
                foreach ($check_continue as $the_date):
                    if (strtotime($the_date->thru_date) > strtotime($start_from_date)):
                        $start_from_date = Carbon::parse($the_date->thru_date)->addDay();
                    endif;
                endforeach;
            endif;

            //If Reactivation added 05042018
            if (!empty(session('reactivation'))):
                if (session('reactivation') == $user->id):
                    $start_from_date = Carbon::parse($request->pay_date);
                endif;
            endif;

            //Adding ContinueMembership (continue_memberships table)
            $membership_type = MembershipType::findOrFail($id);

            $continue = new ContinueMembership;
            $continue->user_id = $user->id;
            $continue->membership_type_id = $membership_type->id;

            if ($membership_type->is_vip == 'general'):
                $the_duration = $membership_type->duration;
            elseif ($membership_type->is_vip == 'vip'):
                $the_duration = 0;
            endif;

            //Cek Jika Perpanjangan dilakukan paling lambat 2 minggu sebelum keanggotaan berakhir Added 16022019
            if (($membership_type->duration == '12') || ($membership_type->duration == '18') || ($membership_type->duration == '24')):
                $payment_diff_days = Carbon::parse($request->pay_date)->diffInDays(Carbon::parse($user->user_memberstatus->thru_date));

                if ($payment_diff_days >= 14):
                    if ($membership_type->is_vip == 'general'):
                        $the_duration += 1;
                    endif;
                endif;
            endif;
            //End of Added 16022019

            $continue->from_date = Carbon::createFromFormat('Y-m-d H:i', Carbon::parse($start_from_date)->format('Y-m-d H:i'), session('timezone'));
            $thru_date_temp = date('Y-m-d', strtotime("+".$the_duration." months", strtotime($continue->from_date)));

            //VIP Member added 02032018
            if (!empty($request->use_vip) && !empty($request->vip_duration)):
                if ($request->vip_option == 'day'):
                    $vip_option = 'days';
                elseif ($request->vip_option == 'month'):
                    $vip_option = 'months';
                elseif ($request->vip_option == 'year'):
                    $vip_option = 'years';
                endif;

                $continue->thru_date = date('Y-m-d', strtotime("+".$request->vip_duration." ".$vip_option, strtotime($thru_date_temp)));
                $continue->member_vip_status = 'VIP';
            else:
                $continue->thru_date = $thru_date_temp;
            endif;

            $continue->status = 'Pending';
            $continue->staff_id = $request->fa_coach;

            if (($membership_type->payment_type == 'Prepaid') || ($membership_type->payment_type == 'Cash')):
                if ($request->rest_of_bill == 0):
                    $continue->payment_status = 'Set';
                else:
                    $continue->payment_status = 'NotSet';
                endif;
            endif;

            $user->user_continuemembership()->save($continue);

            //Adding LogMemberStatus (Log for Member's Membership History)
            $log_member = new LogMemberStatus;
            $log_member->user_id = $user->id;
            $log_member->membership_type_id = $membership_type->id;
            $log_member->from_date = $continue->from_date;
            $log_member->thru_date = $continue->thru_date;
            $log_member->save();

            //Jika current membership = MM, unset payment_status
            /*
            $member_status = $user->user_memberstatus;
            $is_mm = MembershipType::findOrFail($member_status->membership_type_id);
            if (($is_mm->payment_type == 'Cash') && ($is_mm->is_vip == 'general')):
                $member_status->payment_status = 'NotSet';
                $user->user_memberstatus()->save($member_status);
            endif;
            */

            // Jonviter DEV
            // $member_payment->payment_date = $member_payment->payment_date->toDateTimeString(); 
            // $paymentData = $member_payment->toArray();
            // $apiClient = new ApiClientService();
            // $mobile_curves_api = $apiClient->post("/mobile-curves/payment/{$request->fa_coach}", $paymentData);
            // Jonviter DEV

            // Amortization
            app('App\Http\Controllers\AccountingAmortizationController')->create_amortization($member_payment, $membership_type, Carbon::parse($user->user_memberstatus->thru_date)->addDay(1));

            // Accounting
            app('App\Http\Controllers\TempJournalController')->membership_payment($member_payment, false);
            app('App\Http\Controllers\AccountingJournalController')->membership_journal($member_payment, $membership_type);

            return redirect('cf-lite/payment/invoice/'.$member_payment->id.'/member');
        endif;
    }

    //DEBT
    public function debt_list(){
        $paymentDebts = MemberPayment::where('payment_status','<>','Paid')
            ->whereHas('memberpayment_user', function ($q) {
                $q->where('club_id', '=', Auth::user()->club_id);
            })
            ->get();

        foreach($paymentDebts as $paymentDebt){
            if($paymentDebt->payment_category == 'First'){
                $paymentDebt->setAttribute('isDebt','1');
            }else if($paymentDebt->payment_category == 'Monthly'){
                if(Carbon::parse($paymentDebt->payment_date) <= Carbon::now()){
                    $paymentDebt->setAttribute('isDebt','1');
                }else{
                    $paymentDebt->setAttribute('isDebt','0');
                }
            }
        }

        return view('cf_lite.payments.debt-list',compact('paymentDebts'));
    }

    public function debt_list_filter(Request $request){

        $name = $request->name;
        $paymentDebts = MemberPayment::where('payment_status','<>','Paid')
            ->whereHas('memberpayment_user', function ($q) {
                $q->where('club_id', '=', Auth::user()->club_id);
            });
        if(!empty($request->from_date) && !empty($request->thru_date)){
            $paymentDebts = $paymentDebts->whereDate('payment_date','>=',$request->from_date)->whereDate('payment_date','<=',$request->thru_date)->whereHas('memberpayment_user',function($q) use ($name){
                $q->where('name','ilike','%'.$name.'%');
            })->get();
        }else if(!empty($request->from_date) && empty($request->thru_date)){
            $paymentDebts = $paymentDebts->whereDate('payment_date','>=',$request->from_date)->whereHas('memberpayment_user',function($q) use ($name){
                $q->where('name','ilike','%'.$name.'%');
            })->get();
        }else if(empty($request->from_date) && !empty($request->thru_date)){
            $paymentDebts = $paymentDebts->whereDate('payment_date','<=',$request->thru_date)->whereHas('memberpayment_user',function($q) use ($name){
                $q->where('name','ilike','%'.$name.'%');
            })->get();
        }else{
            $paymentDebts = $paymentDebts->whereHas('memberpayment_user',function($q) use ($name){
                $q->where('name','ilike','%'.$name.'%');
            })->get();
        }

        foreach($paymentDebts as $paymentDebt){
            $paymentDebt->setAttribute('isDebt','1');
        }

        return view('cf_lite.payments.debt-list',compact('paymentDebts'));
    }

    public function debt_list_v2(Request $request) {
        $debts = MemberPayment::where('club_id', '=', Auth::user()->club_id)
            ->where('member_payments.payment_status', '<>', 'Paid');

        if (!empty($request->from_date) || !empty($request->thru_date)):
            if (!empty($request->from_date)):
                $debts->whereDate('payment_date', '>=', Carbon::parse($request->from_date)->toDateString());
            endif;

            if (!empty($request->thru_date)):
                $debts->whereDate('payment_date', '<=', Carbon::parse($request->thru_date)->toDateString());
            endif;
        else:
            $debts->whereDate('payment_date', '<=', Carbon::now()->toDateString());
        endif;

        $debts->join('users', 'users.id', '=', 'member_payments.user_id')
            ->join('member_status', 'member_status.user_id', '=', 'users.id')
            ->join('membership_types', 'membership_types.id', '=', 'member_status.membership_type_id');

        $debts = $debts->selectRaw('users.id, users.name, membership_types.membership_type_name, member_status.status,
        array_agg(member_payments.payment_date) as paydate, SUM(member_payments.rest_of_bill) as due_debt, member_status.thru_date as expired')
            ->where(function ($q) use ($request) {
                $q->where('member_status.status', '=', 'Active')
                    ->orWhere('member_status.status', '=', 'Hold');

                if ($request->member_status == 'All'):
                    $q->orWhere('member_status.status', '=', 'Inactive')
                        ->orWhere('member_status.status', '=', 'Stopped');
                endif;
            })
            ->groupBy('users.id', 'users.name', 'membership_types.membership_type_name', 'member_status.status', 'expired')
            ->orderBy('users.name', 'asc')
            ->get();

        foreach ($debts as $debt):
            $payment_trials = MemberPayment::where('user_id', '=', $debt->id)
                ->where('payment_status', '<>', 'Paid')
                ->selectRaw('SUM(rest_of_bill) as due_trial')
                ->groupBy('user_id')
                ->get();

            foreach ($payment_trials as $payment_trial):
                $debt->setAttribute('due_trial', $payment_trial->due_trial);
            endforeach;
        endforeach;

//        return $debts;
        return view('cf_lite.payments.debt-list-v2', compact('debts'));
    }


    //Membership Changing
    public function store_changing_payment($id, FirstPaymentRequest $request) {
        if (!empty(session('membership_changing_member'))):
            $user = User::findOrFail(session('membership_changing_member'));
            $membership_type = MembershipType::findOrFail($id);

            //Update Auto Number
            $temp_auto_number = AutoNumber::findOrFail('1');
            $payment_number = $temp_auto_number->current_number;

            $temp_auto_number->current_number = $temp_auto_number->current_number + 1;
            $temp_auto_number->save();


            //Deposit
            $log_payment_date = LogMemberStatus::where('user_id', '=', $user->id)
                ->where('membership_type_id', '=', $user->user_memberstatus->membership_type_id)
                ->whereDate('from_date', '>=', Carbon::parse($user->user_memberstatus->from_date)->toDateString())
                ->whereDate('thru_date', '=', Carbon::parse($user->user_memberstatus->thru_date)->toDateString())
                ->first();

            //Added 09022019
            if (empty($log_payment_date)): //check by thru_date
                $log_payment_date = LogMemberStatus::where('user_id', '=', $user->id)
                    ->where('membership_type_id', '=', $user->user_memberstatus->membership_type_id)
                    ->whereDate('thru_date', '=', Carbon::parse($user->user_memberstatus->thru_date)->toDateString())
                    ->first();
            endif;

            //Added 8Sept2023
            if (empty($log_payment_date)): //check by from_date
                $log_payment_date = LogMemberStatus::where('user_id', '=', $user->id)
                    ->where('membership_type_id', '=', $user->user_memberstatus->membership_type_id)
                    ->whereDate('from_date', '=', Carbon::parse($user->user_memberstatus->from_date)->toDateString())
                    ->first();
            endif;

            $past_member_payments = MemberPayment::where('user_id', '=', $user->id)
                ->whereDate('created_at', '>=', Carbon::parse($log_payment_date->created_at)->toDateString())
                ->whereDoesntHave('memberpayment_continuemembershiprefund')
                ->get();

            foreach ($past_member_payments as $past_member_payment):
                if ((Carbon::parse($past_member_payment->payment_date)->format('mY') != Carbon::today()->format('mY')) && ($past_member_payment->payment_number == null)):
                    $payment_to_delete = MemberPayment::findOrFail($past_member_payment->id);
                    $payment_to_delete->delete();
                else:
                    if ($past_member_payment->payment_status == 'Halfpaid'):
                        $payment_to_paid = MemberPayment::findOrFail($past_member_payment->id);
                        $payment_to_paid->payment_status = 'Paid';
                        $payment_to_paid->save();
                    else:
                        if ($past_member_payment->payment_number == null):
                            $payment_to_delete = MemberPayment::findOrFail($past_member_payment->id);
                            $payment_to_delete->delete();
                        endif;
                    endif;
                endif;
            endforeach;


            //Update Log Payment Date
            $updated_log_payment_date = LogMemberStatus::findOrFail($log_payment_date->id);
            $updated_log_payment_date->thru_date = Carbon::now();
            $updated_log_payment_date->save();

            $total = $request->total;

            $member_payment = new MemberPayment;
            $member_payment->payment_number = sprintf('%08d', $payment_number).$request->payment_number;
            $member_payment->user_id = $user->id;
            $member_payment->sf_amount = $request->sf_amount;
            $member_payment->mf_amount = $request->mf_amount;
            $member_payment->dc_amount = $request->discount + $request->total_deposit;
            $member_payment->tax_type_id = '1';
            $member_payment->tax_amount = round($total / 11);
            $member_payment->tax_percentage = 10;
            $member_payment->deposit_amount = $request->total_deposit;

            $member_payment->amount_due = $total;
            $member_payment->payment_category = 'First';

            if (strtotime($request->pay_date) == strtotime(Carbon::today())):
                $member_payment->payment_date = Carbon::createFromFormat('Y-m-d H:i', Carbon::now()->format('Y-m-d H:i'), session('timezone'));
            else:
                $member_payment->payment_date = $request->pay_date;
            endif;

            $member_payment->payment_method_id = $request->payment_method;

            if ($membership_type->payment_type == 'AD'):
                if ($request->total_pay == 0):
                    $member_payment->amount_paid = $total;
                else:
//                    $member_payment->amount_paid = $total + $request->total_pay;
                //Added 16072018
                    $member_payment->amount_paid = $request->total_pay;
                endif;
                $member_payment->rest_of_bill = $request->rest_of_bill;

                if ($request->rest_of_bill > 0):
                    $member_payment->payment_status = 'Halfpaid';
                else:
                    $member_payment->payment_status = 'Paid';
                endif;
            else:
                $member_payment->amount_paid = $request->total_pay;

                if (($request->total_bill - $request->total_pay) > 0):
                    if ($request->rest_of_bill == ($request->total_bill - $request->total_pay)):
                        $member_payment->rest_of_bill = $request->rest_of_bill;
                    else:
                        $member_payment->rest_of_bill = ($request->total_bill - $request->total_pay);
                    endif;
                    $member_payment->payment_status = 'Halfpaid';
                elseif (($request->total_bill - $request->total_pay) == 0):
                    $member_payment->payment_status = 'Paid';
                endif;
            endif;

            //Bank Added 28062019
            /* if ($request->payment_method != '1'):
                $member_payment->bank_id = $request->bank;
            endif; */

            if ($request->payment_method == 1 || $request->payment_method == 6 || $request->payment_method == 7 ){
                $member_payment->bank_id = 1;
            }else {
                $member_payment->bank_id = $request->bank;
            }

            // Conversion Added 24022020
            $member_payment->conversion = true;

            $member_payment->save();


            //Set Membership Changing Deposit
            $remaining_total_deposit = 0;
            if (!empty($request->total_deposit)):
                //Added 16072018
                if ($membership_type->payment_type == 'AD'):
                    $total = $request->sf_amount + $request->mf_amount - $request->discount - $request->voucher_value;
                //Added 25072018
                else:
                    $current_deposit = MemberDeposit::where('user_id', '=', $user->id)
                        ->where('status', '=', 'Pending')
                        ->orderBy('created_at', 'desc')
                        ->first();

                    if (!empty($current_deposit) && ($current_deposit->saldo > $total)):
                        $total = $request->sf_amount + $request->mf_amount - $request->discount - $request->voucher_value;
                    endif;
                endif;

                $mc_deposit = new MembershipChangingDeposit;

                if (($request->total_deposit - $total) > 0):
                    $mc_deposit->deposit_amount = $total;
                    $remaining_total_deposit = $request->total_deposit - $total;
                else:
                    $mc_deposit->deposit_amount = $request->total_deposit;
                endif;
                $member_payment->memberpayment_membershipchangingdeposit()->save($mc_deposit);
            endif;


            //Adding Member Payment Voucher if using Voucher
            if (!empty($request->voucher_code) && !empty($request->voucher_value)):
                $check_voucher = Voucher::where('identity_number', '=', $request->voucher_code)
                    ->whereDate('from_date', '<=', Carbon::now()->toDateString())
                    ->whereDate('thru_date', '>=', Carbon::now()->toDateString())
                    ->where('status', '=', 'Out')
                    ->first();

                if (count($check_voucher) > 0):
                    $member_payment_voucher = new MemberPaymentVoucher;
                    $member_payment_voucher->voucher_code = $request->voucher_code;

                    if ($check_voucher->type_id != '2'):
                        if ($check_voucher->voucher_base == 'value'):
                            if (($request->sf_amount + $request->mf_amount - $request->discount) < $check_voucher->value):
                                $member_payment_voucher->voucher_value = $request->sf_amount + $request->mf_amount - $request->discount;
                            else:
                                $member_payment_voucher->voucher_value = $check_voucher->value;
                            endif;
                        elseif ($check_voucher->voucher_base == 'percent'):
                            $actual_voucher = ($request->sf_amount + $request->mf_amount - $request->discount) * $check_voucher->percent * 0.01;
                            if ($actual_voucher > $check_voucher->value):
                                $actual_voucher = $check_voucher->value;
                            endif;

                            if (($request->sf_amount + $request->mf_amount - $request->discount) < $actual_voucher):
                                $member_payment_voucher->voucher_value = $request->sf_amount + $request->mf_amount - $request->discount;
                            else:
                                $member_payment_voucher->voucher_value = $actual_voucher;
                            endif;
                        endif;
                    endif;
                    $member_payment->memberpayment_memberpaymentvoucher()->save($member_payment_voucher);

                    $voucher = Voucher::findOrFail($check_voucher->id);
                    $voucher->status = 'Used';
                    $voucher->save();
                endif;
            endif;

            //Change Member Status
            $member_status = $user->user_memberstatus;
            $member_status->membership_type_id = $membership_type->id;
            $member_status->thru_date = Carbon::createFromFormat('Y-m-d H:i', Carbon::parse($member_status->from_date)->addMonths($membership_type->duration)->format('Y-m-d H:i'), session('timezone'));

            if ($membership_type->payment_type == 'Prepaid'):
                if ($member_payment->payment_status == 'Halfpaid'):
                    $member_status->payment_status = 'NotSet';
                elseif ($member_payment->payment_status == 'Paid'):
                    $member_status->payment_status = 'Set';
                endif;
            else:
                $member_status->payment_status = 'NotSet';
            endif;

            $member_status->member_vip_status = 'General';
            $user->user_memberstatus()->save($member_status);


            //Adding LogMemberStatus (Log for Member's Membership History)
            $log_member = new LogMemberStatus;
            $log_member->user_id = $user->id;
            $log_member->membership_type_id = $membership_type->id;
            $log_member->from_date = Carbon::createFromFormat('Y-m-d H:i', Carbon::now()->format('Y-m-d H:i'), session('timezone'));
            $log_member->thru_date = $member_status->thru_date;
            $log_member->save();


            //If Membership Changing to AD and Deposit > 0
            $deposit_payment = 0;
            if (($membership_type->payment_type == 'AD')):
                if ($remaining_total_deposit > 0):
                    $pay_month = 1;
                    while ($remaining_total_deposit > 0) {
                        if ($pay_month < 12):
                            //Update Auto Number
                            $temp_auto_number = AutoNumber::findOrFail('1');
                            $payment_number = $temp_auto_number->current_number;

                            $temp_auto_number->current_number = $temp_auto_number->current_number + 1;
                            $temp_auto_number->save();

                            $monthly_member_payment = new MemberPayment;
                            $monthly_member_payment->payment_number = sprintf('%08d', $payment_number).$request->payment_number;
                            $monthly_member_payment->user_id = $user->id;
                            $monthly_member_payment->sf_amount = 0;
                            $monthly_member_payment->mf_amount = $request->mf_amount;
                            $monthly_member_payment->dc_amount = 0;
                            $monthly_member_payment->tax_type_id = '1';
                            $monthly_member_payment->tax_amount = round($request->mf_amount / 11);
                            $monthly_member_payment->tax_percentage = 10;
                            $monthly_member_payment->amount_due = $request->mf_amount;
                            $monthly_member_payment->payment_category = 'Monthly';
                            $monthly_member_payment->payment_date = Carbon::parse($user->user_memberstatus->from_date)->addMonths($pay_month);

                            if (($remaining_total_deposit - $request->mf_amount) >= 0):
                                $monthly_member_payment->amount_paid = $request->mf_amount;
                                $monthly_member_payment->rest_of_bill = 0;
                                $monthly_member_payment->payment_status = 'Paid';
                                $remaining_total_deposit = $remaining_total_deposit - $request->mf_amount;
                            else:
                                $monthly_member_payment->amount_paid = $remaining_total_deposit;
                                $monthly_member_payment->rest_of_bill = $request->mf_amount - $remaining_total_deposit;
                                $monthly_member_payment->payment_status = 'Halfpaid';
                                $remaining_total_deposit = 0;
                            endif;

                            $monthly_member_payment->save();
                            $deposit_payment = 1;
                            $pay_month++;
                        else:
                            $last_member_deposit = MemberDeposit::where('user_id', '=', $user->id)
                                ->where('status', '=', 'Pending')
                                ->orderBy('created_at', 'desc')
                                ->first();

                            //Adding into Member Deposit
                            if (($remaining_total_deposit > 0) || (!empty($last_member_deposit))):
                                //Save Correction
                                $member_deposit = new MemberDeposit;
                                $member_deposit->user_id = $user->id;

                                if (!empty($last_member_deposit)):
                                    $member_deposit->payment_number = 'cash-'.$last_member_deposit->payment_number;
                                    $member_deposit->kredit = $last_member_deposit->saldo - $remaining_total_deposit;
                                    $member_deposit->saldo = $remaining_total_deposit;
                                else:
                                    $month_roman = array(1=>'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');

                                    //Update Auto Number
                                    $temp_auto_number = AutoNumber::findOrFail(3);
                                    $payment_number = $temp_auto_number->current_number;
                                    $temp_auto_number->current_number = $temp_auto_number->current_number + 1;
                                    $temp_auto_number->save();

                                    $member_deposit->payment_number = 'manual-debet/'.sprintf('%08d', $payment_number).'/RF/CIND/'.sprintf('%03d', $user->club_id).'/'.$month_roman[Carbon::now()->format('n')].'/'.Carbon::now()->format('Y');
                                    $member_deposit->debet = $remaining_total_deposit;
                                    $member_deposit->saldo = $remaining_total_deposit;
                                endif;

                                $member_deposit->status = 'Pending';
                                $member_deposit->payment_type = 'Refund';
                                $member_deposit->save();

                                $information = new MemberDepositInformation;
                                $information->information = 'Deposito - Upgrade Membership';
                                $member_deposit->memberdeposit_information()->save($information);

                                $remaining_total_deposit = 0;
                            endif;

                            if (!empty($last_member_deposit)):
                                $last_member_deposit_edit = MemberDeposit::findOrFail($last_member_deposit->id);
                                $last_member_deposit_edit->status = 'Done';
                                $last_member_deposit_edit->save();
                            endif;

                            $deposit_payment = 0;
                        endif;
                    }
                else:
                    $deposit_payment = 1;
                endif;
            elseif (($membership_type->payment_type == 'Prepaid')):
                if ($remaining_total_deposit > 0):
                    $last_member_deposit = MemberDeposit::where('user_id', '=', $user->id)
                        ->where('status', '=', 'Pending')
                        ->orderBy('created_at', 'desc')
                        ->first();

                    //Save Correction
                    $member_deposit = new MemberDeposit;
                    $member_deposit->user_id = $user->id;

                    if (!empty($last_member_deposit) && ($last_member_deposit->saldo > $remaining_total_deposit)):
                        $member_deposit->payment_number = 'cash-'.$last_member_deposit->payment_number;
                        $member_deposit->kredit = $last_member_deposit->saldo - $remaining_total_deposit;
                        $member_deposit->saldo = $remaining_total_deposit;
                    else:
                        $month_roman = array(1=>'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');

                        //Update Auto Number
                        $temp_auto_number = AutoNumber::findOrFail(3);
                        $payment_number = $temp_auto_number->current_number;
                        $temp_auto_number->current_number = $temp_auto_number->current_number + 1;
                        $temp_auto_number->save();

                        $member_deposit->payment_number = 'manual-debet/'.sprintf('%08d', $payment_number).'/RF/CIND/'.sprintf('%03d', $user->club_id).'/'.$month_roman[Carbon::now()->format('n')].'/'.Carbon::now()->format('Y');
                        if (!empty($last_member_deposit)):
                            $member_deposit->debet = $remaining_total_deposit - $last_member_deposit->saldo;
                        else:
                            $member_deposit->debet = $remaining_total_deposit;
                        endif;
                        $member_deposit->saldo = $remaining_total_deposit;
                    endif;

                    $member_deposit->status = 'Pending';
                    $member_deposit->payment_type = 'Refund';
                    $member_deposit->save();

                    $information = new MemberDepositInformation;
                    $information->information = 'Deposito - Upgrade Membership';
                    $member_deposit->memberdeposit_information()->save($information);

                    if (!empty($last_member_deposit)):
                        $last_member_deposit_edit = MemberDeposit::findOrFail($last_member_deposit->id);
                        $last_member_deposit_edit->status = 'Done';
                        $last_member_deposit_edit->save();
                    endif;

                    $remaining_total_deposit = 0;
                elseif ($remaining_total_deposit == 0):
                    $deposit_payment = 1;
                endif;
            endif;

            if ($deposit_payment == 1):
                $last_member_deposit = MemberDeposit::where('user_id', '=', $user->id)
                    ->where('status', '=', 'Pending')
                    ->orderBy('created_at', 'desc')
                    ->first();

                if (($remaining_total_deposit > 0) || (!empty($last_member_deposit))):
                    //Save Correction
                    $member_deposit = new MemberDeposit;
                    $member_deposit->user_id = $user->id;

                    if (!empty($last_member_deposit)):
                        $member_deposit->payment_number = 'cash-'.$last_member_deposit->payment_number;
                        $member_deposit->kredit = $last_member_deposit->saldo - $remaining_total_deposit;
                        $member_deposit->saldo = $remaining_total_deposit;
                    else:
                        $month_roman = array(1=>'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');

                        //Update Auto Number
                        $temp_auto_number = AutoNumber::findOrFail(3);
                        $payment_number = $temp_auto_number->current_number;
                        $temp_auto_number->current_number = $temp_auto_number->current_number + 1;
                        $temp_auto_number->save();

                        $member_deposit->payment_number = 'manual-debet/'.sprintf('%08d', $payment_number).'/RF/CIND/'.sprintf('%03d', $user->club_id).'/'.$month_roman[Carbon::now()->format('n')].'/'.Carbon::now()->format('Y');
                        $member_deposit->debet = $remaining_total_deposit;
                        $member_deposit->saldo = $remaining_total_deposit;
                    endif;

                    $member_deposit->status = 'Pending';
                    $member_deposit->payment_type = 'Refund';
                    $member_deposit->save();

                    $information = new MemberDepositInformation;
                    $information->information = 'Deposito - Upgrade Membership';
                    $member_deposit->memberdeposit_information()->save($information);
                endif;

                if (!empty($last_member_deposit)):
                    $last_member_deposit_edit = MemberDeposit::findOrFail($last_member_deposit->id);
                    $last_member_deposit_edit->status = 'Done';
                    $last_member_deposit_edit->save();
                endif;
            endif;

            // Jonviter DEV
            // $member_payment->payment_date = $member_payment->payment_date->toDateTimeString(); 
            // $paymentData = $member_payment->toArray();
            // $apiClient = new ApiClientService();
            // $mobile_curves_api = $apiClient->post('/mobile-curves/payment/' . Auth::id(), $paymentData);
            // Jonviter DEV

            // AMORTISASI
            app('App\Http\Controllers\AccountingAmortizationController')->membership_change_amortization($member_payment, $membership_type);

            // AKUNTING
            app('App\Http\Controllers\TempJournalController')->membership_payment($member_payment, false);

            return redirect('cf-lite/payment/invoice/'.$member_payment->id.'/member');
        endif;
    }

    //Membership Changing Downgrade
    public function store_changing_payment_downgrade($id, FirstPaymentRequest $request) {
        if (!empty(session('membership_changing_member'))):
            $user = User::findOrFail(session('membership_changing_member'));
            $membership_type = MembershipType::findOrFail($id);

            //Update Auto Number
            $temp_auto_number = AutoNumber::findOrFail('1');
            $payment_number = $temp_auto_number->current_number;

            $temp_auto_number->current_number = $temp_auto_number->current_number + 1;
            $temp_auto_number->save();


            //Deposit
            $log_payment_date = LogMemberStatus::where('user_id', '=', $user->id)
                ->where('membership_type_id', '=', $user->user_memberstatus->membership_type_id)
                ->whereDate('from_date', '>=', Carbon::parse($user->user_memberstatus->from_date)->toDateString())
                ->whereDate('thru_date', '=', Carbon::parse($user->user_memberstatus->thru_date)->toDateString())
                ->first();

            //Added 09022019
            if (empty($log_payment_date)): //check by thru_date
                $log_payment_date = LogMemberStatus::where('user_id', '=', $user->id)
                    ->where('membership_type_id', '=', $user->user_memberstatus->membership_type_id)
                    ->whereDate('thru_date', '=', Carbon::parse($user->user_memberstatus->thru_date)->toDateString())
                    ->first();
            endif;

            //Added 8Sept2023
            if (empty($log_payment_date)): //check by from_date
                $log_payment_date = LogMemberStatus::where('user_id', '=', $user->id)
                    ->where('membership_type_id', '=', $user->user_memberstatus->membership_type_id)
                    ->whereDate('from_date', '=', Carbon::parse($user->user_memberstatus->from_date)->toDateString())
                    ->first();
            endif;

            $past_member_payments = MemberPayment::where('user_id', '=', $user->id)
                ->whereDate('created_at', '>=', Carbon::parse($log_payment_date->created_at)->toDateString())
                ->whereDoesntHave('memberpayment_continuemembershiprefund')
                ->get();

            foreach ($past_member_payments as $past_member_payment):
                if ((Carbon::parse($past_member_payment->payment_date)->format('mY') != Carbon::today()->format('mY')) && ($past_member_payment->payment_number == null)):
                    $payment_to_delete = MemberPayment::findOrFail($past_member_payment->id);
                    $payment_to_delete->delete();
                else:
                    if ($past_member_payment->payment_status == 'Halfpaid'):
                        $payment_to_paid = MemberPayment::findOrFail($past_member_payment->id);
                        $payment_to_paid->payment_status = 'Paid';
                        $payment_to_paid->save();
                    // Added 25102019
                    elseif ($past_member_payment->payment_status == 'Unpaid'):
                        $payment_to_delete = MemberPayment::findOrFail($past_member_payment->id);
                        $payment_to_delete->delete();
                    endif;
                endif;
            endforeach;


            //Update Log Payment Date
            $updated_log_payment_date = LogMemberStatus::findOrFail($log_payment_date->id);
            $updated_log_payment_date->thru_date = Carbon::parse($log_payment_date->from_date)->addDay();
            $updated_log_payment_date->save();


            //Adding Member Payment
//            if ($membership_type->payment_type == 'AD'):
//                $total = $request->sf_amount + $request->mf_amount - $request->discount - $request->voucher_value;
//            else:
//                $total = $request->total;
//            endif;
            $total = $request->total;

            $member_payment = new MemberPayment;
            $member_payment->payment_number = sprintf('%08d', $payment_number).$request->payment_number;
            $member_payment->user_id = $user->id;
            $member_payment->sf_amount = $request->sf_amount;
            $member_payment->mf_amount = $request->mf_amount;
            $member_payment->dc_amount = $request->discount + $request->total_deposit;
            $member_payment->tax_type_id = '1';
            $member_payment->tax_amount = round($total / 11);
            $member_payment->tax_percentage = 10;
            $member_payment->deposit_amount = $request->total_deposit;

            $member_payment->amount_due = $total;
            $member_payment->payment_category = 'First';
            $member_payment->payment_date = Carbon::createFromFormat('Y-m-d H:i', Carbon::parse($request->pay_date)->format('Y-m-d H:i'), session('timezone'));
            $member_payment->payment_method_id = $request->payment_method;

            if ($membership_type->payment_type == 'AD'):
                if ($request->total_pay == 0):
                    $member_payment->amount_paid = $total;
                else:
//                    $member_payment->amount_paid = $total + $request->total_pay;
                    $member_payment->amount_paid = $request->total_pay;
                endif;
                $member_payment->rest_of_bill = $request->rest_of_bill;

                if ($request->rest_of_bill > 0):
                    $member_payment->payment_status = 'Halfpaid';
                else:
                    $member_payment->payment_status = 'Paid';
                endif;
            else:
                $member_payment->amount_paid = $request->total_pay;

                if (($request->total_bill - $request->total_pay) > 0):
                    if ($request->rest_of_bill == ($request->total_bill - $request->total_pay)):
                        $member_payment->rest_of_bill = $request->rest_of_bill;
                    else:
                        $member_payment->rest_of_bill = ($request->total_bill - $request->total_pay);
                    endif;
                    $member_payment->payment_status = 'Halfpaid';
                elseif (($request->total_bill - $request->total_pay) == 0):
                    $member_payment->payment_status = 'Paid';
                endif;
            endif;

            // Conversion Added 24022020
            $member_payment->conversion = true;

            $member_payment->save();


            //Set Membership Changing Deposit
            $remaining_total_deposit = 0;
            if (!empty($request->total_deposit)):
//                if (($membership_type->payment_type == 'Cash') || ($membership_type->payment_type == 'Prepaid')):
//                    $total = $request->sf_amount + $request->mf_amount - $request->discount - $request->voucher_value;
//                endif;
                $total = $request->sf_amount + $request->mf_amount - $request->discount - $request->voucher_value;

                $mc_deposit = new MembershipChangingDeposit;

                if (($request->total_deposit - $total) > 0):
                    $mc_deposit->deposit_amount = $total;
                    $remaining_total_deposit = $request->total_deposit - $total;
                else:
                    $mc_deposit->deposit_amount = $request->total_deposit;
                endif;
                $member_payment->memberpayment_membershipchangingdeposit()->save($mc_deposit);
            endif;


            //Adding Member Payment Voucher if using Voucher
            if (!empty($request->voucher_code) && !empty($request->voucher_value)):
                $check_voucher = Voucher::where('identity_number', '=', $request->voucher_code)
                    ->whereDate('from_date', '<=', Carbon::now()->toDateString())
                    ->whereDate('thru_date', '>=', Carbon::now()->toDateString())
                    ->where('status', '=', 'Out')
                    ->first();

                if (count($check_voucher) > 0):
                    $member_payment_voucher = new MemberPaymentVoucher;
                    $member_payment_voucher->voucher_code = $request->voucher_code;

                    if ($check_voucher->type_id != '2'):
                        if ($check_voucher->voucher_base == 'value'):
                            if (($request->sf_amount + $request->mf_amount - $request->discount) < $check_voucher->value):
                                $member_payment_voucher->voucher_value = $request->sf_amount + $request->mf_amount - $request->discount;
                            else:
                                $member_payment_voucher->voucher_value = $check_voucher->value;
                            endif;
                        elseif ($check_voucher->voucher_base == 'percent'):
                            $actual_voucher = ($request->sf_amount + $request->mf_amount - $request->discount) * $check_voucher->percent * 0.01;
                            if ($actual_voucher > $check_voucher->value):
                                $actual_voucher = $check_voucher->value;
                            endif;

                            if (($request->sf_amount + $request->mf_amount - $request->discount) < $actual_voucher):
                                $member_payment_voucher->voucher_value = $request->sf_amount + $request->mf_amount - $request->discount;
                            else:
                                $member_payment_voucher->voucher_value = $actual_voucher;
                            endif;
                        endif;
                    endif;
                    $member_payment->memberpayment_memberpaymentvoucher()->save($member_payment_voucher);

                    $voucher = Voucher::findOrFail($check_voucher->id);
                    $voucher->status = 'Used';
                    $voucher->save();
                endif;
            endif;


            //Change Member Status
            $member_status = $user->user_memberstatus;
            $member_status->membership_type_id = $membership_type->id;

            //Count Member's Joining Duration
            $joining_duration = 1;
            if ($membership_type->duration == '1'):
                $joining_days = Carbon::parse($member_status->from_date)->diffInDays(Carbon::parse($request->pay_date));
                $joining_duration = ceil($joining_days / 30);
                $member_status->thru_date = Carbon::createFromFormat('Y-m-d H:i', Carbon::parse($member_status->from_date)->addMonths($joining_duration)->format('Y-m-d H:i'), session('timezone'));
            else:
                $member_status->thru_date = Carbon::createFromFormat('Y-m-d H:i', Carbon::parse($member_status->from_date)->addMonths($membership_type->duration)->format('Y-m-d H:i'), session('timezone'));
            endif;

            if ($membership_type->payment_type == 'Prepaid'):
                if ($member_payment->payment_status == 'Halfpaid'):
                    $member_status->payment_status = 'NotSet';
                elseif ($member_payment->payment_status == 'Paid'):
                    $member_status->payment_status = 'Set';
                endif;
            elseif ($membership_type->payment_type == 'Cash'):
                $member_status->payment_status = 'Set';
            else:
                $member_status->payment_status = 'NotSet';
            endif;

            $member_status->member_vip_status = 'General';
            $user->user_memberstatus()->save($member_status);


            //Adding LogMemberStatus (Log for Member's Membership History)
            $log_member = new LogMemberStatus;
            $log_member->user_id = $user->id;
            $log_member->membership_type_id = $membership_type->id;
            $log_member->from_date = Carbon::createFromFormat('Y-m-d H:i', Carbon::parse($log_payment_date->from_date)->addDay()->format('Y-m-d H:i'), session('timezone'));
            $log_member->thru_date = $user->user_memberstatus->thru_date;
            $log_member->save();


            //If Membership Changing to AD and Deposit > 0
            $deposit_payment = 0;
            if (($membership_type->payment_type == 'AD') && ($remaining_total_deposit > 0)):
                if ($remaining_total_deposit > 0):
                    $pay_month = 1;
                    while ($remaining_total_deposit > 0) {
                        if ($pay_month < 12):
                            //Update Auto Number
                            $temp_auto_number = AutoNumber::findOrFail('1');
                            $payment_number = $temp_auto_number->current_number;

                            $temp_auto_number->current_number = $temp_auto_number->current_number + 1;
                            $temp_auto_number->save();

                            $monthly_member_payment = new MemberPayment;
                            $monthly_member_payment->payment_number = sprintf('%08d', $payment_number).$request->payment_number;
                            $monthly_member_payment->user_id = $user->id;
                            $monthly_member_payment->sf_amount = 0;
                            $monthly_member_payment->mf_amount = $request->mf_amount;
                            $monthly_member_payment->dc_amount = 0;
                            $monthly_member_payment->tax_type_id = '1';
                            $monthly_member_payment->tax_amount = round($request->mf_amount / 11);
                            $monthly_member_payment->tax_percentage = 10;
                            $monthly_member_payment->amount_due = $request->mf_amount;
                            $monthly_member_payment->payment_category = 'Monthly';
                            $monthly_member_payment->payment_date = Carbon::parse($user->user_memberstatus->from_date)->addMonths($pay_month);

                            if (($remaining_total_deposit - $request->mf_amount) >= 0):
                                $monthly_member_payment->amount_paid = $request->mf_amount;
                                $monthly_member_payment->rest_of_bill = 0;
                                $monthly_member_payment->payment_status = 'Paid';
                                $remaining_total_deposit = $remaining_total_deposit - $request->mf_amount;
                            else:
                                $monthly_member_payment->amount_paid = $remaining_total_deposit;
                                $monthly_member_payment->rest_of_bill = $request->mf_amount - $remaining_total_deposit;
                                $monthly_member_payment->payment_status = 'Halfpaid';
                                $remaining_total_deposit = 0;
                            endif;

                            $monthly_member_payment->save();
                            $deposit_payment = 1;
                            $pay_month++;

                            if ($pay_month == 12):
                                $current_member_status = $user->user_memberstatus;
                                $current_member_status->payment_status = 'Set';
                                $user->user_memberstatus()->save($current_member_status);
                            endif;
                        else:
                            $last_member_deposit = MemberDeposit::where('user_id', '=', $user->id)
                                ->where('status', '=', 'Pending')
                                ->orderBy('created_at', 'desc')
                                ->first();

                            //Adding into Member Deposit
                            if ($remaining_total_deposit > 0):
                                //Save Correction
                                $member_deposit = new MemberDeposit;
                                $member_deposit->user_id = $user->id;

                                if (!empty($last_member_deposit) && ($last_member_deposit->saldo > $remaining_total_deposit)):
                                    $member_deposit->payment_number = 'cash-'.$last_member_deposit->payment_number;
                                    $member_deposit->kredit = $last_member_deposit->saldo - $remaining_total_deposit;
                                    $member_deposit->saldo = $remaining_total_deposit;
                                else:
                                    $month_roman = array(1=>'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');

                                    //Update Auto Number
                                    $temp_auto_number = AutoNumber::findOrFail(3);
                                    $payment_number = $temp_auto_number->current_number;
                                    $temp_auto_number->current_number = $temp_auto_number->current_number + 1;
                                    $temp_auto_number->save();

                                    $member_deposit->payment_number = 'manual-debet/'.sprintf('%08d', $payment_number).'/RF/CIND/'.sprintf('%03d', $user->club_id).'/'.$month_roman[Carbon::now()->format('n')].'/'.Carbon::now()->format('Y');
                                    if (!empty($last_member_deposit)):
                                        $member_deposit->debet = $remaining_total_deposit - $last_member_deposit->saldo;
                                        $member_deposit->saldo = $remaining_total_deposit;
                                    else:
                                        $member_deposit->debet = $remaining_total_deposit;
                                        $member_deposit->saldo = $remaining_total_deposit;
                                    endif;
                                endif;

                                $member_deposit->status = 'Pending';
                                $member_deposit->payment_type = 'Refund';
                                $member_deposit->save();

                                $information = new MemberDepositInformation;
                                $information->information = 'Deposito - Downgrade Membership';
                                $member_deposit->memberdeposit_information()->save($information);

                                if (!empty($last_member_deposit)):
                                    $last_member_deposit_edit = MemberDeposit::findOrFail($last_member_deposit->id);
                                    $last_member_deposit_edit->status = 'Done';
                                    $last_member_deposit_edit->save();
                                endif;

                                $remaining_total_deposit = 0;
                                $deposit_payment = 0;
                            endif;
                        endif;
                    }
                else:
                    $deposit_payment = 1;
                endif;
            elseif ($membership_type->payment_type == 'Cash'):
                for ($i = 1; $i < $joining_duration; $i++):
                    //Update Auto Number
                    $temp_auto_number = AutoNumber::findOrFail(1);
                    $payment_number = $temp_auto_number->current_number;
                    $temp_auto_number->current_number = $temp_auto_number->current_number + 1;
                    $temp_auto_number->save();

                    $monthly_member_payment = new MemberPayment;
                    $monthly_member_payment->payment_number = sprintf('%08d', $payment_number).$request->payment_number;
                    $monthly_member_payment->user_id = $user->id;
                    $monthly_member_payment->sf_amount = '0';
                    $monthly_member_payment->mf_amount = $request->mf_amount;
                    $monthly_member_payment->dc_amount = '0';
                    $monthly_member_payment->tax_type_id = '1';
                    $monthly_member_payment->tax_amount = round($request->mf_amount / 11);
                    $monthly_member_payment->tax_percentage = 10;

                    $monthly_member_payment->amount_due = $request->mf_amount;
                    $monthly_member_payment->payment_category = 'First';
                    $monthly_member_payment->payment_date = Carbon::createFromFormat('Y-m-d H:i', Carbon::parse($log_payment_date->from_date)->format('Y-m-d H:i'), session('timezone'));
                    $monthly_member_payment->payment_method_id = $request->payment_method;

                    if ($remaining_total_deposit  > $request->mf_amount):
                        $monthly_member_payment->amount_paid = $request->mf_amount;
                        $monthly_member_payment->rest_of_bill = 0;
                    else:
                        $monthly_member_payment->amount_paid = $remaining_total_deposit;
                        $monthly_member_payment->rest_of_bill = $request->mf_amount - $remaining_total_deposit;
                    endif;

                    if (($request->mf_amount - $remaining_total_deposit) > 0):
//                        if ($remaining_total_deposit == 0):
//                            $monthly_member_payment->payment_status = 'Unpaid';
//                        else:
//                            $monthly_member_payment->payment_status = 'Halfpaid';
//                        endif;
                        $monthly_member_payment->payment_status = 'Halfpaid';
                    else:
                        $monthly_member_payment->payment_status = 'Paid';
                    endif;

                    $monthly_member_payment->save();

                    $remaining_total_deposit = $remaining_total_deposit - $request->mf_amount;
                    $deposit_payment = 1;
                endfor;

                if ($remaining_total_deposit > 0):
                    $last_member_deposit = MemberDeposit::where('user_id', '=', $user->id)
                        ->where('status', '=', 'Pending')
                        ->orderBy('created_at', 'desc')
                        ->first();

                    //Save Correction
                    $member_deposit = new MemberDeposit;
                    $member_deposit->user_id = $user->id;

                    if (!empty($last_member_deposit) && ($last_member_deposit->saldo > $remaining_total_deposit)):
                        $member_deposit->payment_number = 'cash-'.$last_member_deposit->payment_number;
                        $member_deposit->kredit = $last_member_deposit->saldo - $remaining_total_deposit;
                        $member_deposit->saldo = $remaining_total_deposit;
                    else:
                        $month_roman = array(1=>'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');

                        //Update Auto Number
                        $temp_auto_number = AutoNumber::findOrFail(3);
                        $payment_number = $temp_auto_number->current_number;
                        $temp_auto_number->current_number = $temp_auto_number->current_number + 1;
                        $temp_auto_number->save();

                        $member_deposit->payment_number = 'manual-debet/'.sprintf('%08d', $payment_number).'/RF/CIND/'.sprintf('%03d', $user->club_id).'/'.$month_roman[Carbon::now()->format('n')].'/'.Carbon::now()->format('Y');
                        if (!empty($last_member_deposit)):
                            $member_deposit->debet = $remaining_total_deposit - $last_member_deposit->saldo;
                        else:
                            $member_deposit->debet = $remaining_total_deposit;
                        endif;
                        $member_deposit->saldo = $remaining_total_deposit;
                    endif;

                    $member_deposit->status = 'Pending';
                    $member_deposit->payment_type = 'Refund';
                    $member_deposit->save();

                    $information = new MemberDepositInformation;
                    $information->information = 'Deposito - Downgrade Membership';
                    $member_deposit->memberdeposit_information()->save($information);

                    if (!empty($last_member_deposit)):
                        $last_member_deposit_edit = MemberDeposit::findOrFail($last_member_deposit->id);
                        $last_member_deposit_edit->status = 'Done';
                        $last_member_deposit_edit->save();
                    endif;

                    $remaining_total_deposit = 0;
                    $deposit_payment = 0;
                endif;
            elseif ($membership_type->payment_type == 'Prepaid'):
                if ($remaining_total_deposit > 0):
                    $last_member_deposit = MemberDeposit::where('user_id', '=', $user->id)
                        ->where('status', '=', 'Pending')
                        ->orderBy('created_at', 'desc')
                        ->first();

                    //Save Correction
                    $member_deposit = new MemberDeposit;
                    $member_deposit->user_id = $user->id;

                    if (!empty($last_member_deposit) && ($last_member_deposit->saldo > $remaining_total_deposit)):
                        $member_deposit->payment_number = 'cash-'.$last_member_deposit->payment_number;
                        $member_deposit->kredit = $last_member_deposit->saldo - $remaining_total_deposit;
                        $member_deposit->saldo = $remaining_total_deposit;
                    else:
                        $month_roman = array(1=>'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');

                        //Update Auto Number
                        $temp_auto_number = AutoNumber::findOrFail(3);
                        $payment_number = $temp_auto_number->current_number;
                        $temp_auto_number->current_number = $temp_auto_number->current_number + 1;
                        $temp_auto_number->save();

                        $member_deposit->payment_number = 'manual-debet/'.sprintf('%08d', $payment_number).'/RF/CIND/'.sprintf('%03d', $user->club_id).'/'.$month_roman[Carbon::now()->format('n')].'/'.Carbon::now()->format('Y');
                        if (!empty($last_member_deposit)):
                            $member_deposit->debet = $remaining_total_deposit - $last_member_deposit->saldo;
                        else:
                            $member_deposit->debet = $remaining_total_deposit;
                        endif;
                        $member_deposit->saldo = $remaining_total_deposit;
                    endif;

                    $member_deposit->status = 'Pending';
                    $member_deposit->payment_type = 'Refund';
                    $member_deposit->save();

                    $information = new MemberDepositInformation;
                    $information->information = 'Deposito - Downgrade Membership';
                    $member_deposit->memberdeposit_information()->save($information);

                    if (!empty($last_member_deposit)):
                        $last_member_deposit_edit = MemberDeposit::findOrFail($last_member_deposit->id);
                        $last_member_deposit_edit->status = 'Done';
                        $last_member_deposit_edit->save();
                    endif;

                    $remaining_total_deposit = 0;
                elseif ($remaining_total_deposit == 0):
                    $deposit_payment = 1;
                endif;
            endif;

            if ($deposit_payment == 1):
                $last_member_deposit = MemberDeposit::where('user_id', '=', $user->id)
                    ->where('status', '=', 'Pending')
                    ->orderBy('created_at', 'desc')
                    ->first();

                if (!empty($last_member_deposit)):
                    //Save Correction
                    $member_deposit = new MemberDeposit;
                    $member_deposit->user_id = $user->id;
                    $member_deposit->payment_number = 'cash-'.$last_member_deposit->payment_number;
                    $member_deposit->kredit = $last_member_deposit->saldo;
                    $member_deposit->saldo = 0;
                    $member_deposit->status = 'Done';
                    $member_deposit->payment_type = 'Refund';
                    $member_deposit->save();

                    $information = new MemberDepositInformation;
                    $information->information = 'Deposito - Downgrade Membership';
                    $member_deposit->memberdeposit_information()->save($information);
                endif;

                if (!empty($last_member_deposit)):
                    $last_member_deposit_edit = MemberDeposit::findOrFail($last_member_deposit->id);
                    $last_member_deposit_edit->status = 'Done';
                    $last_member_deposit_edit->save();
                endif;
            endif;

            // Jonviter DEV
            // $member_payment->payment_date = $member_payment->payment_date->toDateTimeString(); 
            // $paymentData = $member_payment->toArray();
            // $apiClient = new ApiClientService();
            // $mobile_curves_api = $apiClient->post('/mobile-curves/payment/' . Auth::id(), $paymentData);
            // Jonviter DEV

            // AMORTISASI
            app('App\Http\Controllers\AccountingAmortizationController')->membership_change_amortization($member_payment, $membership_type);

            //AKUNTING
            app('App\Http\Controllers\TempJournalController')->membership_payment($member_payment, false);

            return redirect('cf-lite/payment/invoice/'.$member_payment->id.'/member');
        endif;
    }

    // Updated February 5th, 2020
    public function cashier_membership_brl(Request $request, int $brl_id) {
        $staffs = User::where('club_id', '=', Auth::user()->club_id)
            ->where('role_id', '<>', '7')
            ->where(function ($q) {
                $q->whereDoesntHave('user_staffinactive')
                    ->orWhere(function ($r) {
                        $r->whereHas('user_staffinactive', function ($s) {
                            $s->where('status', '=', 'Reactivated')
                                ->where('thru_date', null);
                        });
                    });
            })
            ->orderBy('name', 'asc')
            ->pluck('name', 'id');

        $brls = BuddyReferralLead::where('status_gm', '=', 'Guest')->where('club_id', '=', Auth::user()->club_id)
            ->orderBy('fullname', 'asc')
            ->get();

        $selected_brl = BuddyReferralLead::findOrFail($brl_id);

        $sources = MemberSource::orderBy('source', 'asc')->pluck('source', 'id');

        $payment_methods = PaymentMethod::availableForPayment()->pluck('payment_method', 'id');

        $membership_types = ClubServiceFee::selectRaw('membership_types.id, membership_types.membership_type_name, membership_types.duration,
            club_service_fees.sf_amount, club_service_fees.mf_amount, club_service_fees.pre_pay_discount_value')
            ->join('membership_types', 'membership_types.id', '=', 'club_service_fees.membership_type_id')
            ->where('club_service_fees.club_id', '=', Auth::user()->club_id)
            ->whereDate('club_service_fees.from_date', '<=', Carbon::now()->toDateString())
            ->where('club_service_fees.thru_date', null)
            ->where('membership_types.membership_type', '<>', 'Staff')
            ->where('membership_types.status', '=', 'Active')
            ->groupBy('membership_types.id', 'club_service_fees.sf_amount', 'club_service_fees.mf_amount', 'club_service_fees.pre_pay_discount_value')
            ->orderBy('membership_types.membership_type_name', 'asc')
            ->get();

        $banks = Bank::where('id', '<>', 1)
            ->whereHas('bank_club', function ($q){
                $q->where('club_id', '=', Auth::user()->club_id);
            })->get();

        $wcpScript = WebClientPrint::createScript(action('WebClientPrintController@processRequest'), action('PrintESCPOSController@cashierMembership'), Session::getId());

        return view('cf_lite.cashiers.membership-cashier', compact('staffs', 'sources', 'payment_methods', 'membership_types', 'banks', 'brls', 'wcpScript','selected_brl'));
    }

    public function showEzypayInstallments($purchase)
    {
        $purchase = DB::table('ezypay_membership_purchases')->where('id', $purchase)
            ->where('cashier_id', Auth::id())->first();
        abort_unless($purchase, 404);
        if ($purchase->installments_completed || $purchase->installment_remaining <= 0) {
            return redirect('cf-lite/cashier/membership');
        }
        $payment = MemberPayment::findOrFail($purchase->member_payment_id);
        $member = User::findOrFail($payment->user_id);
        return view('cf_lite.cashiers.ezypay-required-installments', compact('purchase', 'payment', 'member'));
    }

    private function ownedPendingEzypayPurchase($purchase)
    {
        $record = DB::table('ezypay_membership_purchases')->where('id', $purchase)
            ->where('cashier_id', Auth::id())->first();
        abort_unless($record && !$record->installments_completed, 404);
        return $record;
    }

    public function getEzypayInstallmentMethods($purchase, ApiClientService $apiClient)
    {
        $record = $this->ownedPendingEzypayPurchase($purchase);
        try {
            $options = $this->loadEzypayPaymentOptions($record->customer_id_ezypay, $apiClient);
            return response()->json(['data' => $options])->header('Cache-Control', 'no-store');
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Gagal memuat metode pembayaran. Silakan muat ulang.'], 502);
        }
    }

    private function loadEzypayPaymentOptions(string $customerId, ApiClientService $apiClient): array
    {
        $methods = $apiClient->get('/mobile-curves/ezypay/payment-methods/' . rawurlencode($customerId))
            ->throw()->json('response.data');
        if (!is_array($methods) || !array_is_list($methods)) {
            throw new \UnexpectedValueException('Invalid EZYPAY payment methods response.');
        }

        $options = [];
        foreach ($methods as $method) {
            $token = data_get($method, 'paymentMethodToken');
            if (data_get($method, 'valid') !== true || !is_string($token) || trim($token) === ''
                || data_get($method, 'customerId', $customerId) !== $customerId) {
                continue;
            }

            $last4 = data_get($method, 'card.last4') ?? data_get($method, 'bank.last4');
            $options[] = [
                'token' => $token,
                'label' => implode(' - ', array_filter([
                    $method['type'] ?? '',
                    data_get($method, 'card.type') ?? data_get($method, 'bank.bankName'),
                    is_scalar($last4) && preg_match('/^[0-9]{4}$/D', (string) $last4) ? '**** '.$last4 : '',
                    data_get($method, 'card.accountHolderName') ?? data_get($method, 'bank.accountHolderName'),
                ])),
                'primary' => $method['primary'] ?? false,
            ];
        }

        return $options;
    }

    public function storeEzypayInstallmentCard($purchase, Request $request, ApiClientService $apiClient)
    {
        $record = $this->ownedPendingEzypayPurchase($purchase);
        if ($record->renewal_user_id !== null) {
            return $this->storeBillEzypayCard($record->renewal_user_id, $request, $apiClient);
        }
        $lead = BuddyReferralLead::where('id', $record->lead_id)->where('club_id', Auth::user()->club_id)->firstOrFail();
        abort_unless($lead->customer_id_ezypay === $record->customer_id_ezypay, 422);
        $request->merge(['lead_id' => $lead->id]);
        $request->attributes->set('ezypay_installment_lead', $lead);
        return $this->storeEzypayPaymentMethod($request, $apiClient);
    }

    public function storeEzypayInstallments($purchase, Request $request)
    {
        $record = $this->ownedPendingEzypayPurchase($purchase);
        $data = $request->validate(['paymentMethodToken' => 'required|string|max:255', 'memo' => 'nullable|string|max:2000']);
        $service = app(\App\Services\EzypayInstallmentService::class);
        $rows = $service->validate($record->installment_remaining, 0, $request->input('installments', []));
        try {
            $methods = app(ApiClientService::class)->get('/mobile-curves/ezypay/payment-methods/' . rawurlencode($record->customer_id_ezypay))->throw()->json('response.data');
            $valid = is_array($methods) && collect($methods)->contains(function ($method) use ($data) {
                return is_array($method) && ($method['paymentMethodToken'] ?? null) === $data['paymentMethodToken'] && ($method['valid'] ?? false) === true;
            });
            if (!$valid) return back()->withErrors(['paymentMethodToken' => 'Pilih metode pembayaran yang valid untuk customer ini.'])->withInput();
        } catch (\Throwable $e) {
            return back()->withErrors(['paymentMethodToken' => 'Gagal memvalidasi metode pembayaran. Silakan coba kembali.'])->withInput();
        }
        // Keep an attempt marker outside the transaction for uncertain remote outcomes.
        $reserved = DB::table('ezypay_membership_purchases')->where('id', $record->id)->where('installments_completed', false)
            ->where(function ($q) { $q->whereNull('installment_sync_status')->orWhere('installment_sync_status', 'failed'); })
            ->update(['installment_sync_status' => 'processing', 'updated_at' => Carbon::now()]);
        if (!$reserved) return back()->withErrors(['installments' => 'Sinkronisasi sedang berjalan atau perlu diperiksa. Jangan mengirim batch ulang.'])->withInput();
        try {
            DB::transaction(function () use ($record, $service, $rows, $data) {
                $record = DB::table('ezypay_membership_purchases')->where('id', $record->id)->lockForUpdate()->first();
                $firstPayment = MemberPayment::findOrFail($record->member_payment_id);
                $status = $record->continue_membership_id
                    ? ContinueMembership::where('user_id', $firstPayment->user_id)->findOrFail($record->continue_membership_id)
                    : MemberStatus::where('user_id', $firstPayment->user_id)->firstOrFail();
                $service->storeAndSync($record, $firstPayment, $status, $rows, $data['paymentMethodToken'], $data['memo'] ?? '');
            });
        } catch (\Throwable $e) {
            $http = $e instanceof \Illuminate\Http\Client\RequestException ? $e->response->status() : null;
            DB::table('ezypay_membership_purchases')->where('id', $record->id)->update([
                'installment_sync_status' => $http && $http >= 400 && $http < 500 ? 'failed' : 'needs_review', 'updated_at' => Carbon::now(),
            ]);
            \Illuminate\Support\Facades\Log::warning('EZYPAY installment batch failed.', ['purchase_id' => $record->id, 'http_status' => $http, 'exception_type' => get_class($e)]);
            return back()->withErrors(['installments' => 'Sinkronisasi cicilan gagal. Data cicilan sudah di-rollback. Periksa status batch EZYPAY sebelum mencoba kembali.'])->withInput();
        }
        Session::flash('cf_lite.includes.flash-message', 'Jadwal cicilan berhasil disimpan dan disinkronkan ke EZYPAY.');
        return redirect('cf-lite/cashier/membership');
    }

    public function getEzypayCheckoutOptions(Request $request, ApiClientService $apiClient)
    {
        $data = $request->validate(['lead_id' => 'required|integer', 'membership_type_id' => 'required|integer']);
        $lead = BuddyReferralLead::where('club_id', Auth::user()->club_id)
            ->where('status_gm', 'Guest')->findOrFail($data['lead_id']);
        $membership = MembershipType::where('status', 'Active')->findOrFail($data['membership_type_id']);
        abort_unless(ClubServiceFee::where('club_id', Auth::user()->club_id)
            ->where('membership_type_id', $membership->id)->whereDate('from_date', '<=', Carbon::today())
            ->whereNull('thru_date')->exists(), 403);
        if (empty($lead->customer_id_ezypay) || empty($membership->plan_id_ezypay)) {
            return response()->json(['message' => 'Customer dan plan EZYPAY harus tersedia terlebih dahulu.'], 422);
        }
        try {
            $methods = $apiClient->get('/mobile-curves/ezypay/payment-methods/' . rawurlencode($lead->customer_id_ezypay))
                ->throw()->json('response.data');
            $plan = $apiClient->get('/mobile-curves/ezypay/plans/' . rawurlencode($membership->plan_id_ezypay))
                ->throw()->json('response');
            if (!is_array($methods) || !array_is_list($methods) || !is_array($plan)
                || ($plan['id'] ?? null) !== $membership->plan_id_ezypay) {
                throw new \UnexpectedValueException('Invalid EZYPAY checkout response.');
            }
            $options = [];
            foreach ($methods as $method) {
                if (!is_array($method) || ($method['valid'] ?? false) !== true || empty($method['paymentMethodToken'])) continue;
                $card = $method['card'] ?? [];
                $options[] = [
                    'paymentMethodToken' => $method['paymentMethodToken'],
                    'label' => implode(' - ', array_filter([$method['type'] ?? '', $card['type'] ?? '',
                        isset($card['last4']) ? '**** ' . $card['last4'] : '', $card['accountHolderName'] ?? ''])),
                    'primary' => $method['primary'] ?? false,
                ];
            }
            return response()->json(['paymentMethods' => $options, 'plan' => \Illuminate\Support\Arr::only($plan, [
                'id', 'name', 'accountingCode', 'status', 'amount', 'interval', 'intervalUnit',
            ])])->header('Cache-Control', 'no-store');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to load EZYPAY checkout options.', ['lead_id' => $lead->id]);
            return response()->json(['message' => 'Gagal memuat metode pembayaran atau informasi plan EZYPAY. Silakan coba kembali.'], 502);
        }
    }

    public function ensureEzypayMembershipPlan(Request $request, ApiClientService $apiClient)
    {
        $data = $request->validate([
            'membership_type_id' => 'required|integer',
            'sf_amount' => 'required|numeric|min:0',
            'mf_amount' => 'required|numeric|min:0',
        ]);
        try {
            return DB::transaction(function () use ($data, $apiClient) {
                // Serialize creation per membership type to avoid duplicate plans.
                $membership = MembershipType::where('status', 'Active')
                    ->where('membership_type', '<>', 'Staff')->lockForUpdate()->findOrFail($data['membership_type_id']);
                $fee = ClubServiceFee::where('club_id', Auth::user()->club_id)
                    ->where('membership_type_id', $membership->id)
                    ->whereDate('from_date', '<=', Carbon::today()->toDateString())
                    ->whereNull('thru_date')->where('sf_amount', $data['sf_amount'])
                    ->where('mf_amount', $data['mf_amount'])->firstOrFail();
                if (!empty($membership->plan_id_ezypay)) {
                    return response()->json(['plan_id_ezypay' => $membership->plan_id_ezypay]);
                }
                $amount = (float) $fee->sf_amount + (float) $fee->mf_amount * (int) $membership->duration;
                if ($amount <= 0) {
                    return response()->json(['message' => 'Total biaya plan harus lebih dari nol.'], 422);
                }
                $response = $apiClient->post('/mobile-curves/ezypay/plans', [
                    'name' => $membership->membership_type_name,
                    'amountValue' => $amount,
                    'plan_type' => 'MEMBERSHIP',
                ])->throw();
                $planId = $response->json('response.id');
                if (!is_string($planId) || trim($planId) === '' || strlen($planId) > 255) {
                    throw new \UnexpectedValueException('Missing EZYPAY plan ID.');
                }
                $membership->plan_id_ezypay = $planId;
                $membership->save();
                return response()->json(['plan_id_ezypay' => $planId]);
            });
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Tipe keanggotaan atau biaya aktif tidak ditemukan untuk klub ini.'], 422);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Failed to create EZYPAY membership plan.', [
                'membership_type_id' => $data['membership_type_id'], 'exception' => $e,
            ]);
            return response()->json(['message' => 'Gagal menyiapkan plan EZYPAY. Silakan coba kembali.'], 502);
        }
    }

    public function storeEzypayPaymentMethod(Request $request, ApiClientService $apiClient)
    {
        $lead = $request->attributes->get('ezypay_installment_lead');
        if ($lead === null) {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), ['lead_id' => 'required|integer']);
            if ($validator->fails()) {
                return response()->json(['message' => 'Data calon anggota tidak valid.', 'errors' => $validator->errors()], 422);
            }
            $lead = BuddyReferralLead::where('club_id', Auth::user()->club_id)
                ->where('status_gm', 'Guest')->findOrFail($validator->validated()['lead_id']);
        }

        return $this->saveEzypayCard($request, $apiClient, $lead->customer_id_ezypay);
    }

    public function storeBillEzypayCard($id, Request $request, ApiClientService $apiClient)
    {
        $member = User::where('club_id', Auth::user()->club_id)->findOrFail($id);

        return $this->saveEzypayCard($request, $apiClient, $member->customer_id_ezypay);
    }

    private function saveEzypayCard(Request $request, ApiClientService $apiClient, ?string $customerId)
    {
        // Validate manually so card details are never flashed into the session.
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'accountHolderName' => 'required|string|max:255',
            'accountNumber' => 'required|string|regex:/^[0-9]{12,19}$/',
            'expiryMonth' => ['required', 'string', 'regex:/^(0[1-9]|1[0-2])$/'],
            'expiryYear' => 'required|string|regex:/^[0-9]{2}$/',
            'termAndConditionAgreed' => 'required|accepted',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Periksa data kartu dan persetujuan pemilik kartu.', 'errors' => $validator->errors()], 422);
        }
        $data = $validator->validated();
        $expiry = Carbon::createFromDate(2000 + (int) $data['expiryYear'], (int) $data['expiryMonth'], 1)->endOfMonth();
        if ($expiry->lt(Carbon::now())) {
            return response()->json(['message' => 'Masa berlaku kartu sudah berakhir.'], 422);
        }
        if (empty($customerId)) {
            return response()->json(['message' => 'Customer EZYPAY belum tersedia.'], 422);
        }
        try {
            $apiClient->post('/mobile-curves/ezypay/payment-methods/' . rawurlencode($customerId), [
                'payment_method_type' => 'card',
                'accountHolderName' => $data['accountHolderName'],
                'accountNumber' => $data['accountNumber'],
                'expiryMonth' => $data['expiryMonth'],
                'expiryYear' => $data['expiryYear'],
                'termAndConditionAgreed' => true,
            ])->throw();
            return response()->json(['message' => 'Metode pembayaran berhasil ditambahkan.']);
        } catch (\Throwable $e) {
            // Do not log the exception or provider body: they may contain card data.
            $httpStatus = $e instanceof \Illuminate\Http\Client\RequestException
                ? $e->response->status() : null;
            \Illuminate\Support\Facades\Log::warning('Failed to add EZYPAY payment method.', [
                'http_status' => $httpStatus,
                'exception_type' => get_class($e),
            ]);
            return response()->json(['message' => 'Gagal menambahkan kartu. Muat ulang daftar untuk memeriksa apakah kartu sudah tersimpan sebelum mencoba kembali.'], 502);
        }
    }

    public function getEzypayPaymentMethods(Request $request, ApiClientService $apiClient)
    {
        $data = $request->validate(['lead_id' => 'required|integer']);
        $lead = BuddyReferralLead::where('club_id', Auth::user()->club_id)
            ->where('status_gm', 'Guest')->findOrFail($data['lead_id']);
        if (empty($lead->customer_id_ezypay)) {
            return response()->json(['message' => 'Customer EZYPAY belum tersedia.'], 422);
        }

        try {
            $response = $apiClient->get('/mobile-curves/ezypay/payment-methods/' . rawurlencode($lead->customer_id_ezypay))->throw();
            $methods = $response->json('response.data');
            if (!is_array($methods) || !array_is_list($methods)) {
                throw new \UnexpectedValueException('Invalid EZYPAY payment methods response.');
            }
            // Return only the information needed for display, excluding payment tokens.
            $methods = array_map(function ($method) {
                if (!is_array($method)) {
                    throw new \UnexpectedValueException('Invalid EZYPAY payment method.');
                }
                return [
                    'type' => $method['type'] ?? null,
                    'card' => isset($method['card']) ? \Illuminate\Support\Arr::only($method['card'], [
                        'last4', 'accountHolderName', 'type', 'expiryMonth', 'expiryYear',
                    ]) : null,
                    'valid' => $method['valid'] ?? false,
                    'primary' => $method['primary'] ?? false,
                ];
            }, $methods);
            return response()->json(['data' => $methods]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Failed to fetch EZYPAY payment methods.', [
                'lead_id' => $lead->id, 'exception' => $e,
            ]);
            return response()->json(['message' => 'Gagal memuat metode pembayaran EZYPAY. Silakan coba kembali.'], 502);
        }
    }

    public function saveEzypayCustomer(Request $request, ApiClientService $apiClient)
    {
        $staff = Auth::user();
        $data = $request->validate([
            'lead_id' => 'nullable|integer',
            'club_id' => 'required|integer',
            'premember_name' => 'required_without:lead_id|nullable|string|max:100',
            'fa_coach' => 'nullable|integer|exists:users,id,club_id,' . $staff->club_id,
            'source' => 'nullable|integer|exists:member_sources,id',
            'phone' => 'required|string|max:50|regex:/^\+?[0-9]{6,20}$/',
            'email' => 'required|email|max:50',
            'wa_number' => 'required_with:lead_id|nullable|string|max:20|regex:/^\+?[0-9]{6,20}$/',
        ]);
        abort_unless((int) $data['club_id'] === (int) $staff->club_id, 403);

        $leadId = $data['lead_id'] ?? null;
        $lead = BuddyReferralLead::where('club_id', $staff->club_id)->where('status_gm', 'Guest');
        if ($leadId !== null) {
            $lead = $lead->findOrFail($leadId);
        } else {
            $lead = new BuddyReferralLead;
            $lead->fullname = $data['premember_name'];
            $lead->club_id = $staff->club_id;
            $lead->staff_id = $data['fa_coach'] ?? $staff->id;
            $lead->source_id = $data['source'] ?? null;
            $lead->is_br = $lead->source_id == 1 ? 'BR' : 'NotBR';
            $lead->status_gm = 'Guest';
        }

        // Keep the contact and lead ID if EZYPAY customer creation fails.
        $lead->phone = $data['phone'];
        $lead->email = $data['email'];
        $lead->wa_number = $leadId === null ? $data['phone'] : $data['wa_number'];
        $lead->save();

        $contact = ['lead_id' => $lead->id, 'name' => $lead->fullname, 'source_id' => $lead->source_id,
            'phone' => $lead->phone, 'email' => $lead->email, 'wa_number' => $lead->wa_number];
        try {
            $customerId = DB::transaction(function () use ($lead, $data, $apiClient) {
                $lead = BuddyReferralLead::lockForUpdate()->findOrFail($lead->id);
                if (!empty($lead->customer_id_ezypay)) {
                    return $lead->customer_id_ezypay;
                }

                $response = $apiClient->post("/mobile-curves/ezypay/customers/{$data['club_id']}", [
                    'name' => $lead->fullname,
                    'email' => $lead->email,
                    'mobilePhone' => $lead->wa_number,
                    'homePhone' => $lead->phone,
                ])->throw();
                $customerId = $response->json('response.id');
                if (!is_string($customerId) || trim($customerId) === '' || strlen($customerId) > 255) {
                    throw new \UnexpectedValueException('Missing EZYPAY customer ID.');
                }
                $lead->customer_id_ezypay = $customerId;
                $lead->save();
                return $customerId;
            });
        } catch (\Throwable $e) {
            $response = $e instanceof \Illuminate\Http\Client\RequestException ? $e->response : null;
            $status = $response?->status();
            $providerCode = $response?->json('error.code');
            $fieldLabels = [
                'firstName' => 'nama depan',
                'lastName' => 'nama belakang',
                'email' => 'email',
                'mobilePhone' => 'nomor ponsel',
                'homePhone' => 'nomor telepon',
            ];
            $fields = [];
            if (is_string($providerCode)) {
                // Only surface known field names. The provider response may contain contact details.
                preg_match_all('/\[(firstName|lastName|email|mobilePhone|homePhone)\]/', $providerCode, $matches);
                $fields = array_values(array_unique($matches[1]));
            }

            \Illuminate\Support\Facades\Log::error('Failed to create EZYPAY customer.', [
                'lead_id' => $lead->id,
                'http_status' => $status,
                'provider_fields' => $fields,
                'access_denied' => $providerCode === 'access_denied',
                'exception_type' => get_class($e),
            ]);

            $detail = match (true) {
                $status === 403 => 'EZYPAY menolak akses untuk membuat customer (HTTP 403).',
                $fields !== [] => 'EZYPAY meminta perbaikan pada ' . implode(', ', array_intersect_key($fieldLabels, array_flip($fields))) . '.',
                $status === 400 => 'EZYPAY menolak data customer (HTTP 400). Periksa nama, email, dan nomor telepon.',
                default => 'Pembuatan customer EZYPAY gagal. Silakan coba kembali.',
            };
            return response()->json($contact + ['message' => 'Data kontak tersimpan. ' . $detail], 502);
        }

        return response()->json($contact + ['customer_id_ezypay' => $customerId]);
    }

    public function cashier_membership() {
        $user =
            User::where('club_id', '=', Auth::user()->club_id)
            ->where('role_id', '<>', '7')
            ->where(function ($q) {
                $q->whereDoesntHave('user_staffinactive')
                    ->orWhere(function ($r) {
                        $r->whereHas('user_staffinactive', function ($s) {
                            $s->where('status', '=', 'Reactivated')
                                ->where('thru_date', null);
                        });
                    });
            })
            ->orderBy('name', 'asc')->first();

        $staffs = User::where('club_id', '=', Auth::user()->club_id)
            ->where('role_id', '<>', '7')
            ->whereHas('user_memberstatus', function ($q) {
                $q->where('status', '=', 'Active');
            })
            ->where(function ($q) {
                $q->whereDoesntHave('user_staffinactive')
                    ->orWhere(function ($r) {
                        $r->whereHas('user_staffinactive', function ($s) {
                            $s->where('status', '=', 'Reactivated')
                                ->where('thru_date', null);
                        });
                    });
            })
            ->orderBy('name', 'asc')
            ->pluck('name', 'id');

        $brls = BuddyReferralLead::where('status_gm', '=', 'Guest')->where('club_id', '=', Auth::user()->club_id)
            ->orderBy('fullname', 'asc')
            ->get();

        $sources = MemberSource::orderBy('source', 'asc')->pluck('source', 'id');

        $payment_methods = PaymentMethod::availableForPayment()->pluck('payment_method', 'id');

        $membership_types = ClubServiceFee::selectRaw('membership_types.id, membership_types.membership_type_name, membership_types.duration,
            club_service_fees.sf_amount, club_service_fees.mf_amount, club_service_fees.pre_pay_discount_value')
            ->join('membership_types', 'membership_types.id', '=', 'club_service_fees.membership_type_id')
            ->where('club_service_fees.club_id', '=', Auth::user()->club_id)
            ->whereDate('club_service_fees.from_date', '<=', Carbon::now()->toDateString())
            ->where('club_service_fees.thru_date', null)
            ->where('membership_types.membership_type', '<>', 'Staff')
            ->where('membership_types.status', '=', 'Active')
            ->groupBy('membership_types.id', 'membership_types.membership_type_name', 'membership_types.duration', 'club_service_fees.sf_amount', 'club_service_fees.mf_amount', 'club_service_fees.pre_pay_discount_value')
            ->orderBy('membership_types.membership_type_name', 'asc')
            ->get();

        $banks = Bank::where('id', '<>', 1)
            ->whereHas('bank_club', function ($q){
                $q->where('club_id', '=', Auth::user()->club_id);
            })->get();

        $wcpScript = WebClientPrint::createScript(action('WebClientPrintController@processRequest'), action('PrintESCPOSController@cashierMembership'), Session::getId());


        //ACCURATE
        /* $accurate = AccurateAuth::find(1);

        $redirectURI = env('ACCURATE_REDIRECT_URI_MEMBERSHIP');
        //$databaseID = Auth::user() ? Accurate::accurateClub(Auth::user()->club_id) : env('ACCURATE_DATABASE_ID');
        $accurateClub = Auth::user() ? Accurate::accurateClub(Auth::user()->club_id) : '';

        if($accurateClub){
            $databaseID = $accurateClub->accurate_id;

            $openDB = Accurate::AccurateOpenDB($databaseID, $accurate->access_token );

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
        return view('cf_lite.cashiers.membership-cashier', compact('user','staffs', 'sources', 'payment_methods', 'membership_types', 'banks', 'brls', 'wcpScript'));
    }

    public function store_cashier_membership(Request $request) {
        /* $accurate = AccurateAuth::find(1);

        //$databaseID = Auth::user() ? Accurate::accurateClub(Auth::user()->club_id) : env('ACCURATE_DATABASE_ID');
        $accurateClub = Auth::user() ? Accurate::accurateClub(Auth::user()->club_id) : '';
        $openDB = '';
        $branchID = '';
        $databaseID = '';
        if($accurateClub){
            $databaseID = $accurateClub->accurate_id;
            $openDB = Accurate::AccurateOpenDB($databaseID, $accurate->access_token );
            //$branchID = Accurate::accurateBranch(Auth::user()->club_id);
            $branchID = $accurateClub->accurate_branch;
        } */

        $this->validate($request, [
            'fa_coach' => 'required|exists:users,id',
            'fa_date' => 'required|date',
            'premember_name' => 'required|string',
            'phone' => 'required|string',
            'source' => 'required|exists:member_sources,id',
            'membership_type' => 'required|exists:membership_types,id',
            'payment_method' => 'required|exists:mst_payment_methods,id',
            'sf_amount' => 'required|numeric',
            'mf_amount' => 'required|numeric',
            'discount' => 'required|numeric',
            'total' => 'required|numeric',
            'pay' => 'required|numeric',
        ]);

        //Buddy Referral Lead
        $lead = new BuddyReferralLead;
        $lead->fullname = $request->premember_name;
        $lead->phone = $request->phone;
        $lead->source_id = $request->source;

        if ($request->source == '1'):
            $lead->is_br = 'BR';
        else:
            $lead->is_br = 'NotBR';
        endif;

        if (!empty($request->staff)):
            $lead->staff_id = $request->fa_coach;
        else:
            if (!empty(session('staff_session'))):
                $lead->staff_id = session('staff_session');
            else:
                $lead->staff_id = Auth::id();
            endif;
        endif;

        $lead->club_id = Auth::user()->club_id;
        $lead->status_gm = 'Member';
        $lead->save();

        if($request->source == '1'){
            $referrer = new Referrer;
            $referrer->user_id = $request->source_detail ?? 0;
            $referrer->status = 'Pending';
            $lead->buddyreferrallead_referrer()->save($referrer);
        }else if(in_array($request->source, ['5','14','15'])){
            //Facebook IG Papan Nama
            $lead->source_detail = $request->source_detail;
            $lead->save();
        }else if(in_array($request->source, ['10','12','13','17'])){
            $lead->location = $request->source_detail;
            $lead->save();
        }

        //Guest Appointment
        $guest_appt = new GuestAppointment;
        $guest_appt->guest_id = $lead->id;
        $guest_appt->staff_id = $lead->staff_id;
        $guest_appt->fa_status = 'NonFA';

        $datetime = Carbon::createFromFormat('Y-m-d H:i', Carbon::parse($request->fa_date.' 09:01')->format('Y-m-d H:i'), session('timezone'))->setTimezone('UTC');

        $guest_appt->fa_date = $datetime;
        $guest_appt->fa_hour = date('H:i', strtotime($datetime));
        $guest_appt->guest_status_after = 'Member';
        $guest_appt->club_id = $lead->club_id;
        $guest_appt->save();

        //Guest Appointment Status
        $guest_appt_status = new GuestAppointmentStatus;
        $guest_appt_status->status_id = '5';
        $guest_appt_status->c_date = $datetime;
        $guest_appt_status->s_date = $datetime;
        $guest_appt_status->sa_date = $datetime;
        $guest_appt->guestappointment_gastatus()->save($guest_appt_status);

        //Add Member
        $user = new User;
        $user->name = $request->premember_name;
        $user->email = date('dmYhis', strtotime(Carbon::now())).'@curvesmember.com';
        $user->password = bcrypt('curvesindonesia');
        $user->role_id = '7';
        $user->club_id = session('club');
        $user->username = date('dnygi', strtotime(Carbon::now())).'.cm.'.$lead->id;
        $user->save();

        //Update Auto Number
        $temp_auto_number = AutoNumber::findOrFail('1');
        $payment_number = $temp_auto_number->current_number;
        $temp_auto_number->current_number = $temp_auto_number->current_number + 1;
        $temp_auto_number->save();

        //Adding Member Payment
        $month_roman = array(1=>'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');
        $member_payment = new MemberPayment;
        $member_payment->payment_number = sprintf('%08d', $payment_number).'/PY/CIND/'.sprintf('%03d', Auth::user()->club_id).'/'.$month_roman[Carbon::now()->format('n')].'/'.Carbon::now()->format('Y');
        $member_payment->user_id = $user->id;
        $member_payment->sf_amount = $request->sf_amount;
        $member_payment->mf_amount = $request->mf_amount;
        $member_payment->dc_amount = $request->discount;
        $member_payment->tax_type_id = '1';
        $member_payment->tax_amount = round($request->total / 11);
        $member_payment->tax_percentage = 10;
        $member_payment->amount_due = $request->total;
        $member_payment->payment_category = 'First';

        if (strtotime($request->fa_date) == strtotime(Carbon::today())):
            $member_payment->payment_date = Carbon::createFromFormat('Y-m-d H:i', Carbon::now()->format('Y-m-d H:i'), session('timezone'));
        else:
            $member_payment->payment_date = $request->fa_date;
        endif;

        $member_payment->payment_method_id = $request->payment_method;
        $member_payment->amount_paid = $request->pay;

        if (($request->total - $request->pay) > 0):
            $member_payment->rest_of_bill = ($request->total - $request->pay);
            $member_payment->payment_status = 'Halfpaid';
        elseif (($request->total - $request->pay) == 0):
            $member_payment->payment_status = 'Paid';
        endif;
        $member_payment->save();

        //Adding MemberStatus (member_status table)
        $membership_type = MembershipType::findOrFail($request->membership_type);

        $member_status = new MemberStatus;
        $member_status->status = 'Active';
        $member_status->membership_type_id = $request->membership_type;

        if ($membership_type->is_vip == 'general'):
            $the_duration = $membership_type->duration;
        elseif ($membership_type->is_vip == 'vip'):
            $the_duration = 0;
        endif;

        if (strtotime($request->fa_date) == strtotime(Carbon::today())):
            $member_status->from_date = Carbon::createFromFormat('Y-m-d H:i', Carbon::now()->format('Y-m-d H:i'), session('timezone'));
            $thru_date_temp = date('Y-m-d', strtotime("+".$the_duration." months", strtotime($member_status->from_date)));
        else:
            $member_status->from_date = $request->fa_date;
            $thru_date_temp = date('Y-m-d', strtotime("+".$the_duration." months", strtotime($request->fa_date)));
        endif;

        $member_status->thru_date = $thru_date_temp;

        if ($membership_type->payment_type == 'Prepaid'):
            if (($request->total - $request->pay) == 0):
                $member_status->payment_status = 'Set';
            endif;
        elseif ($membership_type->payment_type == 'Cash'):
            $member_status->payment_status = 'Set';
        endif;

        $user->user_memberstatus()->save($member_status);

        //Adding LogMemberStatus (Log for Member's Membership History)
        $log_member = new LogMemberStatus;
        $log_member->user_id = $user->id;
        $log_member->membership_type_id = $membership_type->id;
        $log_member->from_date = $member_status->from_date;
        $log_member->thru_date = $member_status->thru_date;
        $log_member->club_id = Auth::user()->club_id;
        $log_member->save();

        //Adding MemberPersonal (member_personals table)
        $member_personal = new MemberPersonal;
        $member_personal->phone = $request->phone;
        $member_personal->key_tag_id = '9999'.Keygen::numeric(5)->generate();

        if (!empty($request->fa_coach)):
            $member_personal->sales_person_id = $request->fa_coach;
        else:
            if (!empty(session('staff_session'))):
                $member_personal->sales_person_id = session('staff_session');
            else:
                $member_personal->sales_person_id = Auth::id();
            endif;
        endif;

        $member_personal->guest_id = $lead->id;

        if (strtotime($request->fa_date) == strtotime(Carbon::today())):
            $member_personal->joined = Carbon::createFromFormat('Y-m-d H:i', Carbon::now()->format('Y-m-d H:i'), session('timezone'));
        else:
            $member_personal->joined = $request->fa_date;
        endif;

        $member_personal->created_by = Auth::id();
        $user->user_memberpersonal()->save($member_personal);


        //Adding Member to Coach based on Sequence
        $club = ClubInfo::findOrFail(Auth::user()->club_id);
        $coach_sequences = CoachSequence::where('club_id', '=', $club->id)
            ->orderBy('sequence', 'desc')
            ->get();

        $max_seq = $coach_sequences[0]->sequence;
        foreach ($coach_sequences->where('sequence', $club->current_sequence) as $coach_seq):
            //Insert to coach member table
            $coach_member = new CoachMember;
            $coach_member->coach_id = $coach_seq->coach_id;
            $user->memberuser_coachmember()->save($coach_member);

            //Update club current sequence
            if ($club->current_sequence < $max_seq):
                $club->current_sequence = $club->current_sequence + 1;
            elseif ($club->current_sequence == $max_seq):
                $club->current_sequence = 1;
            endif;
            $club->save();
        endforeach;

        // Jonviter DEV
        // $member_payment->payment_date = $member_payment->payment_date->toDateTimeString(); 
        // $paymentData = $member_payment->toArray();
        // $apiClient = new ApiClientService();
        // $mobile_curves_api = $apiClient->post("/mobile-curves/payment/{$lead->staff_id}", $paymentData);
        // Jonviter DEV

        //Staff Point
        app('App\Http\Controllers\PointStaffController')->staff_point_apo($lead->staff_id, $request->fa_date);
        app('App\Http\Controllers\PointStaffController')->staff_point_apo_showup($lead->staff_id, $request->fa_date, 'showup');
        app('App\Http\Controllers\PointStaffController')->add_br_point($lead->staff_id, 28, $request->fa_date, $lead);
        app('App\Http\Controllers\PointStaffController')->membership_sales_point($user->id, $request->membership_type, $member_payment->id);

        /* $userData = [
            'name' => $request->premember_name,
            'membership_type' => $request->membership_type,
            'fa_date' => $request->fa_date,
            'pay' => $request->pay,
        ];
        //var_dump($userData);

        if($accurateClub){
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
            $accurateItem = Accurate::AccurateCheckItem($openDB, $userData);
            if($accurateItem){
                //do nothing
            }else{
                $accurateItem = Accurate::AccurateCreateItem($openDB, $userData);
            }

            $invoiceData = [
                "customerNo" => $accurateUser['customerNo'],
                "transDate" => date("d/m/Y"),
                "branchId" => $branchID,
                "detailItem" => array(
                    array(
                        "itemNo" => $accurateItem['no'],
                        "quantity" => 1,
                        "unitPrice" => $request->pay
                    )
                )
            ];

            //CREATE SALES INVOICE ONCE CUSTOMER & ITEM IS TRUE
            $accurateInvoice = Accurate::AccurateCreateInvoice($openDB, $invoiceData);

            if($accurateInvoice){
                return 'berhasil';
            }else{
                return 'Pembayaran berhasil. Tidak dapat membuat faktur di Accurate';
            }
        } */
    }

    /****************/
    /***** TEST PDF *****/
    /****************/
    public function cashier_membership_dummy() {
        $user =
            User::where('club_id', '=', Auth::user()->club_id)
            ->where('role_id', '<>', '7')
            ->where(function ($q) {
                $q->whereDoesntHave('user_staffinactive')
                    ->orWhere(function ($r) {
                        $r->whereHas('user_staffinactive', function ($s) {
                            $s->where('status', '=', 'Reactivated')
                                ->where('thru_date', null);
                        });
                    });
            })
            ->orderBy('name', 'asc')->first();

        $staffs = User::where('club_id', '=', Auth::user()->club_id)
            ->where('role_id', '<>', '7')
            ->whereHas('user_memberstatus', function ($q) {
                $q->where('status', '=', 'Active');
            })
            ->where(function ($q) {
                $q->whereDoesntHave('user_staffinactive')
                    ->orWhere(function ($r) {
                        $r->whereHas('user_staffinactive', function ($s) {
                            $s->where('status', '=', 'Reactivated')
                                ->where('thru_date', null);
                        });
                    });
            })
            ->orderBy('name', 'asc')
            ->pluck('name', 'id');

        $brls = BuddyReferralLead::where('status_gm', '=', 'Guest')->where('club_id', '=', Auth::user()->club_id)
            ->orderBy('fullname', 'asc')
            ->get();

        $sources = MemberSource::orderBy('source', 'asc')->pluck('source', 'id');

        $payment_methods = PaymentMethod::availableForPayment()->pluck('payment_method', 'id');

        $membership_types = ClubServiceFee::selectRaw('membership_types.id, membership_types.membership_type_name, membership_types.duration,
            club_service_fees.sf_amount, club_service_fees.mf_amount, club_service_fees.pre_pay_discount_value')
            ->join('membership_types', 'membership_types.id', '=', 'club_service_fees.membership_type_id')
            ->where('club_service_fees.club_id', '=', Auth::user()->club_id)
            ->whereDate('club_service_fees.from_date', '<=', Carbon::now()->toDateString())
            ->where('club_service_fees.thru_date', null)
            ->where('membership_types.membership_type', '<>', 'Staff')
            ->where('membership_types.status', '=', 'Active')
            ->groupBy('membership_types.id', 'club_service_fees.sf_amount', 'club_service_fees.mf_amount', 'club_service_fees.pre_pay_discount_value')
            ->orderBy('membership_types.membership_type_name', 'asc')
            ->get();

        $banks = Bank::where('id', '<>', 1)
            ->whereHas('bank_club', function ($q){
                $q->where('club_id', '=', Auth::user()->club_id);
            })->get();

        //$wcpScript = WebClientPrint::createScript(action('WebClientPrintController@processRequest'), action('PrintESCPOSController@cashierMembership'), Session::getId());
        $wcpScript = WebClientPrint::createScript(action('WebClientPrintController@processRequest'), action('PrintESCPOSController@cashierMembershipDummy'), Session::getId());


        return view('cf_lite.cashiers.membership-cashier-dummy', compact('user','staffs', 'sources', 'payment_methods', 'membership_types', 'banks', 'brls', 'wcpScript'));

    }

    public function store_cashier_membership_dummy(Request $request)
    {
        $this->validate($request, [
            'fa_coach' => 'required|exists:users,id',
            'fa_date' => 'required|date',
            'premember_name' => 'required|string',
            'phone' => 'required|string',
            'source' => 'required|exists:member_sources,id',
            'membership_type' => 'required|exists:membership_types,id',
            'payment_method' => 'required|exists:mst_payment_methods,id',
            'sf_amount' => 'required|numeric',
            'mf_amount' => 'required|numeric',
            'discount' => 'required|numeric',
            'total' => 'required|numeric',
            'pay' => 'required|numeric',
        ]);

        //Buddy Referral Lead
        $lead = new BuddyReferralLead;
        $lead->fullname = $request->premember_name;
        $lead->phone = $request->phone;
        $lead->source_id = $request->source;

        if ($request->source == '1') :
            $lead->is_br = 'BR';
        else :
            $lead->is_br = 'NotBR';
        endif;

        if (!empty($request->staff)) :
            $lead->staff_id = $request->fa_coach;
        else :
            if (!empty(session('staff_session'))) :
                $lead->staff_id = session('staff_session');
            else :
                $lead->staff_id = Auth::id();
            endif;
        endif;

        $lead->club_id = Auth::user()->club_id;
        $lead->status_gm = 'Member';

        //Guest Appointment
        $guest_appt = new GuestAppointment;
        $guest_appt->guest_id = $lead->id;
        $guest_appt->staff_id = $lead->staff_id;
        $guest_appt->fa_status = 'NonFA';

        $datetime = Carbon::createFromFormat('Y-m-d H:i', Carbon::parse($request->fa_date . ' 09:01')->format('Y-m-d H:i'), session('timezone'))->setTimezone('UTC');

        $guest_appt->fa_date = $datetime;
        $guest_appt->fa_hour = date('H:i', strtotime($datetime));
        $guest_appt->guest_status_after = 'Member';
        $guest_appt->club_id = $lead->club_id;
        // $guest_appt->save();

        //Guest Appointment Status
        $guest_appt_status = new GuestAppointmentStatus;
        $guest_appt_status->status_id = '5';
        $guest_appt_status->c_date = $datetime;
        $guest_appt_status->s_date = $datetime;
        $guest_appt_status->sa_date = $datetime;
        // $guest_appt->guestappointment_gastatus()->save($guest_appt_status);

        //Add Member
        $user = new User;
        $user->name = $request->premember_name;
        $user->email = date('dmYhis', strtotime(Carbon::now())) . '@curvesmember.com';
        $user->password = bcrypt('curvesindonesia');
        $user->role_id = '7';
        $user->club_id = session('club');
        $user->username = date('dnygi', strtotime(Carbon::now())) . '.cm.' . $lead->id;
        // $user->save();


        //Update Auto Number
        // $temp_auto_number = AutoNumber::findOrFail('1');
        // $payment_number = $temp_auto_number->current_number;
        // $temp_auto_number->current_number = $temp_auto_number->current_number + 1;
        // $temp_auto_number->save();


        //Adding Member Payment
        $month_roman = array(1 => 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');
        $member_payment = new MemberPayment;
        $member_payment->payment_number = sprintf('%08d', $request->payment_number) . '/PY/CIND/' . sprintf('%03d', Auth::user()->club_id) . '/' . $month_roman[Carbon::now()->format('n')] . '/' . Carbon::now()->format('Y');
        $member_payment->user_id = $user->id;
        $member_payment->sf_amount = $request->sf_amount;
        $member_payment->mf_amount = $request->mf_amount;
        $member_payment->dc_amount = $request->discount;
        $member_payment->tax_type_id = '1';
        $member_payment->tax_amount = round($request->total / 11);
        $member_payment->tax_percentage = 10;
        $member_payment->amount_due = $request->total;
        $member_payment->payment_category = 'First';

        if (strtotime($request->fa_date) == strtotime(Carbon::today())) :
            $member_payment->payment_date = Carbon::createFromFormat('Y-m-d H:i', Carbon::now()->format('Y-m-d H:i'), session('timezone'));
        else :
            $member_payment->payment_date = $request->fa_date;
        endif;

        $member_payment->payment_method_id = $request->payment_method;
        $member_payment->amount_paid = $request->pay;

        if (($request->total - $request->pay) > 0) :
            $member_payment->rest_of_bill = ($request->total - $request->pay);
            $member_payment->payment_status = 'Halfpaid';
        elseif (($request->total - $request->pay) == 0) :
            $member_payment->payment_status = 'Paid';
        endif;
        // $member_payment->save();

        //Adding MemberStatus (member_status table)
        $membership_type = MembershipType::findOrFail($request->membership_type);

        $member_status = new MemberStatus;
        $member_status->status = 'Active';
        $member_status->membership_type_id = $request->membership_type;

        if ($membership_type->is_vip == 'general') :
            $the_duration = $membership_type->duration;
        elseif ($membership_type->is_vip == 'vip') :
            $the_duration = 0;
        endif;

        if (strtotime($request->fa_date) == strtotime(Carbon::today())) :
            $member_status->from_date = Carbon::createFromFormat('Y-m-d H:i', Carbon::now()->format('Y-m-d H:i'), session('timezone'));
            $thru_date_temp = date('Y-m-d', strtotime("+" . $the_duration . " months", strtotime($member_status->from_date)));
        else :
            $member_status->from_date = $request->fa_date;
            $thru_date_temp = date('Y-m-d', strtotime("+" . $the_duration . " months", strtotime($request->fa_date)));
        endif;

        $member_status->thru_date = $thru_date_temp;

        if ($membership_type->payment_type == 'Prepaid') :
            if (($request->total - $request->pay) == 0) :
                $member_status->payment_status = 'Set';
            endif;
        elseif ($membership_type->payment_type == 'Cash') :
            $member_status->payment_status = 'Set';
        endif;

        //Adding MemberPersonal (member_personals table)
        $member_personal = new MemberPersonal;
        $member_personal->phone = $request->phone;
        $member_personal->key_tag_id = '9999' . Keygen::numeric(5)->generate();

        if (!empty($request->fa_coach)) :
            $member_personal->sales_person_id = $request->fa_coach;
        else :
            if (!empty(session('staff_session'))) :
                $member_personal->sales_person_id = session('staff_session');
            else :
                $member_personal->sales_person_id = Auth::id();
            endif;
        endif;

        $member_personal->guest_id = $lead->id;

        if (strtotime($request->fa_date) == strtotime(Carbon::today())) :
            $member_personal->joined = Carbon::createFromFormat('Y-m-d H:i', Carbon::now()->format('Y-m-d H:i'), session('timezone'));
        else :
            $member_personal->joined = $request->fa_date;
        endif;

        $member_personal->created_by = Auth::id();

    }
    /*************/


    public function renewalEzypayContact($id)
    {
        $member = User::where('club_id', Auth::user()->club_id)->where('role_id', 7)->findOrFail($id);
        return response()->json([
            'name' => $member->name, 'email' => $member->email,
            'phone' => $member->user_memberpersonal?->phone,
            'cellphone' => $member->user_memberpersonal?->cellphone,
        ])->header('Cache-Control', 'no-store');
    }

    public function renewalEzypayOptions(Request $request, ApiClientService $apiClient)
    {
        $data = $request->validate([
            'member_id' => 'required|integer',
            'membership_type_id' => 'required|integer',
            'sf_amount' => 'required|numeric|min:0',
            'mf_amount' => 'required|numeric|min:0',
        ]);
        $member = User::where('club_id', Auth::user()->club_id)->where('role_id', 7)->findOrFail($data['member_id']);
        $contact = $request->validate([
            'name' => 'required|string|max:100', 'email' => 'required|email|max:255|unique:users,email,'.$member->id,
            'phone' => 'required|string|regex:/^\+?[0-9]{6,20}$/',
            'cellphone' => 'required|string|regex:/^\+?[0-9]{6,20}$/',
        ]);
        DB::transaction(function () use ($member, $contact) {
            $member->name = $contact['name'];
            $member->email = $contact['email'];
            $member->save();
            $personal = $member->user_memberpersonal;
            abort_unless($personal, 422);
            $personal->phone = $contact['phone'];
            $personal->cellphone = $contact['cellphone'];
            $personal->save();
        });
        $pendingKey = DB::table('ezypay_membership_purchases')
            ->where('renewal_user_id', $member->id)->whereNull('member_payment_id')
            ->orderByDesc('id')->value('renewal_key');
        $key = $pendingKey ?: session('ezypay_renewal_keys.'.$member->id);
        $paidAttempt = $key && DB::table('ezypay_membership_purchases as purchase')
            ->join('member_payments as payment', 'payment.id', '=', 'purchase.member_payment_id')
            ->where('purchase.renewal_key', $key)->where('purchase.installments_completed', true)
            ->whereIn('payment.payment_status', ['Paid', 'Halfpaid'])->exists();
        if (!$key || $paidAttempt) {
            $key = (string) \Illuminate\Support\Str::uuid();
        }
        session(['ezypay_renewal_keys.'.$member->id => $key]);
        try {
            $this->ensureInstallmentEzypayCustomer($member);
            $planResponse = $this->ensureEzypayMembershipPlan($request, $apiClient);
            if ($planResponse->getStatusCode() !== 200) return $planResponse;
            $planId = $planResponse->getData(true)['plan_id_ezypay'];
            $plan = $apiClient->get('/mobile-curves/ezypay/plans/' . rawurlencode($planId))->throw()->json('response');
            if (!is_array($plan) || ($plan['id'] ?? null) !== $planId || ($plan['status'] ?? null) !== 'ACTIVE') {
                throw new \UnexpectedValueException('Invalid EZYPAY renewal plan.');
            }
            $warning = null;
            try {
                $options = $this->loadEzypayPaymentOptions($member->refresh()->customer_id_ezypay, $apiClient);
            } catch (\Throwable $e) {
                $options = [];
                $warning = 'Gagal memuat akun pembayaran. Pembayaran Mandiri tetap tersedia.';
            }
            return response()->json(['warning' => $warning, 'plan' => $plan, 'methods' => $options, 'renewal_key' => $key])->header('Cache-Control', 'no-store');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to prepare EZYPAY renewal.', [
                'user_id' => $member->id, 'exception_type' => get_class($e),
            ]);
            return response()->json(['message' => 'Gagal menyiapkan customer, plan, atau akun EZYPAY. Periksa kontak anggota dan coba lagi.'], 502);
        }
    }

    public function cashier_renewal(){
        $staffs = User::where('club_id', '=', Auth::user()->club_id)
            ->where('role_id', '<>', '7')
            ->where(function ($q) {
                $q->whereDoesntHave('user_staffinactive')
                    ->orWhere(function ($r) {
                        $r->whereHas('user_staffinactive', function ($s) {
                            $s->where('status', '=', 'Reactivated')
                                ->where('thru_date', null);
                        });
                    });
            })
            ->orderBy('name', 'asc')
            ->pluck('name', 'id');

        $members = User::select('id','name','thru_date')->where('club_id','=',Auth::user()->club_id)
            ->where('role_id','=','7')->join('member_status','users.id','=','member_status.user_id')->orderBy('name','ASC')->get();

        $sources = MemberSource::orderBy('source', 'asc')->pluck('source', 'id');

        $payment_methods = PaymentMethod::availableForPayment()->pluck('payment_method', 'id');

        $membership_types = ClubServiceFee::selectRaw('membership_types.id, membership_types.membership_type_name, membership_types.duration,
            club_service_fees.sf_amount, club_service_fees.mf_amount, club_service_fees.pre_pay_discount_value')
            ->join('membership_types', 'membership_types.id', '=', 'club_service_fees.membership_type_id')
            ->where('club_service_fees.club_id', '=', Auth::user()->club_id)
            ->whereDate('club_service_fees.from_date', '<=', Carbon::now()->toDateString())
            ->where('club_service_fees.thru_date', null)
            ->where('membership_types.membership_type', '<>', 'Staff')
            ->where('membership_types.status', '=', 'Active')
            ->groupBy(
                'membership_types.id',
                'membership_types.membership_type_name',
                'membership_types.duration',
                'club_service_fees.sf_amount',
                'club_service_fees.mf_amount',
                'club_service_fees.pre_pay_discount_value'
            )
            ->orderBy('membership_types.membership_type_name', 'asc')
            ->get();

        $banks = Bank::where('id', '<>', 1)
            ->whereHas('bank_club', function ($q){
                $q->where('club_id', '=', Auth::user()->club_id);
            })->get();

        $wcpScript = WebClientPrint::createScript(action('WebClientPrintController@processRequest'), action('PrintESCPOSController@cashierRenewal'), Session::getId());

        //ACCURATE
        /* $accurate = AccurateAuth::find(1);
        $redirectURI = env('ACCURATE_REDIRECT_URI_RENEWAL');
        //$databaseID = Auth::user() ? Accurate::accurateClub(Auth::user()->club_id) : env('ACCURATE_DATABASE_ID');

        $accurateClub = Auth::user() ? Accurate::accurateClub(Auth::user()->club_id) : '';

        if($accurateClub){
            $databaseID = $accurateClub->accurate_id;

            $openDB = Accurate::AccurateOpenDB($databaseID, $accurate->access_token );

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

        return view('cf_lite.cashiers.renewal-cashier', compact('staffs', 'sources', 'payment_methods', 'membership_types', 'banks', 'wcpScript','members'));
    }

    public function store_renewal_cashier(){

        //$accurate = AccurateAuth::find(1);

        //$databaseID = Auth::user() ? Accurate::accurateClub(Auth::user()->club_id) : env('ACCURATE_DATABASE_ID');
        //$openDB = Accurate::AccurateOpenDB($databaseID, $accurate->access_token );
        //$branchID = Accurate::accurateBranch(Auth::user()->club_id);

        /* $accurateClub = Auth::user() ? Accurate::accurateClub(Auth::user()->club_id) : '';
        $openDB = '';
        $branchID = '';
        $databaseID = '';

        if($accurateClub){
            $databaseID = $accurateClub->accurate_id;
            $openDB = Accurate::AccurateOpenDB($databaseID, $accurate->access_token );
            $branchID = $accurateClub->accurate_branch;
        } */

        // $user = User::findOrFail(session('continue_member'));

        //Update Auto Number
        $temp_auto_number = AutoNumber::findOrFail('1');
        $payment_number = $temp_auto_number->current_number;

        $temp_auto_number->current_number = $temp_auto_number->current_number + 1;
        $temp_auto_number->save();


        //Adding Member Payment
        $member_payment = new MemberPayment;
        $member_payment->payment_number = sprintf('%08d', $payment_number).$request->payment_number;
        $member_payment->user_id = $user->id;
        $member_payment->sf_amount = $request->sf_amount;
        $member_payment->mf_amount = $request->mf_amount;
        $member_payment->dc_amount = $request->discount;
        $member_payment->tax_type_id = '1';
        $member_payment->tax_amount = round($request->total / 11);
        $member_payment->tax_percentage = 10;

        $member_payment->amount_due = $request->total;
        $member_payment->payment_category = 'First';

        if (strtotime($request->pay_date) == strtotime(Carbon::today())):
            $member_payment->payment_date = Carbon::createFromFormat('Y-m-d H:i', Carbon::now()->format('Y-m-d H:i'), session('timezone'));
        else:
            $member_payment->payment_date = $request->pay_date;
        endif;

        $member_payment->payment_method_id = $request->payment_method;
        $member_payment->amount_paid = $request->total_pay;

        if (($request->total_bill - $request->total_pay) > 0):
            if ($request->rest_of_bill == ($request->total_bill - $request->total_pay)):
                $member_payment->rest_of_bill = $request->rest_of_bill;
            else:
                $member_payment->rest_of_bill = ($request->total_bill - $request->total_pay);
            endif;
            $member_payment->payment_status = 'Halfpaid';
        elseif (($request->total_bill - $request->total_pay) == 0):
            $member_payment->payment_status = 'Paid';
        endif;
        $member_payment->save();


        //Adding Member Payment Voucher if using Voucher
        if (!empty($request->voucher_code) && !empty($request->voucher_value)):
            $check_voucher = Voucher::where('identity_number', '=', $request->voucher_code)
                ->whereDate('from_date', '<=', Carbon::now()->toDateString())
                ->whereDate('thru_date', '>=', Carbon::now()->toDateString())
                ->where('status', '=', 'Out')
                ->first();

            if (count($check_voucher) > 0):
                $member_payment_voucher = new MemberPaymentVoucher;
                $member_payment_voucher->voucher_code = $request->voucher_code;

                if ($check_voucher->type_id != '2'):
                    if ($check_voucher->voucher_base == 'value'):
                        if (($request->sf_amount + $request->mf_amount - $request->discount) < $check_voucher->value):
                            $member_payment_voucher->voucher_value = $request->sf_amount + $request->mf_amount - $request->discount;
                        else:
                            $member_payment_voucher->voucher_value = $check_voucher->value;
                        endif;
                    elseif ($check_voucher->voucher_base == 'percent'):
                        $actual_voucher = ($request->sf_amount + $request->mf_amount - $request->discount) * $check_voucher->percent * 0.01;
                        if ($actual_voucher > $check_voucher->value):
                            $actual_voucher = $check_voucher->value;
                        endif;

                        if (($request->sf_amount + $request->mf_amount - $request->discount) < $actual_voucher):
                            $member_payment_voucher->voucher_value = $request->sf_amount + $request->mf_amount - $request->discount;
                        else:
                            $member_payment_voucher->voucher_value = $actual_voucher;
                        endif;
                    endif;
                endif;
                $member_payment->memberpayment_memberpaymentvoucher()->save($member_payment_voucher);

                $voucher = Voucher::findOrFail($check_voucher->id);
                $voucher->status = 'Used';
                $voucher->save();
            endif;
        endif;


        //Check if this member has Pending Continued Membership
        $check_continue = ContinueMembership::where('user_id', '=', $user->id)
            ->whereDate('from_date', '>=', Carbon::parse($user->user_memberstatus->thru_date))
            ->get();

        $start_from_date = Carbon::parse($user->user_memberstatus->thru_date)->addDay();

        if (count($check_continue) > 0):
            foreach ($check_continue as $the_date):
                if (strtotime($the_date->thru_date) > strtotime($start_from_date)):
                    $start_from_date = Carbon::parse($the_date->thru_date)->addDay();
                endif;
            endforeach;
        endif;


        //If Reactivation added 05042018
        if (!empty(session('reactivation'))):
            if (session('reactivation') == $user->id):
                $start_from_date = Carbon::parse($request->pay_date);
            endif;
        endif;


        //Adding ContinueMembership (continue_memberships table)
        $membership_type = MembershipType::findOrFail($id);

        $continue = new ContinueMembership;
        $continue->user_id = $user->id;
        $continue->membership_type_id = $membership_type->id;

        if ($membership_type->is_vip == 'general'):
            $the_duration = $membership_type->duration;
        elseif ($membership_type->is_vip == 'vip'):
            $the_duration = 0;
        endif;

        //Cek Jika Perpanjangan dilakukan paling lambat 2 minggu sebelum keanggotaan berakhir Added 16022019
        if (($membership_type->duration == '12') || ($membership_type->duration == '18') || ($membership_type->duration == '24')):
            $payment_diff_days = Carbon::parse($request->pay_date)->diffInDays(Carbon::parse($user->user_memberstatus->thru_date));

            if ($payment_diff_days >= 14):
                if ($membership_type->is_vip == 'general'):
                    $the_duration += 1;
                endif;
            endif;
        endif;
        //End of Added 16022019

        $continue->from_date = Carbon::createFromFormat('Y-m-d H:i', Carbon::parse($start_from_date)->format('Y-m-d H:i'), session('timezone'));
        $thru_date_temp = date('Y-m-d', strtotime("+".$the_duration." months", strtotime($continue->from_date)));

        //VIP Member added 02032018
        if (!empty($request->use_vip) && !empty($request->vip_duration)):
            if ($request->vip_option == 'day'):
                $vip_option = 'days';
            elseif ($request->vip_option == 'month'):
                $vip_option = 'months';
            elseif ($request->vip_option == 'year'):
                $vip_option = 'years';
            endif;

            $continue->thru_date = date('Y-m-d', strtotime("+".$request->vip_duration." ".$vip_option, strtotime($thru_date_temp)));
            $continue->member_vip_status = 'VIP';
        else:
            $continue->thru_date = $thru_date_temp;
        endif;

        $continue->status = 'Pending';
        $continue->staff_id = $request->fa_coach;

        if (($membership_type->payment_type == 'Prepaid') || ($membership_type->payment_type == 'Cash')):
            if ($request->rest_of_bill == 0):
                $continue->payment_status = 'Set';
            endif;
        endif;

        $user->user_continuemembership()->save($continue);


        //Adding LogMemberStatus (Log for Member's Membership History)
        $log_member = new LogMemberStatus;
        $log_member->user_id = $user->id;
        $log_member->membership_type_id = $membership_type->id;
        $log_member->from_date = $continue->from_date;
        $log_member->thru_date = $continue->thru_date;
        $log_member->save();

        // Jonviter DEV
        $member_payment->payment_date = $member_payment->payment_date->toDateTimeString(); 
        $paymentData = $member_payment->toArray();
        $apiClient = new ApiClientService();
        $mobile_curves_api = $apiClient->post("/mobile-curves/payment/{$request->fa_coach}", $paymentData);
        // Jonviter DEV


        

        /* $userData = [
            'name' => $user->name,
            'membership_type' => $membership_type->id,
            'fa_date' => $continue->from_date,
            'pay' => $request->total_pay,
        ];
        //var_dump($userData);

        if($accurateClub){
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
            $accurateItem = Accurate::AccurateCheckItem($openDB, $userData);
            if($accurateItem){
                //do nothing
            }else{
                $accurateItem = Accurate::AccurateCreateItem($openDB, $userData);
            }

            $invoiceData = [
                "customerNo" => $accurateUser['customerNo'],
                "transDate" => date("d/m/Y"),
                "branchId" => $branchID,
                "detailItem" => array(
                    array(
                        "itemNo" => $accurateItem['no'],
                        "quantity" => 1,
                        "unitPrice" => $request->total_pay
                    )
                )
            ];

            //CREATE SALES INVOICE ONE CUSTOMER & ITEM IS TRUE
            $accurateInvoice = Accurate::AccurateCreateInvoice($openDB, $invoiceData);

            if($accurateInvoice){
                return redirect('cf-lite/payment/invoice/'.$member_payment->id.'/member');
            }else{
                //return 'Pembayaran berhasil. Tidak dapat membuat faktur di Accurate';
                return redirect('cf-lite/payment/invoice/'.$member_payment->id.'/member');
            }
        } */
        return redirect('cf-lite/payment/invoice/'.$member_payment->id.'/member');
    }

    public function cashier_promo() {
        $staffs = User::where('club_id', '=', Auth::user()->club_id)
            ->where('role_id', '<>', '7')
            ->where(function ($q) {
                $q->whereDoesntHave('user_staffinactive')
                    ->orWhere(function ($r) {
                        $r->whereHas('user_staffinactive', function ($s) {
                            $s->where('status', '=', 'Reactivated')
                                ->where('thru_date', null);
                        });
                    });
            })
            ->orderBy('name', 'asc')
            ->pluck('name', 'id');

        $brls = BuddyReferralLead::where('status_gm', '=', 'Guest')
            ->where('club_id', '=', Auth::user()->club_id)
            ->whereDoesntHave('guest_gmmember')
            ->orderBy('fullname', 'asc')
            ->get();

        $sources = MemberSource::orderBy('source', 'asc')->pluck('source', 'id');

        $payment_methods = PaymentMethod::availableForPayment()->pluck('payment_method', 'id');

        $promos = GuestMembership::whereDate('thru_date', '>=', Carbon::today()->toDateString())
//            ->whereDate('from_date', '<=', Carbon::today()->toDateString())
            ->whereHas('gm_clubgm', function ($q) {
                $q->where('club_id', '=', Auth::user()->club_id);
            })
            ->orderBy('from_date', 'asc')
            ->get();

        $banks = Bank::where('id', '<>', 1)
            ->whereHas('bank_club', function ($q){
                $q->where('club_id', '=', Auth::user()->club_id);
            })->get();

        $wcpScript = WebClientPrint::createScript(action('WebClientPrintController@processRequest'), action('PrintESCPOSController@promoMembership'), Session::getId());
        //print_r($promos);

        //ACCURATE
        /* $accurate = AccurateAuth::find(1);
        $redirectURI = env('ACCURATE_REDIRECT_URI_PROMO');

        $accurateClub = Auth::user() ? Accurate::accurateClub(Auth::user()->club_id) : '';
        $accurateItems = [];

        if($accurateClub){
            $databaseID = $accurateClub->accurate_id;
            //$databaseID = Auth::user() ? Accurate::accurateClub(Auth::user()->club_id) : env('ACCURATE_DATABASE_ID');
            $openDB = Accurate::AccurateOpenDB($databaseID, $accurate->access_token );

            $code = '';

            if(isset($_GET['code'])){
                $code = $_GET["code"];
            }

            if($openDB){
                //echo 'DB opened..';
                //$accurateItems = Accurate::accurateCheckItems($openDB, 'SERVICE',1 ,100);

                $x = 1;

                while($x <= 100) {
                    $_accurateItems = Accurate::accurateCheckItems($openDB, 'SERVICE', $x, 100);

                    if($_accurateItems){
                            //do nothing
                            $accurateItems = array_replace_recursive($accurateItems, $_accurateItems);
                    }else{
                            break;
                    }
                    $x++;
                }

            }else{
                Accurate::oauthAOL($code, $accurate, $databaseID, $redirectURI);
            }
        }

        return view('cf_lite.cashiers.promo-cashier', compact('staffs', 'sources', 'payment_methods', 'promos', 'banks', 'brls', 'wcpScript', 'accurateItems')); */

        return view('cf_lite.cashiers.promo-cashier', compact('staffs', 'sources', 'payment_methods', 'promos', 'banks', 'brls', 'wcpScript'));
    }

    public function cashier_promo_convert() {
        $staffs = User::where('club_id', '=', Auth::user()->club_id)
            ->where('role_id', '<>', '7')
            ->where(function ($q) {
                $q->whereDoesntHave('user_staffinactive')
                    ->orWhere(function ($r) {
                        $r->whereHas('user_staffinactive', function ($s) {
                            $s->where('status', '=', 'Reactivated')
                                ->where('thru_date', null);
                        });
                    });
            })
            ->orderBy('name', 'asc')
            ->pluck('name', 'id');

        $brls = BuddyReferralLead::where('status_gm', '=', 'Guest')->where('club_id', '=', Auth::user()->club_id)
            ->whereHas('guest_gmmember', function ($q) {
                $q->whereDate('thru_date', '>=', Carbon::today()->toDateString())
                    ->orWhere(function ($r) {
                        $r->whereDate('thru_date', '<=', Carbon::today()->addMonth()->toDateString());
                    });
            })
            ->whereDoesntHave('buddyreferrallead_memberpersonal')
            ->orderBy('fullname', 'asc')
            ->get();

        $membership_types = ClubServiceFee::selectRaw('membership_types.id, membership_types.membership_type_name, membership_types.duration,
            club_service_fees.sf_amount, club_service_fees.mf_amount, club_service_fees.pre_pay_discount_value')
            ->join('membership_types', 'membership_types.id', '=', 'club_service_fees.membership_type_id')
            ->where('club_service_fees.club_id', '=', Auth::user()->club_id)
            ->whereDate('club_service_fees.from_date', '<=', Carbon::now()->toDateString())
            ->where('club_service_fees.thru_date', null)
            ->where('membership_types.membership_type', '<>', 'Staff')
            ->where('membership_types.status', '=', 'Active')
            ->groupBy('membership_types.id', 'club_service_fees.sf_amount', 'club_service_fees.mf_amount', 'club_service_fees.pre_pay_discount_value')
            ->orderBy('membership_types.membership_type_name', 'asc')
            ->get();

        $payment_methods = PaymentMethod::availableForPayment()->pluck('payment_method', 'id');


        $banks = Bank::where('id', '<>', 1)
            ->whereHas('bank_club', function ($q){
                $q->where('club_id', '=', Auth::user()->club_id);
            })->get();

        $wcpScript = WebClientPrint::createScript(action('WebClientPrintController@processRequest'), action('PrintESCPOSController@promoConvertMembership'), Session::getId());

        //ACCURATE
        /* $accurate = AccurateAuth::find(1);
        $redirectURI = env('ACCURATE_REDIRECT_URI_PROMO_CONVERT');

        $accurateClub = Auth::user() ? Accurate::accurateClub(Auth::user()->club_id) : '';

        if($accurateClub){
            $databaseID = $accurateClub->accurate_id;
            //$databaseID = Auth::user() ? Accurate::accurateClub(Auth::user()->club_id) : env('ACCURATE_DATABASE_ID');
            $openDB = Accurate::AccurateOpenDB($databaseID, $accurate->access_token );

            $code = '';
            if(isset($_GET['code'])){
                $code = $_GET["code"];
            }
            if($openDB){
                //echo 'DB opened..';
            }else{
                Accurate::oauthAOL($code, $accurate, $databaseID, $redirectURI );
            }
        } */

        return view('cf_lite.cashiers.promo-convert-cashier', compact('staffs', 'brls', 'membership_types', 'payment_methods', 'banks', 'wcpScript'));
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
        }

        if($accurateClub){
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
            $accurateItem = Accurate::AccurateCheckItem($openDB, $userData, 'SERVICE');
            if($accurateItem){
                //do nothing
            }else{
                $accurateItem = Accurate::AccurateCreateItem($openDB, $userData, 'SERVICE');
            }

            $invoiceData = [
                "customerNo" => $accurateUser['customerNo'],
                "transDate" => date("d/m/Y"),
                "branchId" => $branchID,
                "detailItem" => array(
                    array(
                        "itemNo" => $accurateItem['no'],
                        "quantity" => 1,
                        "unitPrice" => $userData['pay']
                        )
                    )
            ];

            //CREATE SALES INVOICE ONE CUSTOMER & ITEM IS TRUE
            $accurateInvoice = Accurate::AccurateCreateInvoice($openDB, $invoiceData);
            Storage::put('invoices/'.date("d-m-Y").'_'.$accurateUser['customerNo'].'_invoice.txt', json_encode($accurateInvoice));
        }
        /* if($accurateInvoice){
            return 'berhasil';
        }else{
            return 'Pembayaran berhasil. Tidak dapat membuat faktur di Accurate';
        } */
    }

    public function store_accurate_promo($club_id, $userData)
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
        }

        if($accurateClub){

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
            /*$accurateItem = Accurate::AccurateCheckItem($openDB, $userData, 'PROMO');
            if($accurateItem){
                //do nothing
            }else{
                $accurateItem = Accurate::AccurateCreateItem($openDB, $userData, 'SERVICE');
            } */
            $accurateItem = $userData['membership_type'];

            $invoiceData = [
                "customerNo" => $accurateUser['customerNo'],
                "transDate" => date("d/m/Y"),
                "branchId" => $branchID,
                "detailItem" => array(
                    array(
                        "itemNo" => $accurateItem,
                        "quantity" => 1,
                        "unitPrice" => $userData['pay']
                        )
                    )
            ];

            //CREATE SALES INVOICE ONE CUSTOMER & ITEM IS TRUE
            $accurateInvoice = Accurate::AccurateCreateInvoice($openDB, $invoiceData);
            Storage::put('invoices/'.date("d-m-Y").'_'.$accurateUser['customerNo'].'_invoice.txt', json_encode($accurateInvoice));
        }
        /* if($accurateInvoice){
            return 'berhasil';
        }else{
            return 'Pembayaran berhasil. Tidak dapat membuat faktur di Accurate';
        } */
    }


    public function store_accurate_installment($club_id, $userData)
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
        }

        if($accurateClub){
            //CHECK IF USER EXIST IN ACCURATE
            //IF USER DO NOT EXIST, CREATE USING THE API
            $accurateUser = Accurate::AccurateCheckMember($openDB, $userData);
            if($accurateUser){
                //do nothing
            }else{
                $accurateUser = Accurate::AccurateCreateMember($openDB, $userData);
            }

            $accurateItem = $userData['accurate_code'];

            $invoiceData = [
                "customerNo" => $accurateUser['customerNo'],
                "transDate" => date("d/m/Y"),
                "branchId" => $branchID,
                "detailItem" => array(
                    array(
                        "itemNo" => $accurateItem,
                        "quantity" => 1,
                        "unitPrice" => $userData['pay']
                        )
                    )
            ];

            //CREATE SALES INVOICE ONE CUSTOMER & ITEM IS TRUE
            $accurateInvoice = Accurate::AccurateCreateInvoice($openDB, $invoiceData);
            Storage::put('invoices/installment/'.date("d-m-Y").'_'.$accurateUser['customerNo'].'_installment.txt', json_encode($accurateInvoice));
        }
    }


    //AJAX
    public function ajax_get_tax_config_detail(Request $request) {
        $tax_config = TaxConfig::where('club_id', '=', $request->club_id)
            ->where('tax_type_id', '=', $request->tax_type_id)
            ->where('thru_date', null)
            ->first();

        return response()->json(array('tax_config' => $tax_config));
    }

    public function getUserAjax(Request $request)
    {
        $users = User::select('name','id')->where('name', 'ILIKE', '%' . $request->q . '%')->where('role_id', '<>', '1')->orderBy('name', 'asc')->limit(10)->get();

        return response()->json(['users' => $users]);
    }

    public function getClubAjax(Request $request){
        $clubs = ClubInfo::select('club_name')->where('club_name','ILIKE','%'.$request->q.'%')->where('status', '=', 'Active')->orderBy('club_name')->limit(10)->get();

        return response()->json(['clubs' => $clubs]);
    }
}
