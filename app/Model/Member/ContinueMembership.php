<?php

namespace App\Model\Member;

use Illuminate\Database\Eloquent\Model;

class ContinueMembership extends Model
{
    protected $table = 'continue_memberships';

    protected $fillable = [
        'user_id',
        'membership_type_id',
        'from_date',
        'thru_date',
        'status',
        'payment_status',
        'staff_id',
    ];

    public function continuemembership_continuemembershiprefund() {
        return $this->hasMany('App\Model\Refund\ContinueMembershipRefund', 'continue_id');
    }

    public function continuemembership_user() {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function continuemembership_staff() {
        return $this->belongsTo('App\User', 'staff_id');
    }

    public function continuemembership_membershiptype() {
        return $this->belongsTo('App\Model\MasterData\MembershipType', 'membership_type_id');
    }
}
