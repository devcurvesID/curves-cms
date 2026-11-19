<?php

namespace App\Model\Log;

use Illuminate\Database\Eloquent\Model;

class LogMemberStatus extends Model
{
    protected $table = 'log_member_memberships';

    protected $fillable = [
        'user_id',
        'membership_type_id',
        'from_date',
        'thru_date',
        'club_id',
    ];

    public function logmemberstatus_user() {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function logmemberstatus_membershiptype() {
        return $this->belongsTo('App\Model\MasterData\MembershipType', 'membership_type_id');
    }
}
