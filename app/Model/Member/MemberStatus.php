<?php

namespace App\Model\Member;

use Illuminate\Database\Eloquent\Model;

class MemberStatus extends Model
{
    protected $table = 'member_status';

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'user_id',
        'status',
        'membership_type_id',
        'payment_status',
        'last_wm',
        'from_date',
        'thru_date',
        'member_vip_status',
        'wm_schedule',
        'qr_generate',
        'ignore_wa',
    ];

    public function memberstatus_user() {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function memberstatus_membershiptype() {
        return $this->belongsTo('App\Model\MasterData\MembershipType', 'membership_type_id');
    }
}
