<?php

namespace App\Http\Controllers;

use App\Model\Appointment\GuestAppointment;
use App\Model\Locker\LockerPayment;
use App\Model\Log\LogMemberStatus;
use App\Model\MasterData\MemberSource;
use App\Model\Member\ContinueMembership;
use App\Model\Member\MemberHold;
use App\Model\Payment\MemberDebtPayment;
use App\Model\Payment\MemberPayment;
use App\Model\Product\ProductPaymentDetail;
use App\Model\Product\ProductTypes;
use App\Model\Refund\RefundSubmission;
use App\Model\Workout\Workout;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PJController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('wm-only');
    }

    public function generate_report(Request $request) {
        $this->validate($request, [
            'date' => 'required|date',
        ]);

        $from_date = new Carbon('first day of '.Carbon::parse($request->date)->format('F Y'));
        $thru_date = new Carbon('last day of '.Carbon::parse($request->date)->format('F Y'));
        $diff_days = $thru_date->diffInDays($from_date);

        //Workout
        $wo_daily = [];
        for ($i = 0; $i <= $diff_days; $i++):
            if (Carbon::parse($from_date)->addDays($i) <= Carbon::parse($thru_date)):
                $wo_daily[$i] = Workout::where('club_id', '=', Auth::user()->club_id)
                    ->whereDate('workout_date', '=', Carbon::parse($from_date)->addDays($i)->toDateString())
                    ->whereDate('workout_date', '<=', Carbon::parse($thru_date)->toDateString())
                    ->get()
                    ->count();
            endif;
        endfor;

        //Source
        $sources = MemberSource::orderBy('source', 'asc')->get();
        foreach ($sources as $source):
            $guest[$source->id] = [];
            for ($i = 0; $i <= $diff_days; $i++):
                if (Carbon::parse($from_date)->addDays($i) <= Carbon::parse($thru_date)):
                    $guest[$source->id][$i] = GuestAppointment::where('club_id', '=', Auth::user()->club_id)
                        ->where('guest_status_after', '=', 'Member')
                        ->whereDate('fa_date', '=', Carbon::parse($from_date)->addDays($i)->toDateString())
                        ->whereDate('fa_date', '<=', Carbon::parse($thru_date)->toDateString())
                        ->whereHas('guestappointment_gastatus', function ($q) {
                            $q->where('status_id', '=', '5');
                        })
                        ->whereHas('guestappointment_buddyreferrallead', function ($r) use ($source) {
                            $r->where('source_id', '=', $source->id);
                        })
                        ->get()
                        ->count();
                endif;
            endfor;
        endforeach;

        $tog = [];
        for ($i = 0; $i <= $diff_days; $i++):
            if (Carbon::parse($from_date)->addDays($i) <= Carbon::parse($thru_date)):
                $tog[$i] = GuestAppointment::where('club_id', '=', Auth::user()->club_id)
                    ->whereDate('fa_date', '=', Carbon::parse($from_date)->addDays($i)->toDateString())
                    ->whereDate('fa_date', '<=', Carbon::parse($thru_date)->toDateString())
                    ->whereHas('guestappointment_gastatus', function ($q) {
                        $q->where('status_id', '=', '3')
                            ->orWhere('status_id', '=', '4')
                            ->orWhere('status_id', '=', '5')
                            ->orWhere('status_id', '=', '6');
                    })
                    ->get()
                    ->count();
            endif;
        endfor;

        //New Sales, Membership Renewal, Membership Cancellation, Member Hold, Guest Appointment, & Locker
        for ($i = 0; $i <= $diff_days; $i++):
            if (Carbon::parse($from_date)->addDays($i) <= Carbon::parse($thru_date)):
                //New Sales
                $sales_pp_18[$i] = LogMemberStatus::select('user_id')
                    ->where('club_id', '=', Auth::user()->club_id)
                    ->whereDate('from_date', '=', Carbon::parse($from_date)->addDays($i)->toDateString())
                    ->whereDate('from_date', '<=', Carbon::parse($thru_date)->toDateString())
                    ->where('membership_type_id', '=', '4')
                    ->groupBy('user_id')
                    ->get()
                    ->count();

                $sales_ad_12[$i] = LogMemberStatus::select('user_id')
                    ->where('club_id', '=', Auth::user()->club_id)
                    ->whereDate('from_date', '=', Carbon::parse($from_date)->addDays($i)->toDateString())
                    ->whereDate('from_date', '<=', Carbon::parse($thru_date)->toDateString())
                    ->where(function ($q) {
                        $q->where('membership_type_id', '=', '2')
                            ->orWhere('membership_type_id', '=', '7');
                    })
                    ->groupBy('user_id')
                    ->get()
                    ->count();

                $sales_pp_12[$i] = LogMemberStatus::select('user_id')
                    ->where('club_id', '=', Auth::user()->club_id)
                    ->whereDate('from_date', '=', Carbon::parse($from_date)->addDays($i)->toDateString())
                    ->whereDate('from_date', '<=', Carbon::parse($thru_date)->toDateString())
                    ->where(function ($q) {
                        $q->where('membership_type_id', '=', '3')
                            ->orWhere('membership_type_id', '=', '8');
                    })
                    ->groupBy('user_id')
                    ->get()
                    ->count();

                $sales_st_12[$i] = LogMemberStatus::select('user_id')
                    ->where('club_id', '=', Auth::user()->club_id)
                    ->whereDate('from_date', '=', Carbon::parse($from_date)->addDays($i)->toDateString())
                    ->whereDate('from_date', '<=', Carbon::parse($thru_date)->toDateString())
                    ->where('membership_type_id', '=', '6')
                    ->groupBy('user_id')
                    ->get()
                    ->count();

                $sales_st_6[$i] = LogMemberStatus::select('user_id')
                    ->where('club_id', '=', Auth::user()->club_id)
                    ->whereDate('from_date', '=', Carbon::parse($from_date)->addDays($i)->toDateString())
                    ->whereDate('from_date', '<=', Carbon::parse($thru_date)->toDateString())
                    ->where('membership_type_id', '=', '5')
                    ->groupBy('user_id')
                    ->get()
                    ->count();

                $sales_mm[$i] = LogMemberStatus::select('user_id')
                    ->where('club_id', '=', Auth::user()->club_id)
                    ->whereDate('from_date', '=', Carbon::parse($from_date)->addDays($i)->toDateString())
                    ->whereDate('from_date', '<=', Carbon::parse($thru_date)->toDateString())
                    ->where('membership_type_id', '=', '1')
                    ->groupBy('user_id')
                    ->get()
                    ->count();

                $sales_daily[$i] = LogMemberStatus::select('user_id')
                    ->where('club_id', '=', Auth::user()->club_id)
                    ->whereDate('from_date', '=', Carbon::parse($from_date)->addDays($i)->toDateString())
                    ->whereDate('from_date', '<=', Carbon::parse($thru_date)->toDateString())
                    ->where('membership_type_id', '<>', '9')
                    ->groupBy('user_id')
                    ->get()
                    ->count();

                //Membership Renewal
                $renewal_pp18[$i] = ContinueMembership::whereHas('continuemembership_user', function ($q) {
                        $q->where('club_id', '=', Auth::user()->club_id);
                    })->where('status', '=', 'Active')
                    ->whereDate('from_date', '=', Carbon::parse($from_date)->addDays($i)->toDateString())
                    ->whereDate('from_date', '<=', Carbon::parse($thru_date)->toDateString())
                    ->where('membership_type_id', '=', '4')
                    ->get()
                    ->count();

                $renewal_ad12[$i] = ContinueMembership::whereHas('continuemembership_user', function ($q) {
                    $q->where('club_id', '=', Auth::user()->club_id);
                })->where('status', '=', 'Active')
                    ->whereDate('from_date', '=', Carbon::parse($from_date)->addDays($i)->toDateString())
                    ->whereDate('from_date', '<=', Carbon::parse($thru_date)->toDateString())
                    ->where(function ($q) {
                        $q->where('membership_type_id', '=', '2')
                            ->orWhere('membership_type_id', '=', '7');
                    })
                    ->get()
                    ->count();

                $renewal_pp12[$i] = ContinueMembership::whereHas('continuemembership_user', function ($q) {
                    $q->where('club_id', '=', Auth::user()->club_id);
                })->where('status', '=', 'Active')
                    ->whereDate('from_date', '=', Carbon::parse($from_date)->addDays($i)->toDateString())
                    ->whereDate('from_date', '<=', Carbon::parse($thru_date)->toDateString())
                    ->where(function ($q) {
                        $q->where('membership_type_id', '=', '3')
                            ->orWhere('membership_type_id', '=', '8');
                    })
                    ->get()
                    ->count();

                $renewal_st12[$i] = ContinueMembership::whereHas('continuemembership_user', function ($q) {
                    $q->where('club_id', '=', Auth::user()->club_id);
                })->where('status', '=', 'Active')
                    ->whereDate('from_date', '=', Carbon::parse($from_date)->addDays($i)->toDateString())
                    ->whereDate('from_date', '<=', Carbon::parse($thru_date)->toDateString())
                    ->where('membership_type_id', '=', '6')
                    ->get()
                    ->count();

                $renewal_st6[$i] = ContinueMembership::whereHas('continuemembership_user', function ($q) {
                    $q->where('club_id', '=', Auth::user()->club_id);
                })->where('status', '=', 'Active')
                    ->whereDate('from_date', '=', Carbon::parse($from_date)->addDays($i)->toDateString())
                    ->whereDate('from_date', '<=', Carbon::parse($thru_date)->toDateString())
                    ->where('membership_type_id', '=', '5')
                    ->get()
                    ->count();

                $renewal_mm[$i] = ContinueMembership::whereHas('continuemembership_user', function ($q) {
                    $q->where('club_id', '=', Auth::user()->club_id);
                })->where('status', '=', 'Active')
                    ->whereDate('from_date', '=', Carbon::parse($from_date)->addDays($i)->toDateString())
                    ->whereDate('from_date', '<=', Carbon::parse($thru_date)->toDateString())
                    ->where('membership_type_id', '=', '1')
                    ->get()
                    ->count();

                $renewal_daily[$i] = ContinueMembership::whereHas('continuemembership_user', function ($q) {
                    $q->where('club_id', '=', Auth::user()->club_id);
                })->where('status', '=', 'Active')
                    ->whereDate('from_date', '=', Carbon::parse($from_date)->addDays($i)->toDateString())
                    ->whereDate('from_date', '<=', Carbon::parse($thru_date)->toDateString())
                    ->where('membership_type_id', '<>', '9')
                    ->get()
                    ->count();

                //Membership Cancellation
                $cancellation_pp18[$i] = RefundSubmission::select('user_id')
                    ->where('refund_status', '=', 'Paid')
                    ->where('membership_type_id', '=', '4')
                    ->whereDate('refund_process_date', '=', Carbon::parse($from_date)->addDays($i)->toDateString())
                    ->whereDate('refund_process_date', '<=', Carbon::parse($thru_date)->toDateString())
                    ->whereHas('refundsubmission_user', function ($q) {
                        $q->where('club_id', '=', Auth::user()->club_id)
                            ->where('role_id', '=', '7');
                    })
                    ->groupBy('user_id')
                    ->get()
                    ->count();

                $cancellation_ad12[$i] = RefundSubmission::select('user_id')
                    ->where('refund_status', '=', 'Paid')
                    ->where(function ($q) {
                        $q->where('membership_type_id', '=', '2')
                            ->orWhere('membership_type_id', '=', '7');
                    })
                    ->whereDate('refund_process_date', '=', Carbon::parse($from_date)->addDays($i)->toDateString())
                    ->whereDate('refund_process_date', '<=', Carbon::parse($thru_date)->toDateString())
                    ->whereHas('refundsubmission_user', function ($q) {
                        $q->where('club_id', '=', Auth::user()->club_id)
                            ->where('role_id', '=', '7');
                    })
                    ->groupBy('user_id')
                    ->get()
                    ->count();

                $cancellation_pp12[$i] = RefundSubmission::select('user_id')
                    ->where('refund_status', '=', 'Paid')
                    ->where(function ($q) {
                        $q->where('membership_type_id', '=', '3')
                            ->orWhere('membership_type_id', '=', '8');
                    })
                    ->whereDate('refund_process_date', '=', Carbon::parse($from_date)->addDays($i)->toDateString())
                    ->whereDate('refund_process_date', '<=', Carbon::parse($thru_date)->toDateString())
                    ->whereHas('refundsubmission_user', function ($q) {
                        $q->where('club_id', '=', Auth::user()->club_id)
                            ->where('role_id', '=', '7');
                    })
                    ->groupBy('user_id')
                    ->get()
                    ->count();

                $cancellation_st12[$i] = RefundSubmission::select('user_id')
                    ->where('refund_status', '=', 'Paid')
                    ->where('membership_type_id', '=', '6')
                    ->whereDate('refund_process_date', '=', Carbon::parse($from_date)->addDays($i)->toDateString())
                    ->whereDate('refund_process_date', '<=', Carbon::parse($thru_date)->toDateString())
                    ->whereHas('refundsubmission_user', function ($q) {
                        $q->where('club_id', '=', Auth::user()->club_id)
                            ->where('role_id', '=', '7');
                    })
                    ->groupBy('user_id')
                    ->get()
                    ->count();

                $cancellation_st6[$i] = RefundSubmission::select('user_id')
                    ->where('refund_status', '=', 'Paid')
                    ->where('membership_type_id', '=', '5')
                    ->whereDate('refund_process_date', '=', Carbon::parse($from_date)->addDays($i)->toDateString())
                    ->whereDate('refund_process_date', '<=', Carbon::parse($thru_date)->toDateString())
                    ->whereHas('refundsubmission_user', function ($q) {
                        $q->where('club_id', '=', Auth::user()->club_id)
                            ->where('role_id', '=', '7');
                    })
                    ->groupBy('user_id')
                    ->get()
                    ->count();

                $cancellation_mm[$i] = RefundSubmission::select('user_id')
                    ->where('refund_status', '=', 'Paid')
                    ->where('membership_type_id', '=', '1')
                    ->whereDate('refund_process_date', '=', Carbon::parse($from_date)->addDays($i)->toDateString())
                    ->whereDate('refund_process_date', '<=', Carbon::parse($thru_date)->toDateString())
                    ->whereHas('refundsubmission_user', function ($q) {
                        $q->where('club_id', '=', Auth::user()->club_id)
                            ->where('role_id', '=', '7');
                    })
                    ->groupBy('user_id')
                    ->get()
                    ->count();

                $cancellation_daily[$i] = RefundSubmission::select('user_id')
                    ->where('refund_status', '=', 'Paid')
                    ->where('membership_type_id', '<>', '9')
                    ->whereDate('refund_process_date', '=', Carbon::parse($from_date)->addDays($i)->toDateString())
                    ->whereDate('refund_process_date', '<=', Carbon::parse($thru_date)->toDateString())
                    ->whereHas('refundsubmission_user', function ($q) {
                        $q->where('club_id', '=', Auth::user()->club_id)
                            ->where('role_id', '=', '7');
                    })
                    ->groupBy('user_id')
                    ->get()
                    ->count();

                //Member Hold
                $member_hold[$i] = MemberHold::select('user_id')
                    ->where('club_id', '=', Auth::user()->club_id)
                    ->whereDate('from_date', '=', Carbon::parse($from_date)->addDays($i)->toDateString())
                    ->whereDate('from_date', '<=', Carbon::parse($thru_date)->toDateString())
                    ->where(function ($q) {
                        $q->where('status', '=', 'OnHold')
                            ->orWhere('status', '=', 'Finished');
                    })
                    ->groupBy('user_id', 'from_date')
                    ->get()
                    ->count();

                $member_hold_activated[$i] = MemberHold::select('user_id')
                    ->where('club_id', '=', Auth::user()->club_id)
                    ->whereDate('thru_date', '=', Carbon::parse($from_date)->addDays($i)->toDateString())
                    ->whereDate('thru_date', '<=', Carbon::parse($thru_date)->toDateString())
                    ->where('status', '=', 'Finished')
                    ->groupBy('user_id', 'thru_date')
                    ->get()
                    ->count();

                //Guest Appointment
                $guest_appointment[$i] = GuestAppointment::where('club_id', '=', Auth::user()->club_id)
                    ->whereDate('fa_date', '=', Carbon::parse($from_date)->addDays($i)->toDateString())
                    ->whereDate('fa_date', '<=', Carbon::parse($thru_date)->toDateString())
                    ->get()
                    ->count();

                //Locker
                $locker[$i] = LockerPayment::selectRaw("SUM(pay) as pay")
                    ->whereHas('lockerpayment_memberlocker', function ($q) {
                        $q->whereHas('memberlocker_user', function ($r) {
                            $r->where('club_id', '=', Auth::user()->club_id);
                        });
                    })->whereDate('payment_date', '=', Carbon::parse($from_date)->addDays($i)->toDateString())
                        ->whereDate('payment_date', '<=', Carbon::parse($thru_date)->toDateString())
                        ->get();
            endif;
        endfor;

        //Product
        $product_categories = ProductTypes::orderBy('type', 'asc')->get();
        foreach ($product_categories as $product_category):
            $product_sold[$product_category->id] = [];
            for ($i = 0; $i <= $diff_days; $i++):
                if (Carbon::parse($from_date)->addDays($i) <= Carbon::parse($thru_date)):
//                    $product_sold[$product_category->id][$i] = ProductPaymentDetail::whereHas('productpaymentdetail_productpayment', function ($q) use ($from_date, $thru_date, $i) {
//                        $q->where('club_id', '=', Auth::user()->club_id)
//                            ->whereDate('payment_date', '=', Carbon::parse($from_date)->addDays($i)->toDateString())
//                            ->whereDate('payment_date', '<=', Carbon::parse($thru_date)->toDateString());
//                    })->whereHas('productpaymentdetail_product', function ($r) use ($product_category) {
//                        $r->where('product_type_id', '=', $product_category->id);
//                    })->get()->count();

                    $product_sold[$product_category->id][$i] = ProductPaymentDetail::selectRaw("SUM(price*qty) AS pay, SUM(discount_value) AS discount")
                        ->whereHas('productpaymentdetail_productpayment', function ($q) use ($from_date, $thru_date, $i) {
                            $q->where('club_id', '=', Auth::user()->club_id)
                                ->whereDate('payment_date', '=', Carbon::parse($from_date)->addDays($i)->toDateString())
                                ->whereDate('payment_date', '<=', Carbon::parse($thru_date)->toDateString());
                        })->whereHas('productpaymentdetail_product', function ($r) use ($product_category) {
                            $r->where('product_type_id', '=', $product_category->id);
                        })
//                        ->groupBy('product_id')
                        ->get();
                endif;
            endfor;
        endforeach;

        //Penjualan Baru (New Sales & Due/Drafts)
        //New Sales
//        $new_sales_daily = [];
//        for ($i = 0; $i <= $diff_days; $i++):
//            if (Carbon::parse($from_date)->addDays($i) <= Carbon::parse($thru_date)):
//                $new_sales_daily[$i] = MemberPayment::where('payment_category', '=', 'First')
//                    ->whereHas('memberpayment_user', function ($q) {
//                        $q->where('club_id', '=', Auth::user()->club_id);
//                    })
//                    ->where('payment_status', '<>', 'Unpaid')
//                    ->whereDate('payment_date', '=', Carbon::parse($from_date)->addDays($i)->toDateString())
//                    ->whereDate('payment_date', '<=', Carbon::parse($thru_date)->toDateString())
//                    ->get();
//            endif;
//        endfor;
        $new_sales = MemberPayment::selectRaw("SUM(member_payments.amount_paid) as paid, member_payments.payment_date, SUM(member_debt_payments.amount_paid) as installment, 
            SUM(member_payments.rest_of_bill) as debt_today")
            ->where('member_payments.payment_category', '=', 'First')
            ->whereHas('memberpayment_user', function ($q) {
                $q->where('club_id', '=', Auth::user()->club_id);
            })
            ->where('member_payments.payment_status', '<>', 'Unpaid')
            ->whereDate('member_payments.payment_date', '>=', Carbon::parse($from_date)->toDateString())
            ->whereDate('member_payments.payment_date', '<=', Carbon::parse($thru_date)->toDateString())
            ->leftJoin('member_debt_payments', 'member_debt_payments.member_payment_id', '=', 'member_payments.id')
            ->groupBy('member_payments.payment_date')
            ->get();

        //Due/Drafts
        $membership_payment_daily = [];
        $debt_payment_daily = [];
        for ($i = 0; $i <= $diff_days; $i++):
            if (Carbon::parse($from_date)->addDays($i) <= Carbon::parse($thru_date)):

                $membership_payment_daily[$i] = MemberPayment::selectRaw("SUM(amount_paid) as paid")
                    ->where('payment_category', '=', 'Monthly')
                    ->whereHas('memberpayment_user', function ($q) {
                        $q->where('club_id', '=', Auth::user()->club_id);
                    })
                    ->where('payment_status', '<>', 'Unpaid')
                    ->whereDate('payment_date', '=', Carbon::parse($from_date)->addDays($i)->toDateString())
                    ->whereDate('payment_date', '<=', Carbon::parse($thru_date)->toDateString())
                    ->get();

                $debt_payment_daily[$i] = MemberDebtPayment::selectRaw("SUM(amount_paid) as paid")
                    ->whereHas('memberdebtpayment_memberpayment', function ($q) {
                        $q->whereHas('memberpayment_user', function ($r) {
                            $r->where('club_id', '=', Auth::user()->club_id);
                        });
                    })
                    ->whereDate('payment_date', '=', Carbon::parse($from_date)->addDays($i)->toDateString())
                    ->whereDate('payment_date', '<=', Carbon::parse($thru_date)->toDateString())
                    ->get();
            endif;
        endfor;

//        return view('cf_lite.document.pj.pj-report', compact('from_date', 'diff_days', 'wo_daily', 'sources', 'guest', 'tog', 'sales_pp_18', 'sales_ad_12',
//            'sales_pp_12', 'sales_st_12', 'sales_st_6', 'sales_mm', 'sales_daily', 'renewal_pp18', 'renewal_ad12', 'renewal_pp12', 'renewal_st12', 'renewal_st6',
//            'renewal_mm', 'renewal_daily', 'cancellation_pp18', 'cancellation_ad12', 'cancellation_pp12', 'cancellation_st12', 'cancellation_st6', 'cancellation_mm',
//            'cancellation_daily', 'member_hold', 'member_hold_activated', 'guest_appointment', 'product_categories', 'product_sold', 'locker'));

        return view('cf_lite.document.pj.pj-report-v2', compact('from_date', 'diff_days', 'wo_daily', 'sources', 'guest', 'tog', 'sales_pp_18', 'sales_ad_12',
            'sales_pp_12', 'sales_st_12', 'sales_st_6', 'sales_mm', 'sales_daily', 'renewal_pp18', 'renewal_ad12', 'renewal_pp12', 'renewal_st12', 'renewal_st6',
            'renewal_mm', 'renewal_daily', 'cancellation_pp18', 'cancellation_ad12', 'cancellation_pp12', 'cancellation_st12', 'cancellation_st6', 'cancellation_mm',
            'cancellation_daily', 'member_hold', 'member_hold_activated', 'guest_appointment', 'product_categories', 'product_sold', 'locker', 'new_sales',
            'membership_payment_daily', 'debt_payment_daily'));
    }
}
