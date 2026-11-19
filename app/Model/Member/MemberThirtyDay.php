<?php

namespace App\Model\Member;

use Illuminate\Database\Eloquent\Model;

class MemberThirtyDay extends Model
{
    protected $table = 'member_thirty_days';

    protected $fillable = [
        'user_id',
        'thirty_days_id',
        'result',
    ];

    public function memberthirtyday_user() {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function memberthirtyday_staff() {
        return $this->belongsTo('App\User', 'staff_id');
    }

    public function memberthirtyday_thirtyday() {
        return $this->belongsTo('App\Model\MasterData\ThirtyDay', 'thirty_days_id');
    }
}
