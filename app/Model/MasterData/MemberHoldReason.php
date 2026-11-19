<?php

namespace App\Model\MasterData;

use Illuminate\Database\Eloquent\Model;

class MemberHoldReason extends Model
{
    protected $table = 'member_hold_reasons';

    protected $fillable = [
        'member_hold_reason',
        'description',
    ];

    public function memberholdreason_memberhold() {
        return $this->hasMany('App\Model\Member\MemberHold', 'member_hold_reason_id');
    }
}
