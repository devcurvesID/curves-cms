<?php

namespace App\Model\MasterData;

use Illuminate\Database\Eloquent\Model;

class MembershipType extends Model
{
    protected $table = 'membership_types';

    protected $fillable = [
        'membership_type_name',
        'membership_type',
        'duration',
        'payment_type',
        'status',
    ];

    public function membershiptype_clubservicefee() {
        return $this->hasMany('App\Model\Club\ClubServiceFee', 'membership_type_id');
    }

    public function membershiptype_cancellationreason() {
        return $this->hasMany('App\Model\Refund\CancellationReason', 'membership_type_id');
    }

    public function membershiptype_continuemembership() {
        return $this->hasMany('App\Model\Member\ContinueMembership', 'membership_type_id');
    }

    public function membershiptype_refundsubmission() {
        return $this->hasMany('App\Model\Refund\RefundSubmission', 'membership_type_id');
    }

    public function membershiptype_amorheader() {
        return $this->hasMany('App\Model\Accounting\AmortizationHeader', 'membership_type_id');
    }

    public function membershiptype_logmemberstatus() {
        return $this->hasMany('App\Model\Log\LogMemberStatus', 'membership_type_id');
    }

    public function membershiptype_memberstatus() {
        return $this->belongsTo('App\Model\Member\MemberStatus', 'membership_type_id');
    }
}
