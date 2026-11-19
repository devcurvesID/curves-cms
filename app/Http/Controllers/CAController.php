<?php

namespace App\Http\Controllers;

use App\Model\Appointment\GuestAppointment;
use App\Model\Log\LogMemberStatus;
use App\Model\MasterData\MemberSource;
use App\Model\Member\ContinueMembership;
use App\Model\Member\MemberHold;
use App\Model\Refund\RefundSubmission;
use App\Model\Report\CAManagement;
use App\Model\WM\WeighMeasure;
use App\Model\Workout\Workout;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CAController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('wm-only');
    }

    public function generate_report(Request $request) {
        $this->validate($request, [
            'date' => 'required|date',
            'membership_target' => 'required|numeric',
        ]);

        $from_date = new Carbon('first day of '.Carbon::parse($request->date)->format('F Y'));
        $thru_date = new Carbon('last day of '.Carbon::parse($request->date)->format('F Y'));

//        //Total Anggota
//        $total_member = LogMemberStatus::select('user_id')
//            ->where('club_id', '=', Auth::user()->club_id)
////            ->whereDate('from_date', '>=', Carbon::parse($from_date)->toDateString());
//            ->whereDate('from_date', '<=', Carbon::parse($thru_date)->toDateString())
//            ->whereDate('thru_date', '>=', Carbon::parse($from_date)->toDateString());
//
//        $tm = $total_member->groupBy('user_id')
//            ->get()
//            ->count();
//
//        //Total Anggota Aktif
//        $tam = $total_member->whereDate('thru_date', '>=', Carbon::parse($from_date)->toDateString())
//            ->groupBy('user_id')
//            ->get()
//            ->count();

//        //Guest Appointment
//        $fa = GuestAppointment::where('club_id', '=', Auth::user()->club_id)
//            ->whereDate('fa_date', '>=', Carbon::parse($from_date)->toDateString())
//            ->whereDate('fa_date', '<=', Carbon::parse($thru_date)->toDateString());
//
//        $total_fa = $fa->get()->count();
//
//        //Show Up
//        $show_up_guests = $fa->whereHas('guestappointment_gastatus', function ($q) {
//            $q->where('status_id', '=', '3')
//                ->orWhere('status_id', '=', '5')
//                ->orWhere('status_id', '=', '6');
//        })->get()->count();
//
//        //Sale FA
//        $sale_fas = $fa->whereHas('guestappointment_gastatus', function ($q) {
//            $q->where('status_id', '=', '5');
//        })->get()->count();

        //REAL GOALS (LAST MONTH)
        $from_date_lm = new Carbon('first day of '.Carbon::parse($request->date)->subMonth()->format('F Y'));
        $thru_date_lm = new Carbon('last day of '.Carbon::parse($request->date)->subMonth()->format('F Y'));

        //Total Anggota
        $tm = User::select('id')
            ->where('club_id', '=', Auth::user()->club_id)
            ->where('role_id', '=', '7')
            ->whereHas('user_logmemberstatus', function ($q) use ($thru_date_lm) {
//                $q->whereDate('from_date', '<=', Carbon::parse($from_date_lm)->toDateString())
//                    ->whereDate('thru_date', '>=', Carbon::parse($thru_date_lm)->toDateString());
                $q->whereDate('from_date', '<=', Carbon::parse($thru_date_lm)->toDateString());
            })
            ->get()
            ->count();

        //Total Anggota Aktif
        $tam = User::select('id')
            ->where('club_id', '=', Auth::user()->club_id)
            ->where('role_id', '=', '7')
            ->whereHas('user_logmemberstatus', function ($q) use ($thru_date_lm) {
                $q->whereDate('thru_date', '>=', Carbon::parse($thru_date_lm)->toDateString())
                    ->whereDate('from_date', '<=', Carbon::parse($thru_date_lm)->toDateString());
            })
            ->where(function ($r) use ($from_date_lm, $thru_date_lm) {
                $r->whereDoesntHave('user_memberhold')
                    ->orWhere(function ($s) use ($from_date_lm, $thru_date_lm) {
                        $s->whereHas('user_memberhold', function ($t) use ($from_date_lm, $thru_date_lm) {
                            $t->where(function ($u) use ($from_date_lm) {
                                $u->whereDate('from_date', '<', Carbon::parse($from_date_lm)->toDateString())
                                    ->whereDate('thru_date', '<=', Carbon::parse($from_date_lm)->toDateString());
                            })
                            ->orWhere(function ($v) use ($thru_date_lm) {
                                $v->whereDate('from_date', '>=', Carbon::parse($thru_date_lm)->toDateString())
                                    ->whereDate('thru_date', '>', Carbon::parse($thru_date_lm)->toDateString());
                            });
                        });
                    });
            })
            ->get()
            ->count();

        //Guest Appointment
        $fa = GuestAppointment::where('club_id', '=', Auth::user()->club_id)
            ->whereDate('fa_date', '>=', Carbon::parse($from_date_lm)->toDateString())
            ->whereDate('fa_date', '<=', Carbon::parse($thru_date_lm)->toDateString());

        $total_fa = $fa->get()->count();

        //Show Up
        $show_up_guests = $fa->whereHas('guestappointment_gastatus', function ($q) {
            $q->where('status_id', '=', '3')
                ->orWhere('status_id', '=', '5')
                ->orWhere('status_id', '=', '6');
        })->get()->count();

        //Sale FA
        $sale_fas = $fa->whereHas('guestappointment_gastatus', function ($q) {
            $q->where('status_id', '=', '5');
        })->get()->count();

        //FWO Last Month
        $fwos = User::where('club_id', '=', Auth::user()->club_id)
            ->where('role_id', '=', '7')
            ->whereHas('user_logmemberstatus', function ($q) use ($from_date_lm, $thru_date_lm) {
                $q->whereDate('from_date', '>=', Carbon::parse($from_date_lm)->toDateString())
                    ->whereDate('from_date', '<=', Carbon::parse($thru_date_lm)->toDateString());
            })
            ->whereHas('user_workout', function ($r) use ($from_date_lm, $thru_date_lm) {
                $r->whereDate('workout_date', '>=', Carbon::parse($from_date_lm)->toDateString())
                    ->whereDate('workout_date', '<=', Carbon::parse($thru_date_lm)->toDateString())
                    ->whereNotIn('user_id', DB::table('workouts')
                        ->WhereDate('workout_date', '<', Carbon::parse($from_date_lm))
                        ->pluck('user_id'));
            })
            ->get()
            ->count();

        //Collective Total Member
        $member_collective = [];
        for ($i = 0; $i < 5; $i++):
            if (Carbon::parse($from_date)->addWeeks($i) < Carbon::parse($thru_date)):
//                $member_collective[$i] = LogMemberStatus::select('user_id')
//                    ->where('club_id', '=', Auth::user()->club_id)
//                    ->whereDate('from_date', '<=', Carbon::parse($from_date)->addWeeks($i)->toDateString())
//                    ->whereDate('thru_date', '>=', Carbon::parse($from_date)->addWeeks($i)->toDateString())
//                    ->groupBy('user_id')
//                    ->get()
//                    ->count();

                //V2
                $member_collective[$i] = User::select('id')
                    ->where('club_id', '=', Auth::user()->club_id)
                    ->where('role_id', '=', '7')
                    ->whereHas('user_logmemberstatus', function ($q) use ($from_date, $i) {
                        $q->whereDate('from_date', '<=', Carbon::parse($from_date)->addWeeks($i)->toDateString())
                            ->whereDate('thru_date', '>=', Carbon::parse($from_date)->addWeeks($i)->toDateString());
                    })
                    ->where(function ($r) use ($from_date, $i) {
                        $r->whereDoesntHave('user_refundsubmission')
                            ->orWhere(function ($s) use ($from_date, $i) {
                                $s->whereHas('user_refundsubmission', function ($t) use ($from_date, $i) {
                                    $t->where(function ($u)  use ($from_date, $i) {
                                        $u->where('refund_status', '=', 'Paid')
                                            ->whereDate('start_membership_date', '<=', Carbon::parse($from_date)->addWeeks($i)->toDateString())
                                            ->whereDate('refund_process_date', '>', Carbon::parse($from_date)->addWeeks($i)->toDateString());
                                    })
                                    ->orWhere(function ($v)  use ($from_date, $i) {
                                        $v->where('refund_status', '<>', 'Paid')
                                            ->whereNotIn('user_id', DB::table('refund_submissions')
                                                ->where('refund_status', '=', 'Paid')
                                                ->whereDate('start_membership_date', '<=', Carbon::parse($from_date)->addWeeks($i)->toDateString())
                                                ->whereDate('refund_process_date', '<=', Carbon::parse($from_date)->addWeeks($i)->toDateString())
                                                ->whereDate('refund_process_date', '>=', Carbon::parse($from_date)->addWeeks($i-1)->toDateString())
                                                ->pluck('user_id'))
                                            ->whereDate('start_membership_date', '<=', Carbon::parse($from_date)->addWeeks($i)->toDateString())
                                            ->whereDate('refund_process_date', '<=', Carbon::parse($from_date)->addWeeks($i)->toDateString())
                                            ->whereDate('refund_process_date', '>=', Carbon::parse($from_date)->addWeeks($i-1)->toDateString());
                                    });
                                });
                            });
                    })
                    ->get()
                    ->count();
            endif;
        endfor;

        //Total Member in Hold
        $member_hold = [];
        for ($i = 0; $i < 5; $i++):
            if (Carbon::parse($from_date)->addWeeks($i) < Carbon::parse($thru_date)):
                $member_hold[$i] = MemberHold::select('user_id')
                    ->where('club_id', '=', Auth::user()->club_id)
//                    ->whereDate('from_date', '>=', Carbon::parse($from_date)->toDateString())
//                    ->whereDate('from_date', '<', Carbon::parse($from_date)->addWeeks(($i + 1))->toDateString())
                    ->whereDate('from_date', '<=', Carbon::parse($from_date)->addWeeks(($i + 1))->toDateString())
                    ->whereDate('thru_date', '>=', Carbon::parse($from_date)->addWeeks($i)->toDateString())
                    ->where(function ($q) {
                        $q->where('status', '=', 'OnHold')
                            ->orWhere('status', '=', 'Finished');
                    })
                    ->groupBy('user_id')
                    ->get()
                    ->count();
            endif;
        endfor;

        //Free Membership
        $free_membership = [];
        for ($i = 0; $i < 5; $i++):
            if (Carbon::parse($from_date)->addWeeks($i) < Carbon::parse($thru_date)):
//                $free_membership[$i] = LogMemberStatus::select('user_id')
//                    ->where('club_id', '=', Auth::user()->club_id)
//                    ->where('membership_type_id', '=', '10')
//                    ->whereDate('from_date', '>=', Carbon::parse($from_date)->toDateString())
//                    ->whereDate('from_date', '<', Carbon::parse($from_date)->addWeeks(($i + 1))->toDateString())
//                    ->whereDate('thru_date', '>=', Carbon::parse($from_date)->addWeeks($i)->toDateString())
//                    ->groupBy('user_id')
//                    ->get()
//                    ->count();

                //V2
                $free_membership[$i] = User::select('id')
                    ->where('club_id', '=', Auth::user()->club_id)
                    ->where('role_id', '=', '7')
                    ->whereHas('user_logmemberstatus', function ($q) use ($from_date, $i) {
                        $q->where('membership_type_id', '=', '10')
                            ->whereDate('from_date', '<=', Carbon::parse($from_date)->addWeeks(($i + 1))->toDateString())
                            ->whereDate('thru_date', '>=', Carbon::parse($from_date)->addWeeks($i)->toDateString());
                    })
                    ->get()
                    ->count();
            endif;
        endfor;

        //Total Workout
        $total_wo = [];
        $total_wo_weekly = [];
        $total_wo_this_month = 0;
        for ($i = 0; $i < 5; $i++):
            if (Carbon::parse($from_date)->addWeeks($i) < Carbon::parse($thru_date)):
                $wo_weekly = Workout::where('club_id', '=', Auth::user()->club_id)
                    ->whereDate('workout_date', '>=', Carbon::parse($from_date)->addWeeks($i)->toDateString())
                    ->whereDate('workout_date', '<', Carbon::parse($from_date)->addWeeks(($i + 1))->toDateString())
                    ->get();

                $total_wo_weekly[$i] = 0;
                for ($j = 0; $j < 7; $j++):
                    $wo_daily = Workout::where('club_id', '=', Auth::user()->club_id)
                        ->whereDate('workout_date', '>=', Carbon::parse($from_date)->addWeeks($i)->addDays($j)->toDateString())
                        ->whereDate('workout_date', '<', Carbon::parse($from_date)->addWeeks(($i + 1))->toDateString())
                        ->get()
                        ->count();

                    if ($total_wo_weekly[$i] < $wo_daily):
                        $total_wo_weekly[$i] = $wo_daily;
                    endif;
                endfor;

                $total_wo[$i] = $wo_weekly->count();
                $total_wo_this_month = $total_wo_this_month + $total_wo[$i];
            endif;
        endfor;

        //Member Cancellation
        $total_cancellation_weekly = [];
        $cancellation_bfwo = [];
        $cancellation_afwo = [];
        $cancellation_pp18 = [];
        $cancellation_pp12 = [];
        $cancellation_ad12 = [];
        $cancellation_st12 = [];
        $cancellation_st6 = [];
        $cancellation_mm = [];
        $total_cancellation = 0;
        $total_cancellation_bfwo = 0;
        $total_cancellation_afwo = 0;
        $total_cancellation_pp18 = 0;
        $total_cancellation_pp12 = 0;
        $total_cancellation_ad12 = 0;
        $total_cancellation_st12 = 0;
        $total_cancellation_st6 = 0;
        $total_cancellation_mm = 0;
        for ($i = 0; $i < 5; $i++):
            if (Carbon::parse($from_date)->addWeeks($i) < Carbon::parse($thru_date)):
                $total_cancellation_weekly[$i] = RefundSubmission::select('user_id')
                    ->where('refund_status', '=', 'Paid')
                    ->whereDate('refund_process_date', '>=', Carbon::parse($from_date)->addWeeks($i)->toDateString())
                    ->whereDate('refund_process_date', '<=', Carbon::parse($from_date)->addWeeks($i + 1)->toDateString())
                    ->whereHas('refundsubmission_user', function ($q) {
                        $q->where('club_id', '=', Auth::user()->club_id)
                            ->where('role_id', '=', '7');
                    })
                    ->groupBy('user_id')
                    ->get()
                    ->count();

                $total_cancellation = $total_cancellation + $total_cancellation_weekly[$i];

                //Cancellation Before FWO
                $cancellation_bfwo[$i] = RefundSubmission::select('user_id')
                    ->where('refund_status', '=', 'Paid')
                    ->whereDate('refund_process_date', '>=', Carbon::parse($from_date)->addWeeks($i)->toDateString())
                    ->whereDate('refund_process_date', '<=', Carbon::parse($from_date)->addWeeks($i + 1)->toDateString())
                    ->whereHas('refundsubmission_user', function ($q) {
                        $q->where('club_id', '=', Auth::user()->club_id)
                            ->where('role_id', '=', '7')
                            ->whereDoesntHave('user_workout');
                    })->get()->count();

                $total_cancellation_bfwo = $total_cancellation_bfwo + $cancellation_bfwo[$i];

                //Cancellation After FWO
                $cancellation_afwo[$i] = $total_cancellation_weekly[$i] - $cancellation_bfwo[$i];
                $total_cancellation_afwo = $total_cancellation_afwo + $cancellation_afwo[$i];

                //Cancellation PP 18 Months
                $cancellation_pp18[$i] = RefundSubmission::select('user_id')
                    ->where('refund_status', '=', 'Paid')
                    ->whereDate('refund_process_date', '>=', Carbon::parse($from_date)->addWeeks($i)->toDateString())
                    ->whereDate('refund_process_date', '<=', Carbon::parse($from_date)->addWeeks($i + 1)->toDateString())
                    ->whereHas('refundsubmission_membershiptype', function($q) {
                        $q->where('payment_type', '=', 'Prepaid')
                            ->where('membership_type', '<>', 'Student')
                            ->where('duration', '=', '18');
                    })
                    ->whereRaw('date(refund_submission_date) < date(end_membership_date)')
                    ->get()
                    ->count();

                $total_cancellation_pp18 = $total_cancellation_pp18 + $cancellation_pp18[$i];

                //Cancellation PP 12 Months
                $cancellation_pp12[$i] = RefundSubmission::select('user_id')
                    ->where('refund_status', '=', 'Paid')
                    ->whereDate('refund_process_date', '>=', Carbon::parse($from_date)->addWeeks($i)->toDateString())
                    ->whereDate('refund_process_date', '<=', Carbon::parse($from_date)->addWeeks($i + 1)->toDateString())
                    ->whereHas('refundsubmission_membershiptype', function($q) {
                        $q->where('payment_type', '=', 'Prepaid')
                            ->where('membership_type', '<>', 'Student')
                            ->where('duration', '=', '12');
                    })
                    ->whereRaw('date(refund_submission_date) < date(end_membership_date)')
                    ->get()
                    ->count();

                $total_cancellation_pp12 = $total_cancellation_pp12 + $cancellation_pp12[$i];

                //Cancellation AD 12 Months
                $cancellation_ad12[$i] = RefundSubmission::select('user_id')
                    ->where('refund_status', '=', 'Paid')
                    ->whereDate('refund_process_date', '>=', Carbon::parse($from_date)->addWeeks($i)->toDateString())
                    ->whereDate('refund_process_date', '<=', Carbon::parse($from_date)->addWeeks($i + 1)->toDateString())
                    ->whereHas('refundsubmission_membershiptype', function($q) {
                        $q->where('payment_type', '=', 'AD')
                            ->where('membership_type', '<>', 'Student')
                            ->where('duration', '=', '12');
                    })
                    ->whereRaw('date(refund_submission_date) < date(end_membership_date)')
                    ->get()
                    ->count();

                $total_cancellation_ad12 = $total_cancellation_ad12 + $cancellation_ad12[$i];

                //Cancellation Student 12 Months
                $cancellation_st12[$i] = RefundSubmission::select('user_id')
                    ->where('refund_status', '=', 'Paid')
                    ->whereDate('refund_process_date', '>=', Carbon::parse($from_date)->addWeeks($i)->toDateString())
                    ->whereDate('refund_process_date', '<=', Carbon::parse($from_date)->addWeeks($i + 1)->toDateString())
                    ->whereHas('refundsubmission_membershiptype', function($q) {
                        $q->where('membership_type', '=', 'Student')
                            ->where('duration', '=', '12');
                    })
                    ->whereRaw('date(refund_submission_date) < date(end_membership_date)')
                    ->get()
                    ->count();

                $total_cancellation_st12 = $total_cancellation_st12 + $cancellation_st12[$i];

                //Cancellation Student 6 Months
                $cancellation_st6[$i] = RefundSubmission::select('user_id')
                    ->where('refund_status', '=', 'Paid')
                    ->whereDate('refund_process_date', '>=', Carbon::parse($from_date)->addWeeks($i)->toDateString())
                    ->whereDate('refund_process_date', '<=', Carbon::parse($from_date)->addWeeks($i + 1)->toDateString())
                    ->whereHas('refundsubmission_membershiptype', function($q) {
                        $q->where('membership_type', '=', 'Student')
                            ->where('duration', '=', '6');
                    })
                    ->whereRaw('date(refund_submission_date) < date(end_membership_date)')
                    ->get()
                    ->count();

                $total_cancellation_st6 = $total_cancellation_st6 + $cancellation_st6[$i];

                //Cancellation Month to Month
                $cancellation_mm[$i] = RefundSubmission::select('user_id')
                    ->where('refund_status', '=', 'Paid')
                    ->whereDate('refund_process_date', '>=', Carbon::parse($from_date)->addWeeks($i)->toDateString())
                    ->whereDate('refund_process_date', '<=', Carbon::parse($from_date)->addWeeks($i + 1)->toDateString())
                    ->where('membership_type_id', '=', '1')
                    ->whereRaw('date(refund_submission_date) < date(end_membership_date)')
                    ->get()
                    ->count();

                $total_cancellation_mm = $total_cancellation_mm + $cancellation_mm[$i];

                //Total Weigh & Measure
                $total_wm = WeighMeasure::whereHas('weighmeasure_member', function ($q) {
                    $q->where('club_id', '=', Auth::user()->club_id);
                })->whereDate('wm_date', '>=', Carbon::parse($from_date)->toDateString())
                    ->whereDate('wm_date', '<=', Carbon::parse($thru_date)->toDateString())
                    ->get()
                    ->count();

                //Member Flag
                $inputdate = $request->date;

                $wo_flags = Workout::selectRaw('COUNT(workout_date) as wo_count')
                    ->where('club_id', '=', Auth::user()->club_id)
                    ->whereDate('workout_date', '>=', Carbon::parse($from_date)->toDateString())
                    ->whereDate('workout_date', '<=', Carbon::parse($thru_date)->toDateString())
                    ->groupBy('user_id')
                    ->get();

                foreach ($wo_flags as $wo_flag):
                    if ($wo_flag->wo_count >= 12):
                        $wo_flag->setAttribute('wo_flag','A');
                    elseif (($wo_flag->wo_count >= 8) && ($wo_flag->wo_count <= 11)):
                        $wo_flag->setAttribute('wo_flag','B');
                    elseif (($wo_flag->wo_count >= 4) && ($wo_flag->wo_count <= 7)):
                        $wo_flag->setAttribute('wo_flag','C');
                    elseif (($wo_flag->wo_count >= 1) && ($wo_flag->wo_count <= 3)):
                        $wo_flag->setAttribute('wo_flag','D');
                    else:
                        $wo_flag->setAttribute('wo_flag','E');
                    endif;
                endforeach;
            endif;
        endfor;

        //Appointment
        $sources = MemberSource::orderBy('source', 'asc')->get();

        foreach ($sources as $source):
            $member_from_source[$source->id] = [];
            for ($i = 0; $i < 5; $i++):
                if (Carbon::parse($from_date)->addWeeks($i) < Carbon::parse($thru_date)):
                    if ($source->id == 1):
                        $member_from_source[$source->id][$i] = GuestAppointment::where('club_id', '=', Auth::user()->club_id)
                            ->whereHas('guestappointment_buddyreferrallead', function ($q) {
                                $q->where(function ($r) {
                                    $r->where('source_id', '=', '1')
                                        ->orWhere('source_id', null);
                                })->where('is_br', '=', 'BR');
                            })->whereDate('fa_date', '>=', Carbon::parse($from_date)->addWeeks($i)->toDateString())
                            ->whereDate('fa_date', '<=', Carbon::parse($from_date)->addWeeks($i + 1)->toDateString())
                            ->whereDate('fa_date', '<=', Carbon::parse($thru_date)->toDateString())
                            ->get()
                            ->count();
                    else:
                        $member_from_source[$source->id][$i] = GuestAppointment::where('club_id', '=', Auth::user()->club_id)
                            ->whereHas('guestappointment_buddyreferrallead', function ($q) use ($source) {
                                $q->where('source_id', '=', $source->id)
                                    ->where('is_br', '=', 'NotBR');
                            })->whereDate('fa_date', '>=', Carbon::parse($from_date)->addWeeks($i)->toDateString())
                            ->whereDate('fa_date', '<=', Carbon::parse($from_date)->addWeeks($i + 1)->toDateString())
                            ->whereDate('fa_date', '<=', Carbon::parse($thru_date)->toDateString())
                            ->get()
                            ->count();
                    endif;
                endif;
            endfor;
        endforeach;

        //Attending Guest
        $attending_guest = [];

        for ($i = 0; $i < 5; $i++):
            if (Carbon::parse($from_date)->addWeeks($i) < Carbon::parse($thru_date)):
                $attending_guest[$i] = GuestAppointment::where('club_id', '=', Auth::user()->club_id)
                    ->whereDate('fa_date', '>=', Carbon::parse($from_date)->addWeeks($i)->toDateString())
                    ->whereDate('fa_date', '<=', Carbon::parse($from_date)->addWeeks($i + 1)->toDateString())
                    ->whereDate('fa_date', '<=', Carbon::parse($thru_date)->toDateString())
                    ->whereHas('guestappointment_gastatus', function ($q) {
                        $q->where('status_id', '=', '3')
                            ->orWhere('status_id', '=', '5')
                            ->orWhere('status_id', '=', '6');
                    })->get()->count();
            endif;
        endfor;

        //New Sales & Membership Renewal
        for ($i = 0; $i < 5; $i++):
            if (Carbon::parse($from_date)->addWeeks($i) < Carbon::parse($thru_date)):
                //New Sales
                $new_sales_pp18[$i] = LogMemberStatus::select('user_id')
                    ->where('club_id', '=', Auth::user()->club_id)
                    ->whereDate('from_date', '>=', Carbon::parse($from_date)->addWeeks($i)->toDateString())
                    ->whereDate('from_date', '<=', Carbon::parse($from_date)->addWeeks($i + 1)->toDateString())
                    ->whereDate('from_date', '<=', Carbon::parse($thru_date)->toDateString())
                    ->where('membership_type_id', '=', '4')
                    ->groupBy('user_id')
                    ->get()
                    ->count();

                $new_sales_pp12[$i] = LogMemberStatus::select('user_id')
                    ->where('club_id', '=', Auth::user()->club_id)
                    ->whereDate('from_date', '>=', Carbon::parse($from_date)->addWeeks($i)->toDateString())
                    ->whereDate('from_date', '<=', Carbon::parse($from_date)->addWeeks($i + 1)->toDateString())
                    ->whereDate('from_date', '<=', Carbon::parse($thru_date)->toDateString())
                    ->where(function ($q) {
                        $q->where('membership_type_id', '=', '3')
                            ->orWhere('membership_type_id', '=', '8');
                    })
                    ->groupBy('user_id')
                    ->get()
                    ->count();

                $new_sales_ad12[$i] = LogMemberStatus::select('user_id')
                    ->where('club_id', '=', Auth::user()->club_id)
                    ->whereDate('from_date', '>=', Carbon::parse($from_date)->addWeeks($i)->toDateString())
                    ->whereDate('from_date', '<=', Carbon::parse($from_date)->addWeeks($i + 1)->toDateString())
                    ->whereDate('from_date', '<=', Carbon::parse($thru_date)->toDateString())
                    ->where(function ($q) {
                        $q->where('membership_type_id', '=', '2')
                            ->orWhere('membership_type_id', '=', '7');
                    })
                    ->groupBy('user_id')
                    ->get()
                    ->count();

                $new_sales_st12[$i] = LogMemberStatus::select('user_id')
                    ->where('club_id', '=', Auth::user()->club_id)
                    ->whereDate('from_date', '>=', Carbon::parse($from_date)->addWeeks($i)->toDateString())
                    ->whereDate('from_date', '<=', Carbon::parse($from_date)->addWeeks($i + 1)->toDateString())
                    ->whereDate('from_date', '<=', Carbon::parse($thru_date)->toDateString())
                    ->where('membership_type_id', '=', '6')
                    ->groupBy('user_id')
                    ->get()
                    ->count();

                $new_sales_st6[$i] = LogMemberStatus::select('user_id')
                    ->where('club_id', '=', Auth::user()->club_id)
                    ->whereDate('from_date', '>=', Carbon::parse($from_date)->addWeeks($i)->toDateString())
                    ->whereDate('from_date', '<=', Carbon::parse($from_date)->addWeeks($i + 1)->toDateString())
                    ->whereDate('from_date', '<=', Carbon::parse($thru_date)->toDateString())
                    ->where('membership_type_id', '=', '5')
                    ->groupBy('user_id')
                    ->get()
                    ->count();

                $new_sales_mm[$i] = LogMemberStatus::select('user_id')
                    ->where('club_id', '=', Auth::user()->club_id)
                    ->whereDate('from_date', '>=', Carbon::parse($from_date)->addWeeks($i)->toDateString())
                    ->whereDate('from_date', '<=', Carbon::parse($from_date)->addWeeks($i + 1)->toDateString())
                    ->whereDate('from_date', '<=', Carbon::parse($thru_date)->toDateString())
                    ->where('membership_type_id', '=', '1')
                    ->groupBy('user_id')
                    ->get()
                    ->count();

                //Membership Renewal
                $renewal_pp18[$i] = ContinueMembership::whereHas('continuemembership_user', function ($q) {
                    $q->where('club_id', '=', Auth::user()->club_id);
                })->where('status', '=', 'Active')
                    ->whereDate('from_date', '>=', Carbon::parse($from_date)->addWeeks($i)->toDateString())
                    ->whereDate('from_date', '<=', Carbon::parse($from_date)->addWeeks($i + 1)->toDateString())
                    ->whereDate('from_date', '<=', Carbon::parse($thru_date)->toDateString())
                    ->where('membership_type_id', '=', '4')
                    ->get()
                    ->count();

                $renewal_pp12[$i] = ContinueMembership::whereHas('continuemembership_user', function ($q) {
                    $q->where('club_id', '=', Auth::user()->club_id);
                })->where('status', '=', 'Active')
                    ->whereDate('from_date', '>=', Carbon::parse($from_date)->addWeeks($i)->toDateString())
                    ->whereDate('from_date', '<=', Carbon::parse($from_date)->addWeeks($i + 1)->toDateString())
                    ->whereDate('from_date', '<=', Carbon::parse($thru_date)->toDateString())
                    ->where(function ($r) {
                        $r->where('membership_type_id', '=', '3')
                            ->orWhere('membership_type_id', '=', '8');
                    })
                    ->get()
                    ->count();

                $renewal_ad12[$i] = ContinueMembership::whereHas('continuemembership_user', function ($q) {
                    $q->where('club_id', '=', Auth::user()->club_id);
                })->where('status', '=', 'Active')
                    ->whereDate('from_date', '>=', Carbon::parse($from_date)->addWeeks($i)->toDateString())
                    ->whereDate('from_date', '<=', Carbon::parse($from_date)->addWeeks($i + 1)->toDateString())
                    ->whereDate('from_date', '<=', Carbon::parse($thru_date)->toDateString())
                    ->where(function ($r) {
                        $r->where('membership_type_id', '=', '2')
                            ->orWhere('membership_type_id', '=', '7');
                    })
                    ->get()
                    ->count();

                $renewal_st12[$i] = ContinueMembership::whereHas('continuemembership_user', function ($q) {
                    $q->where('club_id', '=', Auth::user()->club_id);
                })->where('status', '=', 'Active')
                    ->whereDate('from_date', '>=', Carbon::parse($from_date)->addWeeks($i)->toDateString())
                    ->whereDate('from_date', '<=', Carbon::parse($from_date)->addWeeks($i + 1)->toDateString())
                    ->whereDate('from_date', '<=', Carbon::parse($thru_date)->toDateString())
                    ->where('membership_type_id', '=', '6')
                    ->get()
                    ->count();

                $renewal_st6[$i] = ContinueMembership::whereHas('continuemembership_user', function ($q) {
                    $q->where('club_id', '=', Auth::user()->club_id);
                })->where('status', '=', 'Active')
                    ->whereDate('from_date', '>=', Carbon::parse($from_date)->addWeeks($i)->toDateString())
                    ->whereDate('from_date', '<=', Carbon::parse($from_date)->addWeeks($i + 1)->toDateString())
                    ->whereDate('from_date', '<=', Carbon::parse($thru_date)->toDateString())
                    ->where('membership_type_id', '=', '5')
                    ->get()
                    ->count();

                $renewal_mm[$i] = ContinueMembership::whereHas('continuemembership_user', function ($q) {
                    $q->where('club_id', '=', Auth::user()->club_id);
                })->where('status', '=', 'Active')
                    ->whereDate('from_date', '>=', Carbon::parse($from_date)->addWeeks($i)->toDateString())
                    ->whereDate('from_date', '<=', Carbon::parse($from_date)->addWeeks($i + 1)->toDateString())
                    ->whereDate('from_date', '<=', Carbon::parse($thru_date)->toDateString())
                    ->where('membership_type_id', '=', '1')
                    ->get()
                    ->count();
            endif;
        endfor;

        //Individual Staff Assessment
        $staffs = User::where('club_id', '=', Auth::user()->club_id)
            ->where('role_id', '<>', '7')
            ->orderBy('name', 'asc')
            ->get();

        foreach ($staffs as $staff):
            for ($i = 0; $i < 5; $i++):
                if (Carbon::parse($from_date)->addWeeks($i) < Carbon::parse($thru_date)):
                    $assessment_pp18[$staff->id][$i] = LogMemberStatus::select('user_id')
                        ->where('club_id', '=', Auth::user()->club_id)
                        ->whereHas('logmemberstatus_user', function ($q) use ($staff) {
                            $q->whereHas('user_memberpersonal', function ($r) use ($staff) {
                                $r->where('sales_person_id', '=', $staff->id);
                            });
                        })->whereDate('from_date', '>=', Carbon::parse($from_date)->addWeeks($i)->toDateString())
                        ->whereDate('from_date', '<', Carbon::parse($from_date)->addWeeks($i + 1)->toDateString())
                        ->whereDate('from_date', '<=', Carbon::parse($thru_date)->toDateString())
                        ->where('membership_type_id', '=', '4')
                        ->get()
                        ->count();

                    $assessment_pp12[$staff->id][$i] = LogMemberStatus::select('user_id')
                        ->where('club_id', '=', Auth::user()->club_id)
                        ->whereHas('logmemberstatus_user', function ($q) use ($staff) {
                            $q->whereHas('user_memberpersonal', function ($r) use ($staff) {
                                $r->where('sales_person_id', '=', $staff->id);
                            });
                        })->whereDate('from_date', '>=', Carbon::parse($from_date)->addWeeks($i)->toDateString())
                        ->whereDate('from_date', '<', Carbon::parse($from_date)->addWeeks($i + 1)->toDateString())
                        ->whereDate('from_date', '<=', Carbon::parse($thru_date)->toDateString())
                        ->where(function ($s) {
                            $s->where('membership_type_id', '=', '3')
                                ->orWhere('membership_type_id', '=', '8');
                        })
                        ->get()
                        ->count();

                    $assessment_ad12[$staff->id][$i] = LogMemberStatus::select('user_id')
                        ->where('club_id', '=', Auth::user()->club_id)
                        ->whereHas('logmemberstatus_user', function ($q) use ($staff) {
                            $q->whereHas('user_memberpersonal', function ($r) use ($staff) {
                                $r->where('sales_person_id', '=', $staff->id);
                            });
                        })->whereDate('from_date', '>=', Carbon::parse($from_date)->addWeeks($i)->toDateString())
                        ->whereDate('from_date', '<', Carbon::parse($from_date)->addWeeks($i + 1)->toDateString())
                        ->whereDate('from_date', '<=', Carbon::parse($thru_date)->toDateString())
                        ->where(function ($s) {
                            $s->where('membership_type_id', '=', '2')
                                ->orWhere('membership_type_id', '=', '7');
                        })
                        ->get()
                        ->count();

                    $assessment_st12[$staff->id][$i] = LogMemberStatus::select('user_id')
                        ->where('club_id', '=', Auth::user()->club_id)
                        ->whereHas('logmemberstatus_user', function ($q) use ($staff) {
                            $q->whereHas('user_memberpersonal', function ($r) use ($staff) {
                                $r->where('sales_person_id', '=', $staff->id);
                            });
                        })->whereDate('from_date', '>=', Carbon::parse($from_date)->addWeeks($i)->toDateString())
                        ->whereDate('from_date', '<', Carbon::parse($from_date)->addWeeks($i + 1)->toDateString())
                        ->whereDate('from_date', '<=', Carbon::parse($thru_date)->toDateString())
                        ->where('membership_type_id', '=', '6')
                        ->get()
                        ->count();

                    $assessment_st6[$staff->id][$i] = LogMemberStatus::select('user_id')
                        ->where('club_id', '=', Auth::user()->club_id)
                        ->whereHas('logmemberstatus_user', function ($q) use ($staff) {
                            $q->whereHas('user_memberpersonal', function ($r) use ($staff) {
                                $r->where('sales_person_id', '=', $staff->id);
                            });
                        })->whereDate('from_date', '>=', Carbon::parse($from_date)->addWeeks($i)->toDateString())
                        ->whereDate('from_date', '<', Carbon::parse($from_date)->addWeeks($i + 1)->toDateString())
                        ->whereDate('from_date', '<=', Carbon::parse($thru_date)->toDateString())
                        ->where('membership_type_id', '=', '5')
                        ->get()
                        ->count();

                    $assessment_mm[$staff->id][$i] = LogMemberStatus::select('user_id')
                        ->where('club_id', '=', Auth::user()->club_id)
                        ->whereHas('logmemberstatus_user', function ($q) use ($staff) {
                            $q->whereHas('user_memberpersonal', function ($r) use ($staff) {
                                $r->where('sales_person_id', '=', $staff->id);
                            });
                        })->whereDate('from_date', '>=', Carbon::parse($from_date)->addWeeks($i)->toDateString())
                        ->whereDate('from_date', '<', Carbon::parse($from_date)->addWeeks($i + 1)->toDateString())
                        ->whereDate('from_date', '<=', Carbon::parse($thru_date)->toDateString())
                        ->where('membership_type_id', '=', '1')
                        ->get()
                        ->count();

                    $assessment_ns[$staff->id][$i] = GuestAppointment::where('club_id', '=', Auth::user()->club_id)
                        ->where('staff_id', '=', $staff->id)
                        ->where('guest_status_after', '=', 'Lead')
                        ->whereDate('fa_date', '>=', Carbon::parse($from_date)->addWeeks($i)->toDateString())
                        ->whereDate('fa_date', '<', Carbon::parse($from_date)->addWeeks($i + 1)->toDateString())
                        ->whereDate('fa_date', '<=', Carbon::parse($thru_date)->toDateString())
                        ->whereHas('guestappointment_gastatus', function ($q) {
                            $q->where('status_id', '=', '3')
                                ->orWhere('status_id', '=', '4')
                                ->orWhere('status_id', '=', '6');
                        })->get()->count();
                endif;
            endfor;
        endforeach;

        //Management Added 30052018
        $managements = CAManagement::where('club_id', '=', Auth::user()->club_id)
            ->whereDate('ca_date', '>=', Carbon::parse($from_date)->toDateString())
            ->whereDate('ca_date', '<=', Carbon::parse($thru_date)->toDateString())
            ->get();

        //FWO Weekly
        $fwo_weekly = [];
        for ($i = 0; $i < 5; $i++):
            if (Carbon::parse($from_date)->addWeeks($i) < Carbon::parse($thru_date)):
                $fwo_weekly[$i] = User::where('club_id', '=', Auth::user()->club_id)
                    ->where('role_id', '=', '7')
                    ->whereHas('user_logmemberstatus', function ($q) use ($from_date, $i) {
                        $q->whereDate('from_date', '<=', Carbon::parse($from_date)->addWeeks($i)->toDateString())
                            ->whereDate('thru_date', '>=', Carbon::parse($from_date)->addWeeks($i-1)->toDateString());
                    })
                    ->whereHas('user_workout', function ($r) use ($from_date, $i) {
                        $r->whereDate('workout_date', '>=', Carbon::parse($from_date)->toDateString())
                            ->whereDate('workout_date', '<=', Carbon::parse($from_date)->addWeeks($i)->toDateString())
                            ->whereNotIn('user_id', DB::table('workouts')
                                ->WhereDate('workout_date', '<', Carbon::parse($from_date)->addWeeks($i - 1))
                                ->pluck('user_id'));
                    })
                    ->get()
                    ->count();
            endif;
        endfor;

        //Didn't FWO Weekly
        $not_fwo_weekly = [];
        for ($i = 0; $i < 5; $i++):
            if (Carbon::parse($from_date)->addWeeks($i) < Carbon::parse($thru_date)):
                $not_fwo_weekly[$i] = User::where('club_id', '=', Auth::user()->club_id)
                    ->where('role_id', '=', '7')
                    ->whereHas('user_logmemberstatus', function ($q) use ($from_date, $i) {
                        $q->whereDate('from_date', '<=', Carbon::parse($from_date)->addWeeks($i)->toDateString());

                        if ($i = 0):
                            $q->whereDate('thru_date', '>=', Carbon::parse($from_date)->toDateString());
                        else:
                            $q->whereDate('thru_date', '>=', Carbon::parse($from_date)->addWeeks($i-1)->toDateString());
                        endif;
                    })
                    ->whereDoesntHave('user_workout')
                    ->get()
                    ->count();
            endif;
        endfor;

        return view('cf_lite.document.club-assesment.ca-report', compact('tm', 'tam', 'total_fa', 'show_up_guests', 'sale_fas', 'member_collective', 'member_hold', 'free_membership',
            'total_wo', 'total_wo_this_month', 'total_wo_weekly', 'total_cancellation_weekly', 'total_cancellation', 'cancellation_bfwo', 'total_cancellation_bfwo', 'cancellation_afwo',
            'total_cancellation_afwo', 'cancellation_pp18', 'total_cancellation_pp18', 'cancellation_pp12', 'total_cancellation_pp12', 'cancellation_ad12', 'total_cancellation_ad12',
            'cancellation_st12', 'total_cancellation_st12', 'cancellation_st6', 'total_cancellation_st6', 'cancellation_mm', 'total_cancellation_mm', 'total_wm',
            'wo_flags', 'sources', 'member_from_source', 'attending_guest', 'new_sales_pp18', 'new_sales_pp12', 'new_sales_ad12', 'new_sales_st12', 'new_sales_st6', 'new_sales_mm',
            'renewal_pp18', 'renewal_pp12', 'renewal_ad12', 'renewal_st12', 'renewal_st6', 'renewal_mm', 'staffs', 'assessment_pp18', 'assessment_pp12', 'assessment_ad12',
            'assessment_st12', 'assessment_st6', 'assessment_mm', 'assessment_ns', 'managements', 'fwos', 'fwo_weekly', 'not_fwo_weekly'));
    }

    public function set_management($date, Request $request) {
        $from_date = new Carbon('first day of '.Carbon::parse($date)->format('F Y'));
        $thru_date = new Carbon('last day of '.Carbon::parse($date)->format('F Y'));

        $managements = CAManagement::where('club_id', '=', Auth::user()->club_id)
            ->whereDate('ca_date', '>=', Carbon::parse($from_date)->toDateString())
            ->whereDate('ca_date', '<=', Carbon::parse($thru_date)->toDateString())
            ->get();

        if (count($managements) > 0):
            foreach ($managements as $management):
                $to_delete = CAManagement::find($management->id);
                $to_delete->delete();
            endforeach;
        endif;

        //Weekly Staff Meeting
        if (!empty($request->wsm)):
            $wsms = $request->wsm;
            foreach ($wsms as $wsm):
                $to_add = new CAManagement;
                $to_add->club_id = Auth::user()->club_id;
                $to_add->type = 'wsm';
                $to_add->code = $wsm;
                $to_add->ca_date = $date;
                $to_add->save();
            endforeach;
        endif;

        //Club Analysis Report Completed
        if (!empty($request->carc)):
            $carcs = $request->carc;
            foreach ($carcs as $carc):
                $to_add = new CAManagement;
                $to_add->club_id = Auth::user()->club_id;
                $to_add->type = 'carc';
                $to_add->code = $carc;
                $to_add->ca_date = $date;
                $to_add->save();
            endforeach;
        endif;

        //Ongoing Guest Production, Planning and Meeting
        if (!empty($request->ogppam)):
            $ogppams = $request->ogppam;
            foreach ($ogppams as $ogppam):
                $to_add = new CAManagement;
                $to_add->club_id = Auth::user()->club_id;
                $to_add->type = 'ogppam';
                $to_add->code = $ogppam;
                $to_add->ca_date = $date;
                $to_add->save();
            endforeach;
        endif;

        //Ongoing Staff and Management Training in the Club
        if (!empty($request->osamtitc)):
            $osamtitcs = $request->osamtitc;
            foreach ($osamtitcs as $osamtitc):
                $to_add = new CAManagement;
                $to_add->club_id = Auth::user()->club_id;
                $to_add->type = 'osamtitc';
                $to_add->code = $osamtitc;
                $to_add->ca_date = $date;
                $to_add->save();
            endforeach;
        endif;

        //Reporting Projection Sheets Including No Sales
        if (!empty($request->rpsins)):
            $rpsinss = $request->rpsins;
            foreach ($rpsinss as $rpsins):
                $to_add = new CAManagement;
                $to_add->club_id = Auth::user()->club_id;
                $to_add->type = 'rpsins';
                $to_add->code = $rpsins;
                $to_add->ca_date = $date;
                $to_add->save();
            endforeach;
        endif;

        //Follows Travel/Transfer and Cancellation Policies
        if (!empty($request->fttacp)):
            $fttacps = $request->fttacp;
            foreach ($fttacps as $fttacp):
                $to_add = new CAManagement;
                $to_add->club_id = Auth::user()->club_id;
                $to_add->type = 'fttacp';
                $to_add->code = $fttacp;
                $to_add->ca_date = $date;
                $to_add->save();
            endforeach;
        endif;

        //No Unaothirized Classes or Equipment
        if (!empty($request->nucoe)):
            $nucoes = $request->nucoe;
            foreach ($nucoes as $nucoe):
                $to_add = new CAManagement;
                $to_add->club_id = Auth::user()->club_id;
                $to_add->type = 'nucoe';
                $to_add->code = $nucoe;
                $to_add->ca_date = $date;
                $to_add->save();
            endforeach;
        endif;

        //No Unaothirized Workout Schemes
        if (!empty($request->nuws)):
            $nuwss = $request->nuws;
            foreach ($nuwss as $nuws):
                $to_add = new CAManagement;
                $to_add->club_id = Auth::user()->club_id;
                $to_add->type = 'nuws';
                $to_add->code = $nuws;
                $to_add->ca_date = $date;
                $to_add->save();
            endforeach;
        endif;

        //No Unaothirized Supplements or Products
        if (!empty($request->nusop)):
            $nusops = $request->nusop;
            foreach ($nusops as $nusop):
                $to_add = new CAManagement;
                $to_add->club_id = Auth::user()->club_id;
                $to_add->type = 'nusop';
                $to_add->code = $nusop;
                $to_add->ca_date = $date;
                $to_add->save();
            endforeach;
        endif;

        //No Unaothirized Advertising or Logo Usage
        if (!empty($request->nuaolu)):
            $nuaolus = $request->nuaolu;
            foreach ($nuaolus as $nuaolu):
                $to_add = new CAManagement;
                $to_add->club_id = Auth::user()->club_id;
                $to_add->type = 'nuaolu';
                $to_add->code = $nuaolu;
                $to_add->ca_date = $date;
                $to_add->save();
            endforeach;
        endif;

        //Manager/Team Leader/Coaches Attend Corporate Training
        if (!empty($request->mtlcact)):
            $to_add = new CAManagement;
            $to_add->club_id = Auth::user()->club_id;
            $to_add->type = 'mtlcact';
            $to_add->code = $request->mtlcact;
            $to_add->ca_date = $date;
            $to_add->save();
        endif;

        //PJ, CA, Report & Royalty on Time
        if (!empty($request->pcrarot)):
            $to_add = new CAManagement;
            $to_add->club_id = Auth::user()->club_id;
            $to_add->type = 'pcrarot';
            $to_add->code = $request->pcrarot;
            $to_add->ca_date = $date;
            $to_add->save();
        endif;

        //Action Plan & Calendar
        if (!empty($request->apac)):
            $to_add = new CAManagement;
            $to_add->club_id = Auth::user()->club_id;
            $to_add->type = 'apac';
            $to_add->code = $request->apac;
            $to_add->ca_date = $date;
            $to_add->save();
        endif;

        //Club Cleanliness/Equipment Maintained/Rotated
        if (!empty($request->ccemr)):
            $to_add = new CAManagement;
            $to_add->club_id = Auth::user()->club_id;
            $to_add->type = 'ccemr';
            $to_add->code = $request->ccemr;
            $to_add->ca_date = $date;
            $to_add->save();
        endif;

        //Individual Staff Meeting (Season)
        if (!empty($request->isms)):
            $to_add = new CAManagement;
            $to_add->club_id = Auth::user()->club_id;
            $to_add->type = 'isms';
            $to_add->code = $request->isms;
            $to_add->ca_date = $date;
            $to_add->save();
        endif;

        return back();
    }
}
