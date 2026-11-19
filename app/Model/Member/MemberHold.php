<?php

namespace App\Model\Member;

use Illuminate\Database\Eloquent\Model;

class MemberHold extends Model
{
    protected $table = 'member_holds';

    protected $fillable = [
        'user_id',
        'club_id',
        'member_hold_reason_id',
        'member_hold_note',
        'from_date',
        'thru_date',
        'status',
    ];

    public function memberhold_user() {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function memberhold_club() {
        return $this->belongsTo('App\Model\Club\ClubInfo', 'club_id');
    }

    public function memberhold_memberholdreason() {
        return $this->belongsto('App\Model\MasterData\MemberHoldReason', 'member_hold_reason_id');
    }
}
