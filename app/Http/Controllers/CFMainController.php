<?php

namespace App\Http\Controllers;

use App\Model\Appointment\GuestAppointment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CFMainController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('wm-only');
    }

    public function index() {
        if (Auth::user()->user_club->hq == true) {
            return view('cf_lite.hq-dashboard');
        } else {
            $unfinished_drafts = GuestAppointment::where('club_id', '=', Auth::user()->club_id)
                ->whereDate('fa_date', '<=', Carbon::today()->toDateString())
                ->whereHas('guestappointment_gastatus', function ($q) {
                    $q->where('status_id', '=', '3')
                        ->orWhere('status_id', '=', '5')
                        ->orWhere('status_id', '=', '6');
                })
                ->whereHas('guestappointment_buddyreferrallead', function ($r) {
                    $r->whereDoesntHave('buddyreferrallead_guestdetail');
                })
                ->get()
                ->count();

            return view('cf_lite.staff-home', compact('unfinished_drafts'));
        }
    }
}
