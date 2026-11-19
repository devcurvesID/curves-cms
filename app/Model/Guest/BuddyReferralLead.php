<?php

namespace App\Model\Guest;

use Illuminate\Database\Eloquent\Model;

class BuddyReferralLead extends Model
{
    protected $table = 'buddy_referral_leads';

    protected $fillable = [
        'fullname',
        'phone',
        'is_br',
        'source_id',
        'staff_id',
        'status_gm',
        'no_more_call',
        'location',
    ];

    public function buddyreferrallead_guestdetail() {
        return $this->hasOne('App\Model\Guest\GuestDetail', 'guest_id');
    }

    public function buddyreferrallead_referrer() {
        return $this->hasOne('App\Model\Guest\Referrer', 'referral_id');
    }

    public function buddyreferrallead_memberpersonal() {
        return $this->hasOne('App\Model\Member\MemberPersonal', 'guest_id');
    }

    public function buddyreferrallead_guestfat() {
        return $this->hasOne('App\Model\Guest\GuestFat', 'guest_id');
    }

    public function lead_guestmonitoring() {
        return $this->hasOne('App\Model\Guest\GuestMonitoring', 'guest_id');
    }

    public function buddyreferrallead_guestappointment() {
        return $this->hasMany('App\Model\Appointment\GuestAppointment', 'guest_id');
    }

    public function buddyreferrallead_logcontactednewlead() {
        return $this->hasMany('App\Model\Log\LogContactedNewLead', 'guest_id');
    }

    public function guest_gmmember() {
        return $this->hasMany('App\Model\GuestMembership\GuestMembershipMember', 'guest_id');
    }

    public function guest_guestwm() {
        return $this->hasMany('App\Model\GuestMembership\GuestWM', 'guest_id');
    }

    public function guest_guestwo() {
        return $this->hasMany('App\Model\GuestMembership\GuestWO', 'guest_id');
    }

    public function brl_gmdetail() {
        return $this->hasMany('App\Model\Guest\GuestMonitoringDetail', 'guest_monitoring_id');
    }

    public function buddyreferrallead_membersource() {
        return $this->belongsTo('App\Model\MasterData\MemberSource', 'source_id');
    }

    public function buddyreferrallead_user() {
        return $this->belongsTo('App\User', 'staff_id');
    }

    public function buddyreferrallead_club() {
        return $this->belongsTo('App\Model\Club\ClubInfo', 'club_id');
    }

    public function lead_attribute() {
        return $this->belongsToMany('App\Model\MasterData\MemberAttribute', 'guest_attributes', 'guest_id', 'attribute_id')->withTimestamps();
    }
}
