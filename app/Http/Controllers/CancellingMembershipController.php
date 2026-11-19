<?php

namespace App\Http\Controllers;

use App\AutoNumber;
use App\Model\Club\ClubServiceFee;
use App\Model\Log\LogMemberStatus;
use App\Model\MasterData\NoSaleReason;
use App\Model\Member\InactiveMembershipReason;
use App\Model\Member\MemberDeposit;
use App\Model\Staff\TransferHistory;
use Session;
use App\Model\Payment\MemberPayment;
use App\Model\Refund\RefundSubmission;
use App\Model\Workout\Workout;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CancellingMembershipController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('wm-only');
    }

    public function three_days_list(Request $request) {
        $members = User::where('club_id', '=', Auth::user()->club_id)
            ->where('role_id', '=', '7')
            ->whereHas('user_memberpersonal', function ($q) {
                $q->whereDate('joined', '<=', Carbon::now()->toDateString());
//                    ->whereDate('joined', '>=', Carbon::now()->subDays(3)->toDateString());
            })
            ->whereHas('user_memberstatus', function ($r) {
                $r->where('status', '=', 'Active')
                    ->whereDate('from_date', '<=', Carbon::now()->toDateString())
                    ->whereDate('thru_date', '>=', Carbon::now()->toDateString())
                    ->whereDate('from_date', '>', Carbon::now()->subDays(3)->toDateString());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        if (!empty($request->member)):
            $search_members = User::where('club_id', '=', Auth::user()->club_id)
                ->where('role_id', '=', '7')
                ->whereHas('user_memberpersonal', function ($q) {
                    $q->whereDate('joined', '<=', Carbon::now()->toDateString());
                })
                ->whereHas('user_memberstatus', function ($r) {
                    $r->where(function ($s) {
                        $s->where('status', '<>', 'Inactive')
                            ->orWhere('status', '<>', 'Stopped');
                    })
                        ->whereDate('from_date', '<=', Carbon::now()->toDateString())
                        ->whereDate('thru_date', '>=', Carbon::now()->toDateString())
                        ->whereDate('from_date', '>', Carbon::now()->subDays(3)->toDateString());
                })
                ->where('name', 'ILIKE', '%'.strtolower($request->member).'%')
                ->orderBy('created_at', 'asc')
                ->get();

            return view('cf_lite.cancelling-membership.cancelling-membership-main-page', compact('members', 'search_members'));
        else:
            return view('cf_lite.cancelling-membership.cancelling-membership-main-page', compact('members'));
        endif;
    }

    public function cancellation_detail($id) {
        $member = User::findOrFail($id);

        //Cek jumlah uang yang telah dibayarkan
        $payments = MemberPayment::where('user_id', '=', $member->id)
            ->where('payment_status', '<>', 'Unpaid')
            ->whereDate('payment_date', '>=', $member->user_memberpersonal->joined)
            ->get();

        //Cek jika telah FWO
        $workouts = Workout::where('user_id', '=', $member->id)
            ->whereDate('workout_date', '>=', $member->user_memberpersonal->joined)
            ->get();
        $total_workouts = count($workouts);

        //Cek berapa lama member telah bergabung
        $joined = Carbon::parse($member->user_memberpersonal->joined);
        $diff = $joined->diffInDays(Carbon::now());

        return view('cf_lite.cancelling-membership.cancelling-membership-detail', compact('member', 'payments', 'total_workouts', 'diff'));
    }

    public function create_refund($id, Request $request) {
        $this->validate($request, [
            'refund_amount' => 'required|numeric',
            'refund_diff_days' => 'required|numeric',
        ]);

        $member = User::findOrFail($id);

        $refund = new RefundSubmission;
        $refund->user_id = $member->id;
        $refund->refund_amount = $request->refund_amount;
        $refund->refund_diff_days = $request->refund_diff_days;
        $refund->refund_submission_date = Carbon::now();
        $refund->refund_status = 'Pending';
        $refund->submit_by = Auth::id();
        $refund->save();

        Session::flash('flash_message', trans('messages.refund_submission_has_been_made'));

        return redirect('cf-lite/refund/'.$refund->id.'/detail');
    }

    //Cancellation Membership After More Than 3 Days Join
    public function cancellation_request(Request $request) {
        $club = Auth::user()->user_club;
        if (!empty($request->s) && !empty($request->search_option)):
            $search = $request->s;
            $query = User::where('club_id', '=', Auth::user()->club_id)
                ->whereHas('user_memberstatus', function ($q) {
                    $q->where('status', '=', 'Active')
                        ->orWhere('status', '=', 'Hold');
                });

            if ($request->search_option == 'name'):
                $query->where('name', 'ILIKE', '%'.$search.'%');
            elseif ($request->search_option == 'keytag'):
                $query->whereHas('user_memberpersonal', function ($q) use ($search) {
                    $q->where('key_tag_id', 'ILIKE', '%'.$search.'%');
                });
            endif;

            $members = $query->orderBy('name', 'asc')->paginate(100);

            return view('cf_lite.cancelling-membership.cancelling-membership-request', compact('members', 'club'));
        else:
            $members = User::where('club_id', '=', Auth::user()->club_id)
                ->where('role_id', '=', '7')
                ->whereHas('user_memberstatus', function ($q) {
                    $q->whereDate('from_date', '>=', Carbon::today()->subDays(3)->toDateString())
                        ->where('status', '=', 'Active');
                })
                ->orderBy('name', 'asc')
                ->paginate(100);

            return view('cf_lite.cancelling-membership.cancelling-membership-request', compact('members', 'club'));
        endif;
    }

    public function cancellation_request_detail($id) {
        $member = User::findOrFail($id);
        $open_gate = 0;
        $set_installment = 0;

        if (strtotime($member->user_memberstatus->from_date) >= strtotime(Carbon::now()->subMonth())):
//            if (strtotime($member->user_memberstatus->from_date) < strtotime(Carbon::today()->subDays(3))):
//                $open_gate = 1;
//            else:
//                return redirect('cf-lite/cancelling-membership/'.$member->id.'/detail');
//            endif;
            $open_gate = 1;
        else:
            if ($member->user_memberstatus->payment_status == 'Set'):
                $open_gate = 1;
            endif;
        endif;

        if ($open_gate == 1):
            //Cek tanggal awal pembayaran keanggotaan, diambil dari log membership terakhir
            $payment_starting_date = LogMemberStatus::where('user_id', '=', $member->id)
                ->where('membership_type_id', '=', $member->user_memberstatus->membership_type_id)
                ->whereDate('from_date', '>=', Carbon::parse($member->user_memberstatus->from_date))
                ->whereDate('thru_date', '=', Carbon::parse($member->user_memberstatus->thru_date))
                ->orderBy('created_at', 'desc')
                ->first();

            //Added 18102018
            if (empty($payment_starting_date)):
                $payment_starting_date = LogMemberStatus::where('user_id', '=', $member->id)
                    ->where('membership_type_id', '=', $member->user_memberstatus->membership_type_id)
                    ->whereDate('thru_date', '=', Carbon::parse($member->user_memberstatus->thru_date))
                    ->orderBy('created_at', 'desc')
                    ->first();

                //Added 31102018
                if (empty($payment_starting_date)):
                    $payment_starting_date = LogMemberStatus::where('user_id', '=', $member->id)
                        ->where('membership_type_id', '=', $member->user_memberstatus->membership_type_id)
                        ->whereDate('thru_date', '<=', Carbon::parse($member->user_memberstatus->thru_date))
                        ->orderBy('created_at', 'desc')
                        ->first();
                endif;
            endif;

            //Cek lama keanggotaan sejak join
            $joined_date = Carbon::parse($member->user_memberstatus->from_date);
            $day_diff = Carbon::now()->diffInDays($joined_date);
            $month_diff = ceil($day_diff / 30);
//            if ((strtotime($member->user_memberstatus->from_date) >= strtotime(Carbon::now()->subMonth())) && (strtotime($member->user_memberstatus->from_date) < strtotime(Carbon::now()->subDays(3)))):
//                $month_diff = 1;
//            else:
//                $month_diff = ceil($day_diff / 30);
//            endif;

            //First Payment
            $first_payment = MemberPayment::where('user_id', '=', $member->id)
                ->where('payment_category', '=', 'First')
                ->whereDate('created_at', '>=', Carbon::parse($payment_starting_date->created_at)->toDateString())
                ->orderBy('created_at', 'desc')
                ->first();

            //Cek jika ada transfer (Added 12022019)
            $transfer = TransferHistory::where('user_id', '=', $member->id)
                ->whereDate('from_date', '>=', Carbon::parse($member->user_memberstatus->from_date)->toDateString())
                ->first();

            if (!empty($transfer)):
                $original_club = $transfer->club_id;
            else:
                $original_club = $member->club_id;
            endif;
            //Cek jika ada transfer (Added 12022019)

            //Cek Tarif Keanggotaan MM dari CSF
            $mm_price = ClubServiceFee::where('club_id', '=', $original_club)
                ->where('membership_type_id', '=', '1')
                ->whereDate('from_date', '<=', Carbon::parse($payment_starting_date->from_date)->toDateString())
                ->orderBy('from_date', 'asc')
                ->first();

            //Monthly Payment
            //AD
            //dd($member->user_memberstatus->memberstatus_membershiptype->payment_type);
            //die();
            $monthly_payments = '';
            if ($member->user_memberstatus->memberstatus_membershiptype->payment_type == 'AD'):
//                if ($month_diff == 1):
//                    $monthly_payments = MemberPayment::where('user_id', '=', $member->id)
//                        ->where('payment_category', '=', 'Monthly')
//                        ->whereDate('created_at', '>=', Carbon::parse($payment_starting_date->created_at)->toDateString())
//                        ->orderBy('created_at', 'asc')
//                        ->first();
//                elseif ($month_diff > 1):
//                    $monthly_payments = MemberPayment::where('user_id', '=', $member->id)
//                        ->where('payment_category', '=', 'Monthly')
//                        ->whereDate('created_at', '>=', Carbon::parse($payment_starting_date->created_at)->toDateString())
//                        ->orderBy('created_at', 'asc')
//                        ->get();
//                endif;
                $monthly_payments = MemberPayment::where('user_id', '=', $member->id)
                    ->where('payment_category', '=', 'Monthly')
                    ->whereDate('created_at', '>=', Carbon::parse($payment_starting_date->created_at)->toDateString())
                    ->orderBy('created_at', 'asc')
                    ->get();
            //Prepaid
            elseif ($member->user_memberstatus->memberstatus_membershiptype->payment_type == 'Prepaid'):
                $monthly_payments = MemberPayment::where('user_id', '=', $member->id)
                    ->where('payment_category', '=', 'Monthly')
                    ->where('payment_status', '<>', 'Unpaid')
                    ->whereDate('created_at', '>=', Carbon::parse($payment_starting_date->created_at)->toDateString())
                    ->orderBy('created_at', 'asc')
                    ->get();
            endif;

            //FA Coaches
            $fa_coaches = User::where('club_id', '=', Auth::user()->club_id)
                ->where('role_id', '<>', '7')
                ->orderBy('name', 'asc')
                ->pluck('name', 'id');

            //Refund Autonumber
            $temp_auto_number = AutoNumber::findOrFail('3');

            //Jika keanggotaan < 3 hari, cek sudah WO atau belum
            if ($day_diff <= 3):
                //Cek jika telah FWO
                $workouts = Workout::where('user_id', '=', $member->id)
                    ->whereDate('workout_date', '>=', Carbon::parse($member->user_memberstatus->from_date)->toDateString())
                    ->get()
                    ->count();
                $total_workouts = $workouts;

                return view('cf_lite.cancelling-membership.cancelling-membership-payment-detail', compact('member', 'first_payment', 'monthly_payments', 'month_diff', 'mm_price', 'day_diff', 'fa_coaches', 'temp_auto_number', 'total_workouts'));
            else:
                return view('cf_lite.cancelling-membership.cancelling-membership-payment-detail', compact('member', 'first_payment', 'monthly_payments', 'month_diff', 'mm_price', 'day_diff', 'fa_coaches', 'temp_auto_number'));
            endif;
        elseif ($open_gate == 0):
            $set_installment = 1;
        endif;

        if ($set_installment == 1):
            Session::flash('flash_message', trans('messages.please_set_installment_first'));
            return redirect('cf-lite/cancelling-membership/request?search_option=name&s='.$member->name);
        endif;
    }

    public function create_cancellation_submission($id, Request $request) {
        $this->validate($request, [
            'request_date' => 'required|date',
            'refund_amount' => 'required|numeric',
            'refund_diff_days' => 'required|numeric',
            'fa_coach' => 'required',
        ]);

        $member = User::findOrFail($id);
        $gotRenewal = User::where('id','=',$member->id)->whereHas('user_continuemembership',function($q){
            $q->where('status','=','Pending');
        })->count();

        //Added 28/07/2019 Check Continue Membership
        if($gotRenewal == 0){
            //Added 06042018 Checking membership status
            if (($member->user_memberstatus->status == 'Active') || ($member->user_memberstatus->status == 'Hold')):
                $refund_amount = $request->refund_amount;
                $refund_diff_days = $request->refund_diff_days;

                //Cek jika tanggal request lebih awal
                if (strtotime(Carbon::parse($request->request_date)) != strtotime(Carbon::today())):
                    //Cek tanggal awal pembayaran keanggotaan, diambil dari log membership terakhir
                    $payment_starting_date = LogMemberStatus::where('user_id', '=', $member->id)
                        ->where('membership_type_id', '=', $member->user_memberstatus->membership_type_id)
                        ->whereDate('from_date', '>=', Carbon::parse($member->user_memberstatus->from_date))
                        ->whereDate('thru_date', '=', Carbon::parse($member->user_memberstatus->thru_date))
                        ->orderBy('created_at', 'desc')
                        ->first();

                    //Added 01112018
                    if (empty($payment_starting_date)):
                        $payment_starting_date = LogMemberStatus::where('user_id', '=', $member->id)
                            ->where('membership_type_id', '=', $member->user_memberstatus->membership_type_id)
                            ->whereDate('thru_date', '=', Carbon::parse($member->user_memberstatus->thru_date))
                            ->orderBy('created_at', 'desc')
                            ->first();

                        if (empty($payment_starting_date)):
                            $payment_starting_date = LogMemberStatus::where('user_id', '=', $member->id)
                                ->where('membership_type_id', '=', $member->user_memberstatus->membership_type_id)
                                ->whereDate('thru_date', '<=', Carbon::parse($member->user_memberstatus->thru_date))
                                ->orderBy('created_at', 'desc')
                                ->first();
                        endif;
                    endif;

                    //Cek lama keanggotaan sejak join
                    $joined_date = Carbon::parse($member->user_memberstatus->from_date);
                    $day_diff = Carbon::parse($request->request_date)->diffInDays($joined_date);
                    $month_diff = ceil($day_diff / 30);
                    $refund_diff_days = $day_diff;

                    //First Payment
                    $first_payment = MemberPayment::where('user_id', '=', $member->id)
                        ->where('payment_category', '=', 'First')
                        ->whereDate('created_at', '>=', Carbon::parse($payment_starting_date->created_at)->toDateString())
                        ->orderBy('created_at', 'desc')
                        ->first();

                    //Cek jika ada transfer (Added 12022019)
                    $transfer = TransferHistory::where('user_id', '=', $member->id)
                        ->whereDate('from_date', '>=', Carbon::parse($member->user_memberstatus->from_date)->toDateString())
                        ->first();

                    if (!empty($transfer)):
                        $original_club = $transfer->club_id;
                    else:
                        $original_club = $member->club_id;
                    endif;
                    //Cek jika ada transfer (Added 12022019)

                    //Cek Tarif Keanggotaan MM dari CSF
                    $mm_price = ClubServiceFee::where('club_id', '=', $original_club)
                        ->where('membership_type_id', '=', '1')
                        ->whereDate('from_date', '<=', Carbon::parse($payment_starting_date->from_date)->toDateString())
                        ->orderBy('from_date', 'asc')
                        ->first();

                    //Set refund amount atas First Payment
                    if ($refund_diff_days > 3):
                        $refund_amount = ($mm_price->mf_amount - ($first_payment->amount_paid - $first_payment->sf_amount + $first_payment->dc_amount));
                    else:
                        //Cek total Workout
                        $total_workouts = Workout::where('user_id', '=', $member->id)
                            ->whereDate('workout_date', '>=', Carbon::parse($member->user_memberstatus->from_date)->toDateString())
                            ->get()
                            ->count();

                        if ($total_workouts > 0):
                            $refund_amount = (0 - ($first_payment->amount_paid - $first_payment->sf_amount + $first_payment->dc_amount));
                        else:
                            $refund_amount = (0 - ($first_payment->amount_paid));
                        endif;
                    endif;

                    //Monthly Payment
                    //AD
                    if ($member->user_memberstatus->memberstatus_membershiptype->payment_type == 'AD'):
                        $monthly_payments = MemberPayment::where('user_id', '=', $member->id)
                            ->where('payment_category', '=', 'Monthly')
                            ->whereDate('created_at', '>=', Carbon::parse($payment_starting_date->created_at)->toDateString())
                            ->orderBy('created_at', 'asc')
                            ->get();

                        $month_count = 0;
                        foreach ($monthly_payments as $payment):
                            if ($month_count < $month_diff):
                                if ($refund_diff_days > 3):
                                    $refund_amount = $refund_amount + ($mm_price->mf_amount - $payment->amount_paid);
                                else:
                                    if ($payment->payment_status != 'Unpaid'):
                                        $refund_amount = $refund_amount + (0 - $payment->amount_paid);
                                    endif;
                                endif;
                                $month_count++;
                            endif;
                        endforeach;
                    //Prepaid
                    elseif ($member->user_memberstatus->memberstatus_membershiptype->payment_type == 'Prepaid'):
                        $monthly_payments = MemberPayment::where('user_id', '=', $member->id)
                            ->where('payment_category', '=', 'Monthly')
                            ->where('payment_status', '<>', 'Unpaid')
                            ->whereDate('created_at', '>=', Carbon::parse($payment_starting_date->created_at)->toDateString())
                            ->orderBy('created_at', 'asc')
                            ->get();

                        foreach ($monthly_payments as $payment):
                            if ($payment->payment_status != 'Unpaid'):
                                $refund_amount = $refund_amount - $payment->amount_paid;
                            endif;
                        endforeach;

                        for ($i = 0; $i < $month_diff; $i++):
                            if ($refund_diff_days > 3):
                                $refund_amount = $refund_amount + $mm_price->mf_amount;
                            endif;
                        endfor;
                    endif;
                endif;

                $refund_submission = new RefundSubmission;
                $refund_submission->user_id = $member->id;
                $refund_submission->refund_amount = abs($refund_amount);
                $refund_submission->refund_diff_days = $refund_diff_days;
                $refund_submission->refund_submission_date = $request->request_date;
                $refund_submission->refund_status = 'Pending';
                $refund_submission->submit_by = $request->fa_coach;

                if ($request->refund_amount < 0):
                    $billing_type = 'Refund';
                else:
                    $billing_type = 'Bill';
                endif;
                $refund_submission->billing_type = $billing_type;
                $refund_submission->membership_type_id = $member->user_memberstatus->membership_type_id;
                $refund_submission->start_membership_date = $member->user_memberstatus->from_date;
                $refund_submission->end_membership_date = $member->user_memberstatus->thru_date;

                $month_roman = array(1=>'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');
                if ($refund_amount < 0):
                    //Update Auto Number
                    $temp_auto_number = AutoNumber::findOrFail('3');
                    $payment_number = $temp_auto_number->current_number;

                    $temp_auto_number->current_number = $temp_auto_number->current_number + 1;
                    $temp_auto_number->save();

                    $refund_submission->refund_number = sprintf('%08d', $payment_number).'/RF/CIND/'.sprintf('%03d', $member->club_id).'/'.$month_roman[Carbon::now()->format('n')].'/'.Carbon::now()->format('Y');
                else:
                    //Update Auto Number
                    $temp_auto_number = AutoNumber::findOrFail('1');
                    $payment_number = $temp_auto_number->current_number;

                    $temp_auto_number->current_number = $temp_auto_number->current_number + 1;
                    $temp_auto_number->save();

                    $refund_submission->refund_number = sprintf('%08d', $payment_number).'/PY/CIND/'.sprintf('%03d', $member->club_id).'/'.$month_roman[Carbon::now()->format('n')].'/'.Carbon::now()->format('Y');
                endif;

                $refund_submission->save();

                return redirect('cf-lite/refund/'.$refund_submission->id.'/cancellation-detail');
            else:
                Session::flash('flash_message', trans('messages.member_already_cancelled_membership'));
                return redirect('cf-lite/cancelling-membership/request/'.$member->id);
            endif;
        }else{
            Session::flash('flash_message', trans('messages.member_got_continued_membership'));
            return redirect('cf-lite/cancelling-membership/request/'.$member->id);
        }
    }

    //Cancellation List
    public function cancellation_list() {
        $member_deposits = MemberDeposit::where('status', '=', 'Pending')
//            ->whereHas('memberdeposit_user', function ($q) {
//                $q->where('club_id', '=', Auth::user()->club_id);
//            })
            ->orderBy('created_at', 'desc')
            ->get();

        $cancelled_members = User::where('club_id', '=', Auth::user()->club_id)
            ->where('role_id', '=', '7')
            ->whereHas('user_memberstatus', function ($r) {
                $r->where('status', '=', 'Stopped');
            })
            ->where(function($w) {
                $w->whereDoesntHave('user_memberdeposit')
                    ->orWhere(function ($x) {
                        $x->whereHas('user_memberdeposit', function ($y) {
                            $y->where('status', '<>', 'Pending');
                        });
                    });
            })
            ->paginate(100);

        $pending_members = RefundSubmission::where('refund_status', '=', 'Pending')
            ->whereHas('refundsubmission_user', function ($q) {
                $q->where('club_id', '=', Auth::user()->club_id);
            })
            ->orderBy('refund_submission_date', 'desc')
            ->get();

        return view('cf_lite.cancelling-membership.cancelling-membership-list', compact('member_deposits', 'cancelled_members', 'pending_members'));
    }

    //Inactivate Member
    public function inactive_member_list() {
        $inactive_reasons = [];
        $pending_members = User::where('club_id', '=', Auth::user()->club_id)
            ->where('role_id', '=', '7')
            ->whereHas('user_memberstatus', function ($q) {
                $q->where(function ($r) {
                    $r->where('status', '=', 'Active')
                        ->orWhere('status', '=', 'Hold');
                })->whereDate('thru_date', '<', Carbon::now()->toDateString());
            })->get();

        $cancelled_members = User::where('club_id', '=', Auth::user()->club_id)
            ->where('role_id', '=', '7')
            ->whereHas('user_memberstatus', function ($r) {
                $r->where('status', '=', 'Inactive')
                    ->orWhere('status', '=', 'Stopped');
            })
            ->get();

        if (count($cancelled_members) > 0):
            foreach ($cancelled_members as $cancelled_member):
                $inactive_reasons[$cancelled_member->id] = InactiveMembershipReason::where('user_id', '=', $cancelled_member->id)
                    ->orderBy('created_at', 'desc')
                    ->first();
            endforeach;
        endif;

        $cancellation_reasons = NoSaleReason::orderBy('nosale_reason', 'asc')->pluck('nosale_reason', 'id');

        return view('cf_lite.cancelling-membership.inactive-member-list', compact('pending_members', 'cancelled_members', 'cancellation_reasons', 'inactive_reasons'));
    }

    public function inactivate_member(Request $request) {
        $this->validate($request, [
            'user_id' => 'required',
            'cancellation_reason' => 'sometimes',
            'another_cancellation_reason' => 'sometimes',
        ]);

        $user = User::findOrFail($request->user_id);

        $member_status = $user->user_memberstatus;
        $member_status->status = 'Inactive';
        $user->user_memberstatus()->save($member_status);

        $log_member_status = LogMemberStatus::where('user_id', '=', $user->id)
            ->where('membership_type_id', '=', $user->user_memberstatus->membership_type_id)
            ->whereDate('thru_date', '<=', Carbon::now()->toDateString())
            ->orderBy('from_date', 'desc')
            ->first();

        $inactive_reason = new InactiveMembershipReason;
        $inactive_reason->user_id = $user->id;
        $inactive_reason->log_membership_id = $log_member_status->id;

        if (!empty($request->cancellation_reason)):
            $inactive_reason->reason_id = $request->cancellation_reason;
        endif;

        $inactive_reason->another_reason = $request->another_cancellation_reason;
        $inactive_reason->save();

        Session::flash('flash_message', trans('messages.member_has_been_deactivated'));
        return redirect('cf-lite/cancelling-membership/inactive-member-list');
    }
}
