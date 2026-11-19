<?php

namespace App\Http\Controllers;

use App\Model\Guest\BuddyReferralLead;
use App\Model\MasterData\MembershipType;
use Illuminate\Http\Request;

class PreMemberController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('wm-only');
    }

    public function index() {
        $premembers = BuddyReferralLead::where('status_gm', 'Guest')
            ->whereHas('buddyreferrallead_guestappointment', function ($q) {
                $q->where('guest_status_after', '=', 'Lead')
                    ->whereHas('guestappointment_gastatus', function ($r) {
                        $r->where('status_id', '=', '5')
                            ->whereNotNull('sa_date');
                    });
            })->orderBy('created_at', 'desc');

        $premembers_json = $premembers->get();
        $premembers = $premembers->simplePaginate(12);

        return view('cf_lite.pre-member.pre-member-list', compact('premembers', 'premembers_json'));
    }

    public function premember_choose_membership($id) {
        session(['premember' => $id]);

        $user = BuddyReferralLead::findOrFail($id);

//        if (!empty($user->user_membercorporation)):
//            $membership_types = MembershipType::where('membership_type', '=', 'Corporate')->orderBy('membership_types', 'asc')->simplePaginate(9);
//        else:
//            $membership_types = MembershipType::where('membership_type', '<>', 'Corporate')->orderBy('membership_types', 'asc')->simplePaginate(9);
//        endif;

        $membership_types = MembershipType::where('membership_type', '<>', 'Staff')->orderBy('membership_types', 'asc')->simplePaginate(9);

        return view('cf_lite.pre-member.membership-type-list', compact('membership_types'));
    }
}
