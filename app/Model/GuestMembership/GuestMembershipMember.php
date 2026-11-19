<?php

namespace App\Model\GuestMembership;

use Illuminate\Database\Eloquent\Model;

class GuestMembershipMember extends Model
{
    protected $table = 'guest_membership_members';

    protected $fillable = [
        'guest_id',
        'gm_id',
        'from_date',
        'thru_date',
    ];

    //hasMany
    public function gmmember_gmpayment() {
        return $this->hasMany('App\Model\GuestMembership\GuestMemberPayment', 'guest_membership_member_id');
    }

    public function gmmember_guestwo(){
        return $this->hasMany('App\Model\GuestMembership\GuestWO','guest_membership_member_id');
    }

    //belongsTo
    public function gmmember_guest() {
        return $this->belongsTo('App\Model\Guest\BuddyReferralLead', 'guest_id');
    }

    public function gmmember_gm() {
        return $this->belongsTo('App\Model\GuestMembership\GuestMembership', 'gm_id');
    }
}
