<?php

namespace App\Model\Member;

use Illuminate\Database\Eloquent\Model;

class InactiveMembershipReason extends Model
{
    protected $table = 'inactive_membership_reasons';

    protected $fillable = [
        'log_membership_id',
        'reason_id',
        'another_reason',
    ];

    public function inactivereason_user() {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function inactivereason_reason() {
        return $this->belongsTo('App\Model\MasterData\NoSaleReason', 'reason_id');
    }
}
