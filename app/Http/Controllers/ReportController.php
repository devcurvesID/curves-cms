<?php

namespace App\Http\Controllers;

use App\Model\Appointment\GuestAppointment;
use App\Model\Appointment\GuestAppointmentNote;
use App\Model\Appointment\GuestAppointmentStatus;
use App\Model\Club\ClubInfo;
use App\Model\Club\ClubServiceFee;
use App\Model\Guest\BuddyReferralLead;
use App\Model\Guest\Referrer;
use App\Model\Locker\LockerPayment;
use App\Model\Log\LogMemberStatus;
use App\Model\Log\RewardHistory;
use App\Model\MasterData\Challenge;
use App\Model\MasterData\MembershipType;
use App\Model\MasterData\MemberSource;
use App\Model\MasterData\NoSaleReason;
use App\Model\MasterData\Size;
use App\Model\Member\MemberHold;
use App\Model\Member\MemberPersonal;
use App\Model\Member\MemberStatus;
use App\Model\Payment\MemberPayment;
use App\Model\Product\BrokenProduct;
use App\Model\Product\ProductInventory;
use App\Model\Product\ProductPayment;
use App\Model\Product\ProductPaymentDetail;
use App\Model\Refund\CancellationReason;
use App\Model\Refund\RefundSubmission;
use App\Model\Staff\StaffAttendance;
use App\Model\WM\WeighMeasure;
use App\Model\Workout\Workout;
use App\Model\Workout\WoCountAvg;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Session;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('wm-only');
    }

    public function index()
    {
        return view('cf_lite.laporan.index');
    }

    public function birthday()
    {
        if (Auth::user()) {
            $user = Auth::user();
            $club_id = $user->club_id;
            $club = ClubInfo::findOrFail($club_id);

            $members = User::where('role_id', '=', '7')
                ->where('club_id', '=', $club_id)
                ->whereHas('user_memberpersonal')
                ->whereHas('user_memberstatus', function ($q) {
                    $q->where('status', '=', 'Active')
                        ->orWhere('status', '=', 'Hold');
                })
                ->get();
            return view('cf_lite.laporan.birthday', compact('members', 'club'));
        } else {
            return redirect('login');
        }
    }

    public function staff_birthday(){
        if (Auth::user()) {
            $user = Auth::user();
            $club_id = $user->club_id;
            $club = ClubInfo::findOrFail($club_id);

            $members = User::where('role_id', '<>', '7')
                ->where('club_id', '=', $club_id)
                ->whereHas('user_memberpersonal')
                ->where(function ($q) {
                    $q->whereDoesntHave('user_staffinactive')
                        ->orWhere(function ($r) {
                            $r->whereHas('user_staffinactive', function ($s) {
                                $s->where('status', '=', 'Reactivated')
                                    ->where('thru_date', null);
                            });
                        });
                })
                ->get();
            return view('cf_lite.laporan.birthday', compact('club','members'));
        } else {
            return redirect('login');
        }
    }

    public function list_wo()
    {
        if (Auth::user()) {
            $user = Auth::user();
            $club_id = $user->club_id;
            $club = ClubInfo::findOrFail($club_id);

            // $list_wo = Workout::whereHas('workout_user', function ($r) use ($club_id) {
            //     $r->where('role_id', '=', '7')->where('club_id', '=', $club_id);
            // })->orderBy('workout_date', 'DESC')->get();

//             $list_wo = User::whereHas('user_workout',function($q) use ($club_id){
//                $q->where('club_id','=',$club_id);
//             })->get();

            $list_wo = User::whereHas('user_workout')
                ->where('club_id', '=', $user->club_id)
                ->get();

             foreach($list_wo as $user){
                $total = 0;
                foreach ($user->user_workout as $workout) {
                    $total++;
                }
                $user->setAttribute('total_wo',$total);
            }

            return view('cf_lite.laporan.wo', compact('list_wo', 'club'));
        } else {
            return redirect('login');
        }
    }

    public function list_wo_filter(Request $request)
    {
        $this->validate($request, [
            'from_date' => 'required|date',
            'thru_date' => 'required|date|after_or_equal:from_date',
        ]);
        if (Auth::user()) {
            $user = Auth::user();
            $club_id = $user->club_id;
            $club = ClubInfo::findOrFail($club_id);
            $filter1 = $request->from_date;
            $filter2 = $request->thru_date;


            // $list_wo = Workout::whereDate('workout_date', '>=', $filter1)->whereDate('workout_date', '<=', $filter2)->whereHas('workout_user', function ($r) use ($club_id) {
            //     $r->where('role_id', '=', '7')->where('club_id', '=', $club_id);
            // })->orderBy('workout_date', 'DESC')->get();

            $list_wo = User::whereHas('user_workout',function($q) use ($club_id, $filter1, $filter2){
                $q->where('club_id','=',$club_id)
                    ->whereDate('workout_date', '>=', $filter1)
                    ->whereDate('workout_date', '<=', $filter2);
             })->get();

            foreach($list_wo as $user){
                $total = 0;
                foreach ($user->user_workout as $workout) {
                    $date = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $workout->workout_date)->format('Y-m-d');
                    if($filter1 <= $date && $filter2 >= $date){
                        $total++;
                    }
                }
                $user->setAttribute('total_wo',$total);
            }

            return view('cf_lite.laporan.wo', compact('list_wo', 'club', 'filter1', 'filter2'));
        } else {
            return redirect('login');
        }
    }

    public function list_wo_filter_member($id, Request $request)
    {
        if (Auth::user()) {
            $user = Auth::user();
            $club_id = $user->club_id;
            $club = ClubInfo::findOrFail($club_id);

            $the_member = User::findOrFail($id);

            if($request->from_date == '' && $request->thru_date == ''){
                $list_wo = Workout::where('user_id', '=', $id)->whereHas('workout_user', function ($r) use ($club_id) {
                    $r->where('club_id', '=', $club_id);
                })->orderBy('workout_date', 'DESC')->get();
            }else if($request->from_date != '' && $request->thru_date == ''){
                $list_wo = Workout::where('user_id', '=', $id)->where('workout_date','>=',$request->from_date)->whereHas('workout_user', function ($r) use ($club_id) {
                    $r->where('club_id', '=', $club_id);
                })->orderBy('workout_date', 'DESC')->get();
            }else if($request->from_date == '' && $request->thru_date != ''){
                $list_wo = Workout::where('user_id', '=', $id)->where('workout_date','<=',$request->thru_date)->whereHas('workout_user', function ($r) use ($club_id) {
                    $r->where('club_id', '=', $club_id);
                })->orderBy('workout_date', 'DESC')->get();
            }else if($request->from_date != '' && $request->thru_date != ''){
                $list_wo = Workout::where('user_id', '=', $id)->where('workout_date','>=',$request->from_date)->where('workout_date','<=',$request->thru_date)->whereHas('workout_user', function ($r) use ($club_id) {
                    $r->where('club_id', '=', $club_id);
                })->orderBy('workout_date', 'DESC')->get();
            }

            return view('cf_lite.laporan.wo_user', compact('list_wo', 'club', 'the_member'));
        } else {
            return redirect('login');
        }
    }

    public function list_appointment()
    {
        if (Auth::user()) {
            $user = Auth::user();
            $club_id = $user->club_id;
            $club = ClubInfo::findOrFail($club_id);

            $list_appointment = GuestAppointmentStatus::whereHas('gastatus_guestappointment', function ($r) use ($club_id) {
                $r->where('club_id', '=', $club_id);
            })->get();
            return view('cf_lite.laporan.appointment', compact('list_appointment', 'club'));
        } else {
            return redirect('login');
        }
    }

    public function list_member()
    {
        if (Auth::user()) {
            $club_id = Auth::user()->club_id;
            $club = ClubInfo::findOrFail($club_id);

//            $list_members = User::where('club_id', '=', $club_id)
//                ->whereHas('user_memberstatus')
//                ->get();

//            $list_members = User::where('club_id', '=', $club_id)
//                ->where('role_id', '=', '7')
//                ->whereHas('user_memberstatus')
//                ->leftJoin('member_payments', function ($join) {
//                    $join->on('member_payments.user_id', '=', 'users.id')
//                        ->selectRaw('SUM(rest_of_bill) AS member_debt')
//                        ->where('payment_status', '<>', 'Paid')
//                        ->whereDate('payment_date', '<=', Carbon::now()->toDateString());
//                })
//                ->leftJoin('member_personals', function ($personal) {
//                    $personal->on('member_personals.user_id', '=', 'users.id');
//                })
//                ->join('member_status', function ($personal) {
//                    $personal->on('member_status.user_id', '=', 'users.id')
//                        ->where('status', '=', 'Active')
//                        ->orWhere('status', '=', 'Hold');
//                })
//                ->get();

            $list_members = User::where('club_id', '=', $club_id)
                ->where('role_id', '=', '7')
                ->whereHas('user_memberstatus', function ($q) {
                    $q->where('status', '=', 'Active')
                        ->orWhere('status', '=', 'Hold');
                })
                ->orderBy('name', 'ASC')
                ->get();

            foreach ($list_members as $member):
                $workout = Workout::where('user_id', '=', $member->id)->get();
                if (count($workout) > 0):
                    $member->setAttribute('fwo', $workout[0]->workout_date);
                    $member->setAttribute('last_wo', $workout[count($workout) - 1]->workout_date);
                endif;

//                $membership_type = MembershipType::find($member->membership_type_id);
//                $member->setAttribute('membership_type', $membership_type->membership_type_name);

                $member_payment = MemberPayment::selectRaw('SUM(rest_of_bill) AS member_debt')
                    ->where('user_id', '=', $member->id)
                    ->where('payment_status', '<>', 'Paid')
                    ->whereDate('payment_date', '<=', Carbon::now()->toDateString())
                    ->get();
                $member->setAttribute('member_debt', $member_payment);
            endforeach;

            return view('cf_lite.laporan.member-v2', compact('list_members', 'club'));
        } else {
            return redirect('login');
        }
    }

    public function list_member_filter($filter)
    {
        if (Auth::user()) {
            $user = Auth::user();
            $club_id = $user->club_id;
            $club = ClubInfo::findOrFail($club_id);
            if ($filter == "semua") {
                $list_members = User::where('club_id', '=', $club_id)->whereHas('user_memberstatus')->get();
            } else if ($filter == "active") {
                $list_members = User::where('club_id', '=', $club_id)->whereHas('user_memberstatus', function ($r) {
                    $r->where('status', '=', "Active");
                })->get();
            } else {
                $list_members = User::where('club_id', '=', $club_id)->whereHas('user_memberstatus', function ($r) {
                    $r->where('status', '=', "Inactive");
                })->get();
            }
            return view('cf_lite.laporan.member', compact('list_members', 'club', 'filter'));
        } else {
            return redirect('login');
        }
    }

    public function list_staff_selling()
    {
        if (Auth::user()) {
            $user = Auth::user();
            $club_id = $user->club_id;
            $club = ClubInfo::findOrFail($club_id);

            $staffs = User::where('role_id', '<>', '7')->pluck('name', 'id');

            $list_appointment = GuestAppointmentStatus::whereNotNull('sa_date')->whereHas('gastatus_guestappointment', function ($r) use ($club_id) {
                $r->where('club_id', '=', $club_id);
            })->get();
            return view('cf_lite.laporan.staff_selling', compact('list_appointment', 'club', 'staffs'));
        } else {
            return redirect('login');
        }
    }

    public function list_staff_selling_filter($staff, $filter1, $filter2)
    {
        if (Auth::user()) {
            $user = Auth::user();
            $club_id = $user->club_id;
            $club = ClubInfo::findOrFail($club_id);

            $staffs = User::where('role_id', '<>', '7')->pluck('name', 'id');

            $query = GuestAppointmentStatus::whereNotNull('sa_date');
            if ($filter1 != "0") {
                $query = $query->whereDate('sa_date', '>=', $filter1);
            }

            if ($filter2 != "0") {
                $query = $query->whereDate('sa_date', '<=', $filter2);
            }

            $list_appointment = $query->whereHas('gastatus_guestappointment', function ($r) use ($club_id, $staff) {
                if ($staff == "0") {
                    $r->where('club_id', '=', $club_id);
                } else {
                    $r->where('club_id', '=', $club_id)->where('staff_id', '=', $staff);
                }
            })->get();

            return view('cf_lite.laporan.staff_selling', compact('list_appointment', 'club', 'staffs', 'staff', 'filter1', 'filter2'));
        } else {
            return redirect('login');
        }
    }


    public function list_comparison_wm(Request $request)
    {
        if (Auth::user()) {
            $user = Auth::user();
            $club_id = $user->club_id;
            $club = ClubInfo::findOrFail($club_id);

            $the_user = User::where('club_id', '=', $club_id);
            $list_user_wm = array();

            // Cek Status
            $status = $request->status ?? 'Active';

            if ($status == 'Semua') {
            } else {
                $the_user->whereHas('user_memberstatus', function ($q) use ($status) {
                    $q->where('status', '=', $status);
                });
            }
            $the_user = $the_user->get();

            $count = 0;
            foreach ($the_user as $x) {
                $user_wm = array();
                $user_id = $x->id;

                $first_user_wm = WeighMeasure::where('user_id', '=', $user_id)->select('id', 'weight', 'total_measurement', 'user_id')->orderBy('wm_date', 'ASC')->first();
                if ($first_user_wm == "" || $first_user_wm == null) {

                } else {
                    $last_user_wm = WeighMeasure::where('user_id', '=', $user_id)->select('id', 'weight', 'total_measurement', 'user_id')->orderBy('wm_date', 'DESC')->first();
//                    array_push($user_wm, $first_user_wm, $last_user_wm);
                    $user_wm[0] = $first_user_wm;
                    $user_wm[1] = $last_user_wm;

                    $list_user_wm[$count] = $user_wm;
                    $count++;
                }
            }

//            $list_wm = WeighMeasure::whereHas('weighmeasure_member', function ($r) use ($club_id) {
//                $r->where('club_id', '=', $club_id);
//            })->groupBy('user_id', 'id')->get();
            return view('cf_lite.laporan.comparison_wm', compact('list_user_wm', 'club','status'));
        } else {
            return redirect('login');
        }
    }

    public function choose_challenge()
    {
        if (Auth::user()) {
            $user = Auth::user();
            $club_id = $user->club_id;
            $club = ClubInfo::findOrFail($club_id);

            $list_challenge = Challenge::all()->pluck('challenge', 'id');
            return view('cf_lite.laporan.challenge', compact('list_challenge', 'club'));
        } else {
            return redirect('login');
        }
    }

    public function result_challenge($challenge, $filter1, $filter2)
    {

        if (Auth::user()) {
            $user = Auth::user();
            $club_id = $user->club_id;
            $club = ClubInfo::findOrFail($club_id);

            $the_challenge = Challenge::findOrFail($challenge);
            $list_challenge = Challenge::all()->pluck('challenge', 'id');

            if ($the_challenge->type == "day") {
                $variable = $the_challenge->variable;

                if ($filter1 == "0" && $filter2 == "0") {
                    $select = Workout::orderBy('user_id', 'ASC')->get();
                } else if ($filter1 != "0" && $filter2 == "0") {
                    $select = Workout::whereHas('workout_user', function ($r) use ($filter1) {
                        $r->whereHas('user_memberpersonal', function ($x) use ($filter1) {
                            $x->whereDate('joined', '>=', $filter1);
                        });
                    })->orderBy('user_id', 'ASC')->get();
                } else if ($filter1 == "0" && $filter2 != "0") {
                    $select = Workout::whereHas('workout_user', function ($r) use ($filter2) {
                        $r->whereHas('user_memberpersonal', function ($x) use ($filter2) {
                            $x->whereDate('joined', '<=', $filter2);
                        });
                    })->orderBy('user_id', 'ASC')->get();
                } else {
                    $select = Workout::whereHas('workout_user', function ($r) use ($filter1, $filter2) {
                        $r->whereHas('user_memberpersonal', function ($x) use ($filter1, $filter2) {
                            $x->whereDate('joined', '>=', $filter1)->whereDate('joined', '<=', $filter2);
                        });
                    })->orderBy('user_id', 'ASC')->get();
                }
                $wo = new Workout;
                $wo->user_id = 0;
                $wo->workout_date = Carbon::now();

                $temp_user = 0;
                $count_wo = 0;
                $first = true;
                $query_child = array();
                $query = array();
                $count = 0;
                $join_date_thirty = null;

                $select->push($wo);
                foreach ($select as $x) {
                    if ($temp_user == $x->user_id) {
                        if ($join_date_thirty >= $x->workout_date) {
                            $count_wo++;
                        }
                    } else {
                        if ($first == true) {
                            $first = false;
                        } else {
                            $query_child['user_id'] = $temp_user;
                            $query_child['user_name'] = User::findOrFail($temp_user);
                            $query_child['total'] = $count_wo;

                            $query[$count] = $query_child;
                            $count++;
                        }
                        $query_child = array();
                        $temp_user = $x->user_id;
                        $join_date_thirty = Carbon::createFromFormat('Y-m-d H:i:s', $x->workout_date)->addDays($variable);
                        $count_wo = 1;
                    }
                }
//                $query = Workout::select('user_id', DB::raw('count(workout_date) as total'))->groupBy('user_id')->whereHas('workout_user', function ($r) use ($filter1, $filter2) {
//                    $r->whereHas('user_memberpersonal', function ($x) use ($filter1, $filter2) {
//                        if ($filter1 != "0") {
//                            $x->whereDate('joined', '>=', $filter1);
//                        }
//
//                        if ($filter2 != "0") {
//                            $x->whereDate('joined', '<=', $filter2);
//                        }
//                    });
//                });
                $result = $query;
            } else if ($the_challenge->type == "workout") {
                if ($filter1 == "0" && $filter2 == "0") {
                    $query = Workout::select('user_id', DB::raw('count(*) as total'))->groupBy('user_id')->orderBy('user_id');
                } else if ($filter1 != "0" && $filter2 == "0") {
                    $query = Workout::select('user_id', DB::raw('count(*) as total'))->whereHas('workout_user', function ($r) use ($filter1) {
                        $r->whereHas('user_memberpersonal', function ($x) use ($filter1) {
                            $x->whereDate('joined', '>=', $filter1);
                        });
                    })->groupBy('user_id')->orderBy('user_id');
                } else if ($filter1 == "0" && $filter2 != "0") {
                    $query = Workout::select('user_id', DB::raw('count(*) as total'))->whereHas('workout_user', function ($r) use ($filter2) {
                        $r->whereHas('user_memberpersonal', function ($x) use ($filter2) {
                            $x->whereDate('joined', '<=', $filter2);
                        });
                    })->groupBy('user_id')->orderBy('user_id');
                } else {
                    $query = Workout::select('user_id', DB::raw('count(*) as total'))->whereHas('workout_user', function ($r) use ($filter1, $filter2) {
                        $r->whereHas('user_memberpersonal', function ($x) use ($filter1, $filter2) {
                            $x->whereDate('joined', '>=', $filter1)->whereDate('joined', '<=', $filter2);
                        });
                    })->groupBy('user_id')->orderBy('user_id');
                }

                $result = $query->get();
            } else if ($the_challenge->type == "br") {
                if ($filter1 == "0" && $filter2 == "0") {
                    $query = Referrer::select('user_id', DB::raw('count(*) as total'))->where('status', '=', 'Joined')->groupBy('user_id')->orderBy('user_id');
                } else if ($filter1 != "0" && $filter2 == "0") {
                    $query = Referrer::select('user_id', DB::raw('count(*) as total'))->where('status', '=', 'Joined')->whereHas('user_referrer', function ($r) use ($filter1) {
                        $r->whereHas('user_memberpersonal', function ($x) use ($filter1) {
                            $x->whereDate('joined', '>=', $filter1);
                        });
                    })->groupBy('user_id')->orderBy('user_id');
                } else if ($filter1 == "0" && $filter2 != "0") {
                    $query = Referrer::select('user_id', DB::raw('count(*) as total'))->where('status', '=', 'Joined')->whereHas('user_referrer', function ($r) use ($filter2) {
                        $r->whereHas('user_memberpersonal', function ($x) use ($filter2) {
                            $x->whereDate('joined', '<=', $filter2);
                        });
                    })->groupBy('user_id')->orderBy('user_id');
                } else {
                    $query = Referrer::select('user_id', DB::raw('count(*) as total'))->where('status', '=', 'Joined')->whereHas('user_referrer', function ($r) use ($filter1, $filter2) {
                        $r->whereHas('user_memberpersonal', function ($x) use ($filter1, $filter2) {
                            $x->whereDate('joined', '>=', $filter1)->whereDate('joined', '<=', $filter2);
                        });
                    })->groupBy('user_id')->orderBy('user_id');
                }

                $result = $query->get();
            }

            return view('cf_lite.laporan.challenge_result', compact('list_challenge', 'club', 'result', 'the_challenge', 'filter1', 'filter2'));
        } else {
            return redirect('login');
        }
    }


    public function list_reward()
    {
        if (Auth::user()) {
            $user = Auth::user();
            $club_id = $user->club_id;
            $club = ClubInfo::findOrFail($club_id);
            $list_challenge = Challenge::all()->pluck('challenge', 'id');

            $result = RewardHistory::whereHas('rewardhistory_user', function ($q) use ($club_id) {
                $q->where('club_id', '=', $club_id);
            })->get();
            return view('cf_lite.laporan.reward', compact('result', 'list_challenge', 'club'));
        } else {
            return redirect('login');
        }
    }

    public function list_reward_filter($challenge, $filter1, $filter2)
    {
        if (Auth::user()) {
            $user = Auth::user();
            $club_id = $user->club_id;
            $club = ClubInfo::findOrFail($club_id);
            $list_challenge = Challenge::all()->pluck('challenge', 'id');
            $query = RewardHistory::whereNotNull('created_at')
                ->whereHas('rewardhistory_user', function ($q) use ($club_id) {
                    $q->where('club_id', '=', $club_id);
                });
            if ($challenge != "0") {
                $query = $query->where('challenge_id', '=', $challenge);
            }
            if ($filter1 != "0") {
                $query = $query->whereDate('created_at', '>=', $filter1);
            }
            if ($filter2 != "0") {
                $query = $query->whereDate('created_at', '<=', $filter2);
            }
            $result = $query->get();
            return view('cf_lite.laporan.reward', compact('result', 'list_challenge', 'club', 'challenge', 'filter1', 'filter2'));
        } else {
            return redirect('login');
        }
    }

    public function list_cancellation()
    {
        if (Auth::user()) {
            $user = Auth::user();
            $club_id = $user->club_id;
            $club = ClubInfo::findOrFail($club_id);
//            $list_reason = NoSaleReason::all()->pluck('nosale_reason', 'id');

            $first_date_of_month = Carbon::now()->startOfMonth();
            $last_date_of_month = Carbon::now()->endOfMonth();

            $query = CancellationReason::select('cancellation_reason_id', DB::raw('count(*) as total'))->whereHas('cancellationreason_refundsubmission', function ($r) use ($first_date_of_month, $last_date_of_month) {
                $r->where('refund_status', '=', 'Paid')->whereDate('refund_process_date', '>=', $first_date_of_month)->whereDate('refund_process_date', '<=', $last_date_of_month);
                $r->whereHas('refundsubmission_user', function($s){
                        $s->where('club_id','=',Auth::user()->club_id);
                    });
            })->groupBy('cancellation_reason_id')->orderBy('cancellation_reason_id');

            $result = $query->get();
            $filter1 = $first_date_of_month;
            $filter2 = $last_date_of_month;
            return view('cf_lite.laporan.cancellation', compact('result', 'club', 'filter1', 'filter2'));
        } else {
            return redirect('login');
        }
    }

    public function list_cancellation_filter($id, $filter1, $filter2)
    {
        if (Auth::user()) {
            $user = Auth::user();
            $club_id = $user->club_id;
            $club = ClubInfo::findOrFail($club_id);

            if ($id == "0") {
                $query = CancellationReason::select('cancellation_reason_id', DB::raw('count(*) as total'))->whereHas('cancellationreason_refundsubmission', function ($r) use ($filter1, $filter2) {
                    $r->whereHas('refundsubmission_user', function($s){
                        $s->where('club_id','=',Auth::user()->club_id);
                    });
                    if ($filter1 != "0" && $filter2 != "0") {
                        $r->where('refund_status', '=', 'Paid')->whereDate('refund_process_date', '>=', $filter1)->whereDate('refund_process_date', '<=', $filter2);
                    } else if ($filter1 == "0" && $filter2 != "0") {
                        $r->where('refund_status', '=', 'Paid')->whereDate('refund_process_date', '<=', $filter2);
                    } else if ($filter1 != "0" && $filter2 == "0") {
                        $r->where('refund_status', '=', 'Paid')->whereDate('refund_process_date', '>=', $filter1);
                    } else {
                        $r->where('refund_status', '=', 'Paid');
                    }
                })->groupBy('cancellation_reason_id')->orderBy('cancellation_reason_id');

                $result = $query->get();
                return view('cf_lite.laporan.cancellation', compact('result', 'club', 'filter1', 'filter2'));
            } else {
                $query = CancellationReason::select('cancellation_reason_id', DB::raw('count(*) as total'))->whereHas('cancellationreason_refundsubmission', function ($r) use ($filter1, $filter2) {
                    $r->whereHas('refundsubmission_user', function($s){
                        $s->where('club_id','=',Auth::user()->club_id);
                    });
                    if ($filter1 != "0" && $filter2 != "0") {
                        $r->where('refund_status', '=', 'Paid')->whereDate('refund_process_date', '>=', $filter1)->whereDate('refund_process_date', '<=', $filter2);
                    } else if ($filter1 == "0" && $filter2 != "0") {
                        $r->where('refund_status', '=', 'Paid')->whereDate('refund_process_date', '<=', $filter2);
                    } else if ($filter1 != "0" && $filter2 == "0") {
                        $r->where('refund_status', '=', 'Paid')->whereDate('refund_process_date', '>=', $filter1);
                    } else {
                        $r->where('refund_status', '=', 'Paid');
                    }
                    $r->whereHas('refundsubmission_user', function($s){
                        $s->where('club_id','=',Auth::user()->club_id);
                    });
                })->groupBy('cancellation_reason_id')->orderBy('cancellation_reason_id');

                $list_top = $query->get();

                $query = RefundSubmission::where('refund_status', '=', 'Paid')
                    ->whereHas('refundsubmission_user', function ($s) {
                        $s->where('club_id', '=', Auth::user()->club_id);
                    });
                if ($filter1 != "0") {
                    $query = $query->whereDate('refund_process_date', '>=', $filter1);
                }
                if ($filter2 != "0") {
                    $query = $query->whereDate('refund_process_date', '<=', $filter2);
                }

                $query = $query->whereHas('refundsubmission_cancellationreason', function ($r) use ($id) {
                    $r->where('cancellation_reason_id', '=', $id);
                });

                $result = $query->get();

                foreach($result as $x){
                    $selectedUser = User::findOrFail($x->refundsubmission_user->user_memberpersonal->sales_person_id);
                    $membership = MembershipType::findOrFail($x->refundsubmission_user->user_memberstatus->membership_type_id);
                    $x->setAttribute('fastaff',$selectedUser->name);
                    $x->setAttribute('membershiptype',$membership->membership_type_name);
                }

                return view('cf_lite.laporan.cancellation_detail', compact('result', 'list_top', 'club', 'id', 'filter1', 'filter2'));
            }
        } else {
            return redirect('login');
        }
    }

    public function member_hold_list() {
        $club = ClubInfo::findOrFail(Auth::user()->club_id);

        $member_holds = MemberHold::where('club_id', '=', $club->id)
            ->whereDate('from_date', '<=', Carbon::now()->toDateString())
            ->whereDate('thru_date', '>=', Carbon::now()->toDateString())
            ->where('status', '=', 'OnHold')
            ->orderBy('from_date', 'asc')
            ->paginate(100);

        return view('cf_lite.laporan.member-hold', compact('member_holds', 'club'));
    }

    public function member_hold_filter(Request $request) {
        $club = ClubInfo::findOrFail(Auth::user()->club_id);
        $date = $request->date;

        $member_holds = MemberHold::where(function ($q) use ($club, $date) {
            $q->where('club_id', '=', $club->id)
                ->whereDate('from_date', '<=', Carbon::parse($date)->toDateString())
                ->whereDate('thru_date', '>=', Carbon::parse($date)->toDateString())
                ->where('status', '=', 'OnHold');
        })
            ->orWhere(function ($r) use ($club, $date) {
                $r->where('club_id', '=', $club->id)
                    ->whereDate('from_date', '<=', Carbon::parse($date)->toDateString())
                    ->whereDate('thru_date', '>=', Carbon::parse($date)->toDateString())
                    ->where('status', '=', 'Finished');
            })
            ->orderBy('from_date', 'asc')
            ->paginate(100);

        return view('cf_lite.laporan.member-hold', compact('member_holds', 'club', 'date'));
    }

    //Modified 24102018
    public function absent_report_list(Request $request) {
        $club = ClubInfo::findOrFail(Auth::user()->club_id);

        $members = User::where('club_id', '=', Auth::user()->club_id)
            ->where('role_id', '=', '7')
            ->whereHas('user_memberstatus', function ($q) {
                $q->where('status', '=', 'Active');
            })
            ->get();

        if (!empty($request->absence)):
            $absence = $request->absence;
        else:
            $absence = 7;
        endif;

        foreach ($members as $member):
            $absent = Workout::where('user_id', '=', $member->id)
                ->orderBy('workout_date', 'desc')
                ->first();

            if ($absent):
                if (strtotime($absent->workout_date) < strtotime(Carbon::now()->subDays($absence))):
                    $member->setAttribute('workout_date', $absent->workout_date);
                endif;
            endif;
        endforeach;

        return view('cf_lite.laporan.absent-member', compact('members', 'club', 'absence'));
    }

    public function absent_member_filter(Request $request) {
        $this->validate($request, [
            'absence' => 'required|numeric',
        ]);

        $club = ClubInfo::findOrFail(Auth::user()->club_id);
        $absence = $request->absence;

        $members = User::where('club_id', '=', Auth::user()->club_id)
            ->where('role_id', '=', '7')
            ->whereHas('user_memberstatus', function ($q) {
                $q->where('status', '=', 'Active');
            })
            ->paginate(100);

        foreach ($members as $member):
            $absent = Workout::where('user_id', '=', $member->id)
                ->orderBy('workout_date', 'desc')
                ->first();

            if (count($absent) > 0):
                if (strtotime($absent->workout_date) < strtotime(Carbon::now()->subDays($absence))):
                    $member->setAttribute('workout_date', $absent->workout_date);
                endif;
            endif;
        endforeach;

        return view('cf_lite.laporan.absent-member', compact('members', 'club', 'absence'));
    }

    public function list_fa_succes_staff(){
        if (Auth::user()) {
            $user = Auth::user();
            $club_id = $user->club_id;
            $club = ClubInfo::findOrFail($club_id);


            $staff_fas = User::where('role_id','<>','7')->get();

            //Success fa count
            foreach($staff_fas as $user){
                $total = 0;
                foreach ($user->sales_memberpersonal as $sales) {
                    if($sales->memberpersonal_user->role_id == 7){
                        $total++;
                    }
                }
                $user->setAttribute('success_count',$total);
            }

            //Fail fa count
            foreach($staff_fas as $user){
                $total = 0;
                foreach ($user->user_guestappointment as $sales) {
                    if($sales->guest_status_after == 'Lead' && $sales->fa_date < \Carbon\Carbon::now()){
                        $total++;
                    }
                }
                $user->setAttribute('fail_count',$total);
            }

            return view('cf_lite.laporan.staff-fa', compact('staff_fas', 'club'));
        } else {
            return redirect('login');
        }
    }

    public function staff_fa_date_filter(Request $request){
        $this->validate($request, [
            'from_date' => 'required|date',
            'thru_date' => 'required|date|after_or_equal:from_date',
        ]);
        if (Auth::user()) {
            $user = Auth::user();
            $club_id = $user->club_id;
            $club = ClubInfo::findOrFail($club_id);
            $from_date = $request->from_date;
            $thru_date = $request->thru_date;

            $staff_fas = User::where('role_id','<>','7')->get();

            //Success fa count
            foreach($staff_fas as $user){
                $total = 0;
                foreach ($user->sales_memberpersonal as $sales) {
                    if($sales->memberpersonal_user->role_id == 7){
                        foreach ($sales->memberpersonal_buddyreferrallead->buddyreferrallead_guestappointment as $ga) {
                            if($ga->guest_status_after == 'Member' && $ga->fa_date >= $from_date && $ga->fa_date <= $thru_date){
                                $total++;
                            }
                        }
                    }
                }
                $user->setAttribute('success_count',$total);
            }

            //Fail fa count
            foreach($staff_fas as $user){
                $total = 0;
                foreach ($user->user_guestappointment as $sales) {
                    if($sales->guest_status_after == 'Lead' && $sales->fa_date >= $from_date && $sales->fa_date <= $thru_date){
                        $total++;
                    }
                }
                $user->setAttribute('fail_count',$total);
            }

            return view('cf_lite.laporan.staff-fa', compact('staff_fas', 'club','from_date','thru_date'));
        } else {
            return redirect('login');
        }
    }

    public function traffic_control_main(Request $request) {
        if (empty($request->mode) || ($request->mode == 'Sale')):
            $payments = MemberPayment::whereHas('memberpayment_user', function ($q) {
                $q->where('club_id', '=', Auth::user()->club_id);
            })
                ->where('payment_status', '<>', 'Unpaid')
                ->where('payment_category', '=', 'First');

            if (!empty($request->date)):
                $payments->whereDate('payment_date', '=', Carbon::parse($request->date)->toDateString());
            else:
                $payments->whereDate('payment_date', '=', Carbon::today()->toDateString());
            endif;

            $payments = $payments->orderBy('created_at', 'asc')->get();
        elseif (!empty($request->mode) || ($request->mode == 'NoSale')):
            $payments = GuestAppointment::where('club_id', '=', Auth::user()->club_id)
                ->where('guest_status_after', '=', 'Lead')
                ->whereHas('guestappointment_gastatus', function ($q) {
                    $q->where('status_id', '=', '6')
                        ->orWhere('status_id', '=', '4');
                });

            if (!empty($request->date)):
                $payments->whereDate('fa_date', '=', Carbon::parse($request->date)->toDateString());
            else:
                $payments->whereDate('fa_date', '=', Carbon::today()->toDateString());
            endif;

            $payments = $payments->orderBy('fa_date', 'asc')->get();
        endif;

        foreach ($payments as $payment):
            $guest_id = $payment->memberpayment_user->user_memberpersonal->guest_id;

            $total_appt = GuestAppointment::where('guest_id', '=', $guest_id);
            if (!empty($request->date)):
                $real_date = $request->date;
                $total_appt->whereDate('fa_date', '<', Carbon::parse($request->date)->toDateString());
//                $total_appt->where(function($q) use ($real_date) {
//                    $q->where('fa_status', '=', 'FA')
//                        ->whereDate('fa_date', '<=', Carbon::parse($real_date)->toDateString());
//                })
//                    ->orWhere(function($r) {
//                        $r->where('fa_status', '=', 'NonFA');
//                    });

                if (!empty($payment->memberpayment_memberdebtpayment)):
                    $owed = 0;
                    foreach ($payment->memberpayment_memberdebtpayment as $debt):
                        if (strtotime($debt->payment_date) > strtotime($request->date)):
                            $owed = $owed + $debt->amount_paid;
                        endif;
                    endforeach;
                    $payment->setAttribute('owed', $owed);
                endif;
            else:
                $real_date = Carbon::now()->format('Y-m-d');
                $total_appt->whereDate('fa_date', '<', Carbon::today()->toDateString());
//                $total_appt->where(function($q) use ($real_date) {
//                    $q->where('fa_status', '=', 'FA')
//                        ->whereDate('fa_date', '<=', Carbon::parse($real_date)->toDateString());
//                })
//                    ->orWhere(function($r) {
//                        $r->where('fa_status', '=', 'NonFA');
//                    });
            endif;

            $total_appt = $total_appt->get();

            if (count($total_appt) > 0):
                $payment->setAttribute('past_day', 'Yes');
            endif;

            $csf = ClubServiceFee::where('club_id', '=', Auth::user()->club_id)
                ->where('membership_type_id', '=', $payment->memberpayment_user->user_memberstatus->membership_type_id)
                ->whereDate('from_date', '<=', Carbon::parse($real_date)->toDateString())
                ->where('thru_date', null)
                ->orderBy('from_date', 'desc')
                ->first();

            $money_owed = $csf->mf_amount * ($payment->memberpayment_user->user_memberstatus->memberstatus_membershiptype->duration - 1);
            $payment->setAttribute('money_owed', $money_owed);
        endforeach;

        //FA Sources
        $appt_date = Carbon::today()->format('Y-m-d');
        if (!empty($request->date)):
            $appt_date = $request->date;
        endif;
        $fa_sources = BuddyReferralLead::where('club_id', '=', Auth::user()->club_id)
            ->whereHas('buddyreferrallead_guestappointment', function ($q) use ($appt_date) {
                $q->whereDate('fa_date', '=', Carbon::parse($appt_date)->toDateString())
                    ->whereHas('guestappointment_gastatus');
        })
            ->get();


        //Sale today
        $sales = MemberStatus::where('status', '<>', 'Inactive')
            ->where('status', '<>', 'Stopped')
            ->whereDate('from_date', '=', Carbon::parse($appt_date)->toDateString())
            ->whereHas('memberstatus_user', function ($q) {
                $q->where('club_id', '=', Auth::user()->club_id);
            })
            ->get();

        $sources = MemberSource::all();
        $membership_types = MembershipType::where('membership_type', '<>', 'Staff')->get();

        return view('cf_lite.laporan.traffic-control-main', compact('payments', 'sources', 'membership_types', 'fa_sources', 'sales'));
    }

    public function fa_source_main(Request $request) {
        $sources = MemberSource::orderBy('source', 'asc')->get();

        $fas = GuestAppointment::where('club_id', '=', Auth::user()->club_id);

        if (!empty($request->f) && empty($request->t)):
            $this->validate($request, [
                'f' => 'required|date',
            ]);

            $from_date = $request->f;
            $fas->whereDate('fa_date', '=', Carbon::parse($from_date)->toDateString())
                ->orWhere(function ($q) use ($from_date) {
                    $q->whereDate('fa_date', '=', Carbon::parse($from_date)->subDay()->toDateString())
                        ->where('fa_hour', '=', Carbon::createFromFormat('H:i:s', Carbon::parse($from_date)->format('H:i:s'), session('timezone'))->setTimezone('UTC')->toTimeString());
                });
        elseif (!empty($request->f) && !empty($request->t)):
            $this->validate($request, [
                'f' => 'required|date',
                't' => 'required|date',
            ]);

            $from_date = $request->f;
            $thru_date = $request->t;
            $fas->where(function($x) use ($from_date) {
                $x->whereDate('fa_date', '>=', Carbon::parse($from_date)->toDateString())
                    ->orWhere(function ($q) use ($from_date) {
                        $q->whereDate('fa_date', '=', Carbon::parse($from_date)->subDay()->toDateString())
                            ->where('fa_hour', '>=', Carbon::createFromFormat('H:i:s', Carbon::parse($from_date)->format('H:i:s'), session('timezone'))->setTimezone('UTC')->toTimeString());
                    });
            })
                ->where(function ($y) use ($thru_date) {
                    $y->whereDate('fa_date', '<', Carbon::parse($thru_date)->toDateString())
                        ->orWhere(function ($r) use ($thru_date) {
                            $r->whereDate('fa_date', '=', Carbon::parse($thru_date)->subDay()->toDateString())
                                ->where('fa_hour', '>=', Carbon::createFromFormat('H:i:s', Carbon::parse($thru_date)->format('H:i:s'), session('timezone'))->setTimezone('UTC')->toTimeString());
                        });
                });
        endif;

        $fas = $fas->get();

        return view('cf_lite.laporan.fa-source', compact('sources', 'fas'));
    }

    public function fa_source_nosale_reason(Request $request) {
        $sources = MemberSource::orderBy('source', 'asc')->get();
        $nosale_reasons = NoSaleReason::all();

        $fas = GuestAppointment::where('club_id', '=', Auth::user()->club_id)
            ->whereHas('guestappointment_gastatus', function ($q) {
                $q->where('status_id', '=', '6');
            });

        if (!empty($request->f) && empty($request->t)):
            $this->validate($request, [
                'f' => 'required|date',
            ]);

            $from_date = $request->f;
            $fas->whereDate('fa_date', '=', Carbon::parse($from_date)->toDateString())
                ->orWhere(function ($q) use ($from_date) {
                    $q->whereDate('fa_date', '=', Carbon::parse($from_date)->subDay()->toDateString())
                        ->where('fa_hour', '=', Carbon::createFromFormat('H:i:s', Carbon::parse($from_date)->format('H:i:s'), session('timezone'))->setTimezone('UTC')->toTimeString());
                });
        elseif (!empty($request->f) && !empty($request->t)):
            $this->validate($request, [
                'f' => 'required|date',
                't' => 'required|date',
            ]);

            $from_date = $request->f;
            $thru_date = $request->t;
            $fas->where(function($x) use ($from_date) {
                $x->whereDate('fa_date', '>=', Carbon::parse($from_date)->toDateString())
                    ->orWhere(function ($q) use ($from_date) {
                        $q->whereDate('fa_date', '=', Carbon::parse($from_date)->subDay()->toDateString())
                            ->where('fa_hour', '>=', Carbon::createFromFormat('H:i:s', Carbon::parse($from_date)->format('H:i:s'), session('timezone'))->setTimezone('UTC')->toTimeString());
                    });
            })
                ->where(function ($y) use ($thru_date) {
                    $y->whereDate('fa_date', '<', Carbon::parse($thru_date)->toDateString())
                        ->orWhere(function ($r) use ($thru_date) {
                            $r->whereDate('fa_date', '=', Carbon::parse($thru_date)->subDay()->toDateString())
                                ->where('fa_hour', '>=', Carbon::createFromFormat('H:i:s', Carbon::parse($thru_date)->format('H:i:s'), session('timezone'))->setTimezone('UTC')->toTimeString());
                        });
                });
        endif;

        $fas = $fas->get();

        return view('cf_lite.laporan.fa-no-sale-reason', compact('sources', 'fas', 'nosale_reasons'));
    }

    public function fa_source_cancel(Request $request) {
        $sources = MemberSource::orderBy('source', 'asc')->get();
        $rss = RefundSubmission::where('refund_status','=','Paid')->whereHas('refundsubmission_user',function($q){
            $q->whereHas('user_memberpersonal',function($r){
                $r->whereHas('memberpersonal_buddyreferrallead',function($s){
                    $s->whereHas('buddyreferrallead_membersource');
                });
            });
        });

        if (!empty($request->f) && empty($request->t)){
            $this->validate($request, [
                'f' => 'required|date',
            ]);

            $from_date = $request->f;
            $rss->whereDate('refund_process_date','>=',$from_date);
        }else if(empty($request->f) && !empty($request->t)){
            $this->validate($request, [
                't' => 'required|date',
            ]);

            $thru_date = $request->t;
            $rss->whereDate('refund_process_date','<=',$thru_date);
        }else if (!empty($request->f) && !empty($request->t)){
            $this->validate($request, [
                'f' => 'required|date',
                't' => 'required|date',
            ]);

            $from_date = $request->f;
            $thru_date = $request->t;
            $rss->whereDate('refund_process_date','<=',$thru_date)->whereDate('refund_process_date','>=',$from_date);
        }

        $rss = $rss->get();

        foreach ($sources as $source) {
            $source->setAttribute('source_counter',0);
            foreach ($rss as $rs) {
                if($source->id == $rs->refundsubmission_user->user_memberpersonal->memberpersonal_buddyreferrallead->source_id){
                    $source->source_counter += 1;
                }
            }
        }

        return view('cf_lite.laporan.fa-source-cancel', compact('sources', 'rss'));
    }

    public function wo_time_list_check(){

        $wib_zone = ['1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','26','27','28'];

        $wita_zone = ['25'];
        $wit_zone = [];

        $wo_times = Workout::selectRaw('club_id, workout_date, extract(HOUR from workout_date) as wohour')->whereHas('workout_user', function($q){
                $q->where('club_id','=',Auth::user()->club_id);
            })->get();

        $counter = [];

        for ($i=0; $i < 24 ; $i++) {
            $counter[$i] = 0;
        }

        foreach ($wo_times as $wo_time) {

            //if(session('timezone') == 'Asia/Jakarta'){
            if(in_array(Auth::user()->club_id, $wib_zone)){
                $time = $wo_time->wohour + 7;
                if($time == 7){
                    $time = 0;
                }
                if($time>=24){
                    $counter[$time-24] += 1;
                }else{
                    $counter[$time] += 1;
                }
            //}else if(session('timezone') == 'Asia/Makassar'){
            }else if(in_array(Auth::user()->club_id, $wita_zone)){

                $time = $wo_time->wohour + 8;
                if($time == 8){
                    $time = 0;
                }
                if($time>=24){
                    $counter[$time-24] += 1;
                }else{
                    $counter[$time] += 1;
                }
            //}else if(session('timezone') == 'Asia/Jayapura'){
            }else if(!empty($wit_zone) && in_array(Auth::user()->club_id, $wit_zone)){
                $time = $wo_time->wohour + 9;
                if($time == 9){
                    $time = 0;
                }
                if($time>=24){
                    $counter[$time-24] += 1;
                }else{
                    $counter[$time] += 1;
                }
            }

        }
        echo json_encode($wo_times);
    }
    //WO-time report
    public function wo_time_list(){
        $wib_zone = ['1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','26','27','28'];

        $wita_zone = ['25'];
        $wit_zone = [];


        $wo_times = Workout::selectRaw('extract(HOUR from workout_date) as wohour')->whereHas('workout_user',function($q){
            $q->where('club_id','=',Auth::user()->club_id);
        })->get();

        $counter = [];

        for ($i=0; $i < 24 ; $i++) {
            $counter[$i] = 0;
        }

        foreach ($wo_times as $wo_time) {

            //if(session('timezone') == 'Asia/Jakarta'){
            if(in_array(Auth::user()->club_id, $wib_zone)){
                $time = $wo_time->wohour + 7;
                if($time == 7){
                    $time = 0;
                }
                if($time>=24){
                    $counter[$time-24] += 1;
                }else{
                    $counter[$time] += 1;
                }
            //}else if(session('timezone') == 'Asia/Makassar'){
            }else if(in_array(Auth::user()->club_id, $wita_zone)){

                $time = $wo_time->wohour + 8;
                if($time == 8){
                    $time = 0;
                }
                if($time>=24){
                    $counter[$time-24] += 1;
                }else{
                    $counter[$time] += 1;
                }
            //}else if(session('timezone') == 'Asia/Jayapura'){
            }else if(!empty($wit_zone) && in_array(Auth::user()->club_id, $wit_zone)){

                $time = $wo_time->wohour + 9;
                if($time == 9){
                    $time = 0;
                }
                if($time>=24){
                    $counter[$time-24] += 1;
                }else{
                    $counter[$time] += 1;
                }
            }

        }

        // foreach ($wo_times as $wo_time) {
        //     if(Carbon::parse($wo_time['workout_date'])->format('H:i:s') >= "00:00:00" &&
        //        Carbon::parse($wo_time['workout_date'])->format('H:i:s') <= "00:59:59"){
        //         $wo_time->setAttribute('inc_time','0');
        //     }
        // }

        return view('cf_lite.laporan.wo-time',compact('wo_times','counter'));
    }

    public function wo_time_list_filter(Request $request){
        $wib_zone = ['1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','26','27','28'];

        $wita_zone = ['25'];
        $wit_zone = [];

        $this->validate($request, [
                'date_chosen' => 'required',
            ]);

        $dates = $request->date_chosen;
        $selDate = explode(" ", $dates);

        $wo_times = Workout::selectRaw('extract(HOUR from workout_date) as wohour');

        foreach($selDate as $date){

            $wo_times->orWhereDate('workout_date','=',$date);
        }
        $wo_times->whereHas('workout_user',function($q){
            $q->where('club_id','=',Auth::user()->club_id);
        });
        $wo_times = $wo_times->get();

        $counter = [];

        for ($i=0; $i < 24 ; $i++) {
            $counter[$i] = 0;
        }

        foreach ($wo_times as $wo_time) {

            //if(session('timezone') == 'Asia/Jakarta'){
            if(in_array(Auth::user()->club_id, $wib_zone)){

                $time = $wo_time->wohour + 7;
                if($time == 7){
                    $time = 0;
                }
                if($time>=24){
                    $counter[$time-24] += 1;
                }else{
                    $counter[$time] += 1;
                }
            //}else if(session('timezone') == 'Asia/Makassar'){
            }else if(in_array(Auth::user()->club_id, $wita_zone)){

                $time = $wo_time->wohour + 8;
                if($time == 8){
                    $time = 0;
                }
                if($time>=24){
                    $counter[$time-24] += 1;
                }else{
                    $counter[$time] += 1;
                }
            //}else if(session('timezone') == 'Asia/Jayapura'){
            }else if(!empty($wit_zone) && in_array(Auth::user()->club_id, $wit_zone)){

                $time = $wo_time->wohour + 9;
                if($time == 9){
                    $time = 0;
                }
                if($time>=24){
                    $counter[$time-24] += 1;
                }else{
                    $counter[$time] += 1;
                }
            }
            //added to check if there's record if timezone not set
            /* else{
                $time = $wo_time->wohour + 7;
                if($time == 7){
                    $time = 0;
                }
                if($time>24){
                    $counter[$time-24] += 1;
                }else{
                    $counter[$time] += 1;
                }
            } */
        }

        // foreach ($wo_times as $wo_time) {
        //     if(Carbon::parse($wo_time['workout_date'])->format('H:i:s') >= "00:00:00" &&
        //        Carbon::parse($wo_time['workout_date'])->format('H:i:s') <= "00:59:59"){
        //         $wo_time->setAttribute('inc_time','0');
        //     }
        // }

        return view('cf_lite.laporan.wo-time',compact('wo_times','counter','dates'));
    }

    //Membership type Report
    public function membership_type_list(){
        $membershiptypes = MembershipType::get();
        $memberstatuses = MemberStatus::where('status','=','Active')->whereHas('memberstatus_user',function($q){
            $q->where('club_id','=',Auth::user()->club_id);
        })->get();

        foreach ($membershiptypes as $membershiptype ) {
            $counter = 0;
            foreach ($memberstatuses as $memberstatus ) {
                if($membershiptype->id == $memberstatus->membership_type_id){
                    $counter++;
                }
            }
            $membershiptype->setAttribute('membership_counter',$counter);
        }

        return view('cf_lite.laporan.membership-type-report',compact('membershiptypes'));
    }

    // Added 29112019
    public function membership_type_list_v2() {
        $memberships = MembershipType::selectRaw('membership_types.id, membership_types.membership_type_name, COUNT(log_member_memberships.user_id) as membership_counter')
            ->leftJoin('log_member_memberships', function ($join) {
                $join->on('log_member_memberships.membership_type_id', '=', 'membership_types.id')
                    ->whereDate('from_date', '<=', Carbon::today()->toDateString())
                    ->whereDate('thru_date', '>=', Carbon::today()->toDateString())
                    ->where('club_id', '=', Auth::user()->club_id);
            })
            ->groupBy('membership_types.id')
            ->orderBy('membership_types.id', 'asc')
            ->get();

        return view('cf_lite.laporan.membership-type-report-v2',compact('memberships'));
    }

    public function membership_type_each_list($id){
        $membershipmembers = User::whereHas('user_memberstatus',function($q) use ($id){
            $q->where('membership_type_id','=',$id);
        })->where('club_id','=',Auth::user()->club_id)->whereHas('user_memberpersonal',function($q){

        })->get();

        $membership = MembershipType::findOrFail($id);

        return view('cf_lite.laporan.membership-type-each-report',compact('membershipmembers','membership'));
    }

    public function membership_type_list_filter(Request $request){
        $this->validate($request, [
                'f' => 'required|date',
                't' => 'required|date',
            ]);

        $membershiptypes = MembershipType::get();
        $memberstatuses = MemberStatus::where('status','=','Active')->whereHas('memberstatus_user',function($q){
            $q->where('club_id','=',Auth::user()->club_id);
        })->where('from_date','>=',$request->f)->where('from_date','<=',$request->t)->get();

        foreach ($membershiptypes as $membershiptype ) {
            $counter = 0;
            foreach ($memberstatuses as $memberstatus ) {
                if($membershiptype->id == $memberstatus->membership_type_id){
                    $counter++;
                }
            }
            $membershiptype->setAttribute('membership_counter',$counter);
        }

        return view('cf_lite.laporan.membership-type-report',compact('membershiptypes'));
    }

    // Added 29112019
    public function membership_type_list_filter_v2(Request $request) {
        $this->validate($request, [
            'f' => 'required|date',
            't' => 'required|date',
        ]);

        $memberships = MembershipType::selectRaw('membership_types.id, membership_types.membership_type_name, COUNT(log_member_memberships.user_id) as membership_counter')
            ->leftJoin('log_member_memberships', function ($join) use ($request) {
                $join->on('log_member_memberships.membership_type_id', '=', 'membership_types.id')
                    ->whereDate('from_date', '<=', Carbon::parse($request->t)->toDateString())
                    ->whereDate('thru_date', '>=', Carbon::parse($request->f)->toDateString())
                    ->where('club_id', '=', Auth::user()->club_id);
            })
            ->groupBy('membership_types.id')
            ->orderBy('membership_types.id', 'asc')
            ->get();

        return view('cf_lite.laporan.membership-type-report-v2',compact('memberships'));
    }

    //WO-flag
    public function wo_flag_list(Request $request){
        /*
        if(empty($request->f)){
            $wo_flags = Workout::where('workout_date','>=',Carbon::now()->subDays(30))->get();

        }else{
            $this->validate($request,[
                'f' => 'required|date',
            ]);

            $wo_flags = Workout::where('workout_date','>=',Carbon::parse($request->f)->subDays(30))->where('workout_date','<=',Carbon::parse($request->f))->get();

        }

        $users = User::whereHas('user_memberstatus',function($q){
                $q->where('status','=','Active');
            })->where('club_id','=',Auth::user()->club_id)->get();

        foreach ($users as $user) {
            $counter = 0;
            foreach ($wo_flags as $wo_flag) {
                if($user->id == $wo_flag->user_id){
                    $counter++;
                }
                $user->setAttribute('wo_count',$counter);
                if($user->wo_count >= 12){
                    $user->setAttribute('user_flag','A');
                }else if($user->wo_count >= 8){
                    $user->setAttribute('user_flag','B');
                }else if($user->wo_count >= 4){
                    $user->setAttribute('user_flag','C');
                }else if($user->wo_count >= 1){
                    $user->setAttribute('user_flag','D');
                }else if($user->wo_count >= 0){
                    $user->setAttribute('user_flag','E');
                }
            }
        }
        */

        $totalNeedA = 0;
        $totalNeedB = 0;
        $totalNeedC = 0;
        $totalNeedD = 0;
        $totalNeedE = 0;

        if(empty($request->f)){
            $wo_flags = User::selectRaw('users.id, name, COUNT(workout_date) as wo_count')->where('users.club_id','=',Auth::user()->club_id)->whereHas('user_memberstatus',function($q){
                    $q->where('status','=','Active');
            })->leftJoin('workouts',function($join){
                    $join->on('workouts.user_id','=','users.id');
                    $join->where('workout_date','>=',Carbon::now()->subDays(30));
                })
                ->where('role_id', '=', '7')
                ->groupBy('users.id', 'name')->get();

            $totalNeedA = 12;
            $totalNeedB = 8;
            $totalNeedC = 4;
            $totalNeedD = 1;
            $totalNeedE = 0;

        }else{
            $this->validate($request,[
                'f' => 'required|date',
                't' => 'required|date',
                'weeks' => 'required|numeric',
            ]);

            $inputdate = $request->f;
            $thrudate = $request->t;
            $weekcount = $request->weeks;

            // $wo_flags = User::selectRaw('users.id, name, COUNT(workout_date) as wo_count')->where('users.club_id','=',Auth::user()->club_id)->whereHas('user_memberstatus',function($q){
            //         $q->where('status','=','Active');
            // })->leftJoin('workouts',function($join) use ($inputdate,$weekcount) {
            //         $join->on('workouts.user_id','=','users.id');
            //         $join->where('workout_date','>=',Carbon::parse($inputdate));
            //         $join->where('workout_date','<=',Carbon::parse($inputdate)->addWeeks($weekcount));
            //     })
            //     ->where('role_id', '=', '7')
            //     ->groupBy('users.id', 'name')->get();

            $wo_flags = User::selectRaw('users.id, name, COUNT(workout_date) as wo_count')->where('users.club_id','=',Auth::user()->club_id)->whereHas('user_memberstatus',function($q){
                    $q->where('status','=','Active');
            })->leftJoin('workouts',function($join) use ($inputdate,$thrudate,$weekcount) {
                    $join->on('workouts.user_id','=','users.id');
                    $join->where('workout_date','>=',Carbon::parse($inputdate));
                    $join->where('workout_date','<=',Carbon::parse($thrudate));
                })
                ->where('role_id', '=', '7')
                ->groupBy('users.id', 'name')->get();

            // Jumlah workout yang dibutuhkan berdasarkan minggu
            $oneWeekA = 3;
            $oneWeekB = 2;
            $oneWeekC = 1;
            $oneWeekD = 0;
            $oneWeekE = 0;

            $twoWeekA = 6;
            $twoWeekB = 4;
            $twoWeekC = 2;
            $twoWeekD = 1;
            $twoWeekE = 0;

            $threeWeekA = 9;
            $threeWeekB = 6;
            $threeWeekC = 3;
            $threeWeekD = 1;
            $threeWeekE = 0;

            $fourWeekA = 12;
            $fourWeekB = 8;
            $fourWeekC = 4;
            $fourWeekD = 1;
            $fourWeekE = 0;
            // End jumlah workout yang dibutuhkan berdasarkan minggu

            if($weekcount % 4 == 0){
                $counter = $weekcount/4;
                $totalNeedA = $counter * $fourWeekA;
                $totalNeedB = $counter * $fourWeekB;
                $totalNeedC = $counter * $fourWeekC;
                $totalNeedD = $counter * $fourWeekD;
                $totalNeedE = $counter * $fourWeekE;
            }else if($weekcount % 4 == 1){
                $counter    = ($weekcount - 1)/4;
                $totalNeedA = $oneWeekA + ($counter * $fourWeekA);
                $totalNeedB = $oneWeekB + ($counter * $fourWeekB);
                $totalNeedC = $oneWeekC + ($counter * $fourWeekC);
                $totalNeedD = $oneWeekD + ($counter * $fourWeekD);
                $totalNeedE = $oneWeekE + ($counter * $fourWeekE);
            }else if($weekcount % 4 == 2){
                $counter    = ($weekcount - 2)/4;
                $totalNeedA = $twoWeekA + ($counter * $fourWeekA);
                $totalNeedB = $twoWeekB + ($counter * $fourWeekB);
                $totalNeedC = $twoWeekC + ($counter * $fourWeekC);
                $totalNeedD = $twoWeekD + ($counter * $fourWeekD);
                $totalNeedE = $twoWeekE + ($counter * $fourWeekE);
            }else if($weekcount % 4 == 3){
                $counter    = ($weekcount - 3)/4;
                $totalNeedA = $threeWeekA + ($counter * $fourWeekA);
                $totalNeedB = $threeWeekB + ($counter * $fourWeekB);
                $totalNeedC = $threeWeekC + ($counter * $fourWeekC);
                $totalNeedD = $threeWeekD + ($counter * $fourWeekD);
                $totalNeedE = $threeWeekE + ($counter * $fourWeekE);
            }
        }

        $countA = 0;
        $countB = 0;
        $countC = 0;
        $countD = 0;
        $countE = 0;

        foreach ($wo_flags as $user) {

            if($user->wo_count >= $totalNeedA){
                $user->setAttribute('user_flag','A');
                $countA++;
            }else if($user->wo_count >= $totalNeedB){
                $user->setAttribute('user_flag','B');
                $countB++;
            }else if($user->wo_count >= $totalNeedC){
                $user->setAttribute('user_flag','C');
                $countC++;
            }else if($user->wo_count >= $totalNeedD){
                $user->setAttribute('user_flag','D');
                $countD++;
            }else if($user->wo_count >= $totalNeedE){
                $user->setAttribute('user_flag','E');
                $countE++;
            }

            //Get membership type
            $member = User::find($user->id);
            $user->setAttribute('membership_type', $user->user_memberstatus->memberstatus_membershiptype->membership_type_name);
        }

        return view('cf_lite.laporan.wo-flag',compact('wo_flags','countA','countB','countC','countD','countE','totalNeedA','totalNeedB','totalNeedC','totalNeedD','totalNeedE'));
    }

    //WO-count-average-report
    public function wo_count_average_list(Request $request){
        if(empty($request->f) && empty($request->t)){

//            $first_wo = LogMemberStatus::where('club_id','=',Auth::user()->club_id)->orderBy('from_date','asc')->first();
            //Modified 19102018
            $first_wo = LogMemberStatus::whereHas('logmemberstatus_user', function ($q) {
                $q->where('club_id', '=', Auth::user()->club_id);
            })->orderBy('from_date', 'asc')->first();
            //Modified 19102018
            $first_wo_date = Carbon::parse($first_wo->from_date);
            $thru_wo = Carbon::now();
        }else if(!empty($request->f) && empty($request->t)){
            $this->validate($request,[
                'f' => 'required|date',
            ]);
//            $first_wo = LogMemberStatus::where('club_id','=',Auth::user()->club_id)->orderBy('from_date','asc')->first();
            //Modified 19102018
            $first_wo = LogMemberStatus::whereHas('logmemberstatus_user', function ($q) {
                $q->where('club_id', '=', Auth::user()->club_id);
            })->orderBy('from_date', 'asc')->first();
            //Modified 19102018
            $first_wo_date = Carbon::parse($first_wo->from_date);
            $input_date = Carbon::parse($request->f);
            if($input_date >= $first_wo_date){
                $first_wo_date = $input_date;
            }
            $thru_wo = Carbon::now();
        }else if(empty($request->f) && !empty($request->t)){
            $this->validate($request,[
                't' => 'required|date',
            ]);
            $input_date = Carbon::parse($request->t);
//            $first_wo = LogMemberStatus::where('club_id','=',Auth::user()->club_id)->orderBy('from_date','asc')->first();
            //Modified 19102018
            $first_wo = LogMemberStatus::whereHas('logmemberstatus_user', function ($q) {
                $q->where('club_id', '=', Auth::user()->club_id);
            })->orderBy('from_date', 'asc')->first();
            //Modified 19102018
            $first_wo_date = Carbon::parse($first_wo->from_date);
            if($input_date > $first_wo_date){
                $thru_wo = $input_date;
            }else{
                Session::flash('flash_message', trans('messages.wrong_thru_date_message'));

                return redirect('cf-lite/laporan/wo-count-avg');
            }
        }else if(!empty($request->f) && !empty($request->t)){
            $this->validate($request,[
                'f' => 'required|date',
                't' => 'required|date|after_or_equal:'.$request->f,
            ]);
//            $first_wo = LogMemberStatus::where('club_id','=',Auth::user()->club_id)->orderBy('from_date','asc')->first();
            //Modified 19102018
            $first_wo = LogMemberStatus::whereHas('logmemberstatus_user', function ($q) {
                $q->where('club_id', '=', Auth::user()->club_id);
            })->orderBy('from_date', 'asc')->first();
            //Modified 19102018
            $first_wo_date = Carbon::parse($first_wo->from_date);
            $input_date = Carbon::parse($request->f);
            $input_thru_date = Carbon::parse($request->t);
            if($input_date >= $first_wo_date){
                $first_wo_date = $input_date;
            }
            if($input_thru_date > $first_wo_date){
                $thru_wo = $input_thru_date;
            }else{
                Session::flash('flash_message', trans('messages.wrong_thru_date_message'));

                return redirect('cf-lite/laporan/wo-count-avg');
            }
        }
        $wo_count_avgs = [];
        $index = 1;
        $curTotalPersenLat = 0.0;
        $curCounter = 0;
        while($first_wo_date <= $thru_wo){
            $tempDay = '';
            switch ($first_wo_date->dayOfWeek) {
                case '0':
                    $tempDay = 'Minggu';
                    break;
                case '1':
                    $tempDay = 'Senin';
                    break;
                case '2':
                    $tempDay = 'Selasa';
                    break;
                case '3':
                    $tempDay = 'Rabu';
                    break;
                case '4':
                    $tempDay = 'Kamis';
                    break;
                case '5':
                    $tempDay = 'Jumat';
                    break;
                case '6':
                    $tempDay = 'Sabtu';
                    break;
                default:
                    $tempDay = '';
                    break;
            }
            $tempDate = Carbon::parse($first_wo_date)->format('d-m-Y');
            if($first_wo_date->dayOfWeek != 0){
                $tempCountWo = Workout::where('club_id','=',Auth::user()->club_id)
                    ->whereHas('workout_user', function ($q) {
                        $q->where('role_id', '=', '7');
                    })
                    ->whereDate('workout_date','=',Carbon::parse($first_wo_date)->toDateString())
                    ->get()
                    ->count();

//                $tempCountActiveMember = LogMemberStatus::where('club_id','=',Auth::user()->club_id)
//                    ->whereHas('logmemberstatus_user', function ($q) {
//                        $q->where('role_id', '=', '7')
//                            ->whereHas('user_memberstatus', function ($r) {
//                                $r->where('status', '=', 'Active')
//                                    ->orWhere('status', '=', 'Hold');
//                            });
//                    })
//                    ->whereDate('from_date','<=',Carbon::parse($first_wo_date))
//                    ->whereDate('thru_date','>=',Carbon::parse($first_wo_date))
//                    ->get()
//                    ->count();
                //Modified 19102018
                $tempCountActiveMember = LogMemberStatus::selectRaw('COUNT(id) as total')
                    ->whereDate('from_date','<=',Carbon::parse($first_wo_date))
                    ->whereDate('thru_date','>=',Carbon::parse($first_wo_date))
                    ->whereHas('logmemberstatus_user', function ($q) {
                        $q->where('role_id', '=', '7')
                            ->where('club_id', '=', Auth::user()->club_id)
//                            ->whereHas('user_memberstatus', function ($r) {
//                                $r->where('status', '=', 'Active')
//                                    ->orWhere('status', '=', 'Hold');
//                            });
                            ->whereHas('user_memberstatus');
                    })
                    ->groupBy('user_id')
                    ->get()
                    ->count();
                //Modified 19102018
                if($tempCountActiveMember !=0){
                    $tempWoPercent = round(($tempCountWo/$tempCountActiveMember)*100,2);
                }else{
                    $tempWoPercent = 0;
                }
                if($index > 7){
                    if($wo_count_avgs[$index-8]['day'] != 'Minggu'){
                        $curTotalPersenLat -= $wo_count_avgs[$index-8]['woPercent'];
                        $curCounter = 5;
                    }else if($wo_count_avgs[$index-8]['day'] == 'Minggu'){
                        if($index > 8){
                            $curTotalPersenLat -= $wo_count_avgs[$index-9]['woPercent'];
                            $curCounter = 5;
                        }else{

                        }
                    }
                }
                $curTotalPersenLat += $tempWoPercent;
                $curCounter += 1;
                $tempAvgPercent = round(($curTotalPersenLat/$curCounter),2);
            }else{
                $tempCountWo = 0;
                $tempCountActiveMember = 0;
                $tempWoPercent = 0.0;
                $tempAvgPercent = 0.0;
            }

            array_push($wo_count_avgs, ['day'=>$tempDay, 'date'=>$tempDate, 'countWo'=>$tempCountWo, 'activeMember'=>$tempCountActiveMember, 'woPercent'=>$tempWoPercent, 'avgPercent'=>$tempAvgPercent]);

            $index++;
            $first_wo_date->addDay();
        }

        return view('cf_lite.laporan.wo-count-average',compact('wo_count_avgs'));
    }

    // T-Shirt Size
    public function tshirt_size_list(){
        $memberpersonals = MemberPersonal::whereHas('memberpersonal_user',function($q){
            $q->whereHas('user_memberstatus',function($r){
                $r->where('status','=','Active');
                $r->orWhere('status','=','Hold');
                $r->where('thru_date','<',Carbon::now());
            });
            $q->where('club_id','=',Auth::user()->club_id);
        })->get();

        $sizes = Size::get();

        return view('cf_lite.laporan.tshirt-size',compact('memberpersonals','sizes'));
    }

    public function tshirt_size_detail($id){
        $memberpersonals = MemberPersonal::whereHas('memberpersonal_user',function($q){
            $q->whereHas('user_memberstatus',function($r){
                $r->where('status','=','Active');
                $r->orWhere('status','=','Hold');
                $r->where('thru_date','<',Carbon::now());
            });
            $q->where('club_id','=',Auth::user()->club_id);
        })->where('tshirt_size','=',$id)->get();

        $sizes = Size::findOrFail($id);

        return view('cf_lite.laporan.tshirt-size-detail',compact('memberpersonals','sizes'));
    }

    public function top_weight_loser(Request $request) {
        if (!empty($request->month) && !empty($request->year)):
            $filter_date = Carbon::createFromFormat('Y-m', Carbon::parse($request->year.'-'.$request->month)->format('Y-m'));
            $last_day = new Carbon('last day of '.Carbon::parse($filter_date)->format('F Y'));
            $first_day = new Carbon('first day of '.Carbon::parse($filter_date)->format('F Y'));
        else:
            $last_day = new Carbon('last day of this month');
            $first_day = new Carbon('first day of this month');
        endif;

        //Get User ID
        $wm_users = WeighMeasure::select('user_id')
            ->whereHas('weighmeasure_member', function ($q) {
                $q->where('club_id', '=', Auth::user()->club_id);
            })
            ->whereDate('wm_date', '<=', Carbon::parse($last_day)->toDateString())
            ->whereDate('wm_date', '>=', Carbon::parse($first_day)->toDateString())
            ->groupBy('user_id')
            ->get();

        //Get Last & Previous WM
        $user = []; $last_wm = []; $prev_wm = []; $diff_days = []; $wo = []; $last_day = [];

        if (count($wm_users) > 0):
            foreach ($wm_users as $wm_user):
                $user[$wm_user->user_id] = User::find($wm_user->user_id);

                $last_wm[$wm_user->user_id] = WeighMeasure::where('user_id', '=', $wm_user->user_id)
                    ->whereDate('wm_date', '<=', Carbon::parse($last_day)->toDateString())
                    ->orderBy('wm_date', 'desc')
                    ->first();

                $prev_wm[$wm_user->user_id] = WeighMeasure::where('user_id', '=', $wm_user->user_id)
                    ->whereDate('wm_date', '<=', Carbon::parse($last_day)->toDateString())
                    ->orderBy('wm_date', 'desc')
                    ->offset(1)
                    ->first();

                if (($last_wm[$wm_user->user_id]) && ($prev_wm[$wm_user->user_id])):
                    $diff_days[$wm_user->user_id] = Carbon::parse($prev_wm[$wm_user->user_id]->wm_date)->diffInDays(Carbon::parse($last_wm[$wm_user->user_id]->wm_date));

                    $wo[$wm_user->user_id] = Workout::where('user_id', '=', $wm_user->user_id)
                        ->whereDate('workout_date', '<=', Carbon::parse($last_wm[$wm_user->user_id]->wm_date)->toDateString())
                        ->whereDate('workout_date', '>=', Carbon::parse($prev_wm[$wm_user->user_id]->wm_date)->toDateString())
                        ->get()
                        ->count();
                else:
                    $diff_days[$wm_user->user_id] = 0;
                    $wo[$wm_user->user_id] = 0;
                endif;
            endforeach;
        endif;

        return view('cf_lite.laporan.top-weight-loser', compact('wm_users', 'user', 'last_wm', 'prev_wm', 'diff_days', 'wo', 'last_day'));
    }

    //Product Report
    public function product_report_list(){
        $product_list = ProductInventory::selectRaw('item_name, qty, SUM(quantity) AS broken_qty, (qty + SUM(quantity)) AS total')->join('mst_products',function($r){
            $r->on('product_inventories.product_id','=','mst_products.id');
        })->leftJoin('broken_products',function($join) {
            $join->on('broken_products.product_inventory_id','=','product_inventories.id');
        })
            ->where('club_id', '=', Auth::user()->club_id)
            ->groupBy('item_name','qty')
            ->get();

        foreach ($product_list as $list) {
            if(empty($list->broken_qty)){
                $list->broken_qty = 0;
                $list->total = $list->qty;
            }
        }

        $club = ClubInfo::findOrFail(Auth::user()->club_id);

        return view('cf_lite.laporan.product-report',compact('product_list','club'));

    }

    public function product_sales_report(Request $request) {
        $from_date = new Carbon('first day of this month');
        $thru_date = new Carbon('last day of this month');
        $club_id = null;

        if (!empty($request->fd)):
            $from_date = Carbon::parse($request->fd);
        endif;

        if (!empty($request->td)):
            $thru_date = Carbon::parse($request->td);
        endif;

        if (isset($request->club)):
            if (!empty($request->club)):
                $club_id = $request->club;
            endif;
        else:
            $club_id = Auth::user()->club_id;
        endif;

//        $product_sales = ProductPayment::whereHas('productpayment_productpaymentdetail')
//            ->whereDate('payment_date', '>=', Carbon::parse($from_date)->toDateString())
//            ->whereDate('payment_date', '<=', Carbon::parse($thru_date)->toDateString());
//
//        if (isset($request->club)):
//            if (!empty($request->club)):
//                $product_sales->where('club_id', '=', $request->club_id);
//                $club_id = $request->club_id;
//            endif;
//        else:
//            $product_sales->where('club_id', '=', Auth::user()->club_id);
//            $club_id = Auth::user()->club_id;
//        endif;
//
//        $product_sales = $product_sales->get();

        $product_sales = ProductPaymentDetail::whereHas('productpaymentdetail_productpayment', function ($q) use ($from_date, $thru_date, $club_id) {
            $q->whereDate('payment_date', '>=', Carbon::parse($from_date)->toDateString())
                ->whereDate('payment_date', '<=', Carbon::parse($thru_date)->toDateString());

            if (!empty($club_id)):
                $q->where('club_id', '=', $club_id);
            endif;
        })->with('productpaymentdetail_productpayment')
        ->get();

        $clubs = ClubInfo::orderBy('club_name')->pluck('club_name', 'id');

        $club = null;
        if ($club_id != null):
            $club = ClubInfo::findOrFail($club_id);
        endif;
        //dd($product_sales[0]->productpaymentdetail_productpayment->payment_number);
        return view('cf_lite.laporan.product-sales-report', compact('product_sales', 'clubs', 'club_id', 'from_date', 'thru_date', 'club'));
    }

    //Member Not FWO Yet
    public function member_not_fwo() {
//        $members = User::where('club_id', '=', Auth::user()->club_id)
//            ->where('role_id', '=', '7')
//            ->whereDoesntHave('user_workout')
//            ->orderBy('name', 'ASC')
//            ->get();

        $club_id = Auth::user()->club_id;
        $members = MemberStatus::where('status', '=', 'Active')
            ->whereHas('memberstatus_user', function ($q) use ($club_id) {
                $q->where('club_id', '=', $club_id)
                    ->where('role_id', '=', '7')
                    ->whereDoesntHave('user_workout');
            })->orderBy('from_date', 'ASC')->get();

        return view('cf_lite.laporan.member-not-fwo', compact('members'));
    }

    //fungsi untuk fitur report wo member
    public function wo_member_report()
    {
        $members = User::where('role_id','=','7')->get();
        $current_club = ClubInfo::findorFail(Auth::user()->club_id);
        $results = array();
        foreach ($members as $member)
        {
            $absent = Workout::where('club_id','=', $current_club->id)->where('user_id','=',$member->id)->whereDate('workout_date','=',Carbon::today())->orderBy('workout_date','desc')->first();
            if($absent != null)
            {
                $member->setAttribute('wo_time',$absent->workout_date);
                array_push($results,$member);
            }
        }
        foreach ($results as $result)
        {
            $club = ClubInfo::findorFail($result->club_id);
            $result->setAttribute('club_name',$club->club_name);
        }
        return view('cf_lite.laporan.wo-member-report', compact('results', 'current_club'));
    }

    public function wo_member_report_filter(Request $request)
    {
        $this->validate($request,['date' => 'required|date']);
        $current_club = ClubInfo::findorFail(Auth::user()->club_id);
        $members = User::where('role_id','=','7')->get();

        $results = array();
        $filterdate = $request->date;

        foreach ($members as $member)
        {
            $absent = Workout::where('club_id','=', $current_club->id)->where('user_id','=',$member->id)->whereDate('workout_date','=',$filterdate)->orderBy('workout_date','desc')->first();
            if($absent!=null)
            {
                $member->setAttribute('wo_time',$absent->workout_date);
                array_push($results,$member);
            }
        }
        foreach ($results as $result)
        {
            $club = ClubInfo::findorFail($result->club_id);
            $result->setAttribute('club_name',$club->club_name);
        }
        return view('cf_lite.laporan.wo-member-report', compact('results', 'current_club','filterdate'));
    }

    // Locker Sale Report
    public function locker_sale_report(Request $request){
        $clubs = ClubInfo::orderBy('id','ASC')->get();
        $club_id = Auth::user()->id;
        $club_name = '';

        if($request->club_id == 0){
            $club_name = 'Semua Klub';
            $club_id = 0;
            $lockers = LockerPayment::where('id','>',0);
        }else if(!empty($request->club_id)){
            $club_id = $request->club_id;
            $the_club = ClubInfo::findOrFail($club_id);
            $club_name = $the_club->club_name;
            $lockers = LockerPayment::join('member_lockers',function($q){
                $q->on('locker_payments.memberlocker_id','=','member_lockers.id');
            })->join('users',function($r) use($club_id){
                $r->on('member_lockers.user_id','=','users.id');
                $r->where('users.club_id','=',$club_id);
            });
        }else{
            $the_club = ClubInfo::findOrFail($club_id);
            $club_name = $the_club->club_name;
            $lockers = LockerPayment::join('member_lockers',function($q){
                $q->on('locker_payments.memberlocker_id','=','member_lockers.id');
            })->join('users',function($r)use($club_id){
                $r->on('member_lockers.user_id','=','users.id');
                $r->where('users.club_id','=',$club_id);
            });
        }

        $from_date = $request->from_date;
        $thru_date = $request->thru_date;

        if($from_date == null && $thru_date == null){
            $from_date = new Carbon('first day of this month');
            $thru_date = new Carbon('last day of this month');
        }

        $lockers->where('locker_payments.payment_date','>=',Carbon::parse($from_date))->where('locker_payments.payment_date','<=',Carbon::parse($thru_date));
        $lockers = $lockers->get();

        foreach ($lockers as $locker ) {
            $duration = $locker->lockerpayment_locker->duration;
            $times = 0;
            if($locker->lockerpayment_locker->duration_type == 'daily'){
                $times = 1;
            }else if($locker->lockerpayment_locker->duration_type == 'monthly'){
                $times = 30;
            }else if($locker->lockerpayment_locker->duration_type == 'yearly'){
                $times = 365;
            }

            $locker->setAttribute('day_duration',($duration*$times));
        }

        return view('cf_lite.laporan.locker-sales-report',compact('lockers','club_name','clubs','from_date','thru_date','club_id'));
    }

    public function member_source_report(Request $request) {
        $sources = MemberSource::orderBy('source', 'asc')->get();
        $membership_types = MembershipType::where('membership_type_name', '<>', 'Staff')->orderBy('membership_type_name', 'asc') ->get();
        $member_reports1 = '';
        $source_temp = '';
        $membership_temp = '';

        if (!empty($request->source)):
            $source_temp = $request->source;

            $member_reports1 = User::selectRaw('users.id, users.name, member_status.from_date, membership_types.membership_type_name, sum(member_payments.amount_paid) AS total_amount_paid')
                ->where('users.club_id', '=', Auth::user()->club_id)
                ->where('member_status.status', '=', 'Active')
                ->where('buddy_referral_leads.source_id', '=', $request->source)
                ->where('membership_types.membership_type_name', '<>', 'Staff');

            $member_reports1 = $member_reports1->join('member_status', function ($join)  {
                $join->on('users.id', '=', 'member_status.user_id');
            });
            $member_reports1 = $member_reports1->join('member_personals', function ($join){
                $join->on('users.id', '=', 'member_personals.user_id');
            });
            $member_reports1 = $member_reports1->join('buddy_referral_leads', function ($join){
                $join->on('member_personals.guest_id', '=', 'buddy_referral_leads.id');
            });
            $member_reports1 = $member_reports1->join('member_payments', function ($join){
                $join->on('users.id', '=', 'member_payments.user_id');
            });
            $member_reports1 = $member_reports1->join('membership_types', function ($join){
                $join->on('member_status.membership_type_id', '=', 'membership_types.id');
            });

            if (!empty($request->membership)):
                $membership_temp = $request->membership;
                $member_reports1->where('membership_types.id', '=', $request->membership);
            endif;

            if (!empty($request->from_date) || !empty($request->thru_date)):
                $member_reports1->whereHas('user_memberstatus', function ($join) use ($request) {
                    if (!empty($request->from_date)):
                        $join->whereDate('from_date', '>=', Carbon::parse($request->from_date)->toDateString());
                    endif;
                    if (!empty($request->thru_date)):
                        $join->whereDate('from_date', '<=', Carbon::parse($request->thru_date)->toDateString());
                    endif;
                });
            endif;

            $member_reports1 = $member_reports1->groupBy('users.id', 'users.name', 'member_status.from_date', 'membership_types.membership_type_name')
                ->orderBy('users.name', 'asc')
                ->get();
        endif;


        return view('cf_lite.laporan.member-source-report', compact('sources', 'membership_types', 'member_reports1', 'source_temp', 'membership_temp'));
    }

    public function member_report_to_excel(Request $request) {
        $clubs = ClubInfo::where('status', '=', 'Active')->orderBy('club_name', 'asc')->pluck('club_name', 'id');
        if (!empty($request->clubs)) {
            $member_data = User::selectRaw('
                users.name,
                member_personals.birth,
                member_personals.address,
                member_personals.cellphone,
                member_personals.phone,
                member_personals.flag,
                buddy_referral_leads.wa_number,
                CASE
                    WHEN RIGHT(users.email, 17) ILIKE \'@curvesmember.com\' THEN \'-\'
                    ELSE users.email
                END as email
            ')
                ->join('member_personals', 'users.id', '=', 'member_personals.user_id')
                ->join('buddy_referral_leads','member_personals.guest_id','=','buddy_referral_leads.id')
                ->where('users.club_id', '=', $request->clubs)
                ->where('role_id', '=', '7')
                ->orderBy('name', 'asc')
                ->get();
            return view('cf_lite.laporan.member-data-report', compact('clubs', 'member_data'));
        } else {
            return view('cf_lite.laporan.member-data-report', compact('clubs'));
        }
    }

    public function staff_attendance_report(Request $request){
        $this->validate($request,[
            'selected_date' => 'sometimes'
        ]);

        $selected_date = Carbon::today();
        if(!empty($request->selected_date)){
            $selected_date = $request->selected_date;
        }

        $club_id = Auth::user()->club_id;

        $attendances = StaffAttendance::where('club_id','=',$club_id)->where(function($q) use ($selected_date){
            $q->whereDate('sign_in','=',$selected_date);
            $q->orWhere('sign_out','=',$selected_date);
        })->get();

        return view('cf_lite.laporan.staff-attendance-report',compact('attendances','selected_date'));
    }
}
