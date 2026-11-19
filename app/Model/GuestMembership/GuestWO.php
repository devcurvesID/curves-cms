<?php

namespace App\Model\GuestMembership;

use Illuminate\Database\Eloquent\Model;

class GuestWO extends Model
{
    protected $table = 'guest_wos';

    protected $fillable = [
        'guest_id',
        'guest_membership_member_id',
        'wo_date',
        'club_id',
    ];

    //belongsTo
    public function guestwo_guest() {
        return $this->belongsTo('App\Model\Guest\BuddyReferralLead', 'guest_id');
    }

    public function guestwo_club() {
        return $this->belongsTo('App\Model\Club\ClubInfo', 'club_id');
    }

    public function guestwo_guestmembershipmember(){
        return $this->belongsTo('App\Model\GuestMembership\GuestMembershipMember','guest_membership_member_id');
    }
}
