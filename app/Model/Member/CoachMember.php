<?php

namespace App\Model\Member;

use Illuminate\Database\Eloquent\Model;

class CoachMember extends Model
{
    protected $table = 'coach_members';

    protected $primaryKey = 'member_id';

    protected $fillable = [
        'member_id',
        'coach_id',
    ];

    public function coachmember_memberuser() {
        return $this->belongsTo('App\User', 'member_id');
    }

    public function coachmember_coachuser() {
        return $this->belongsTo('App\User', 'coach_id');
    }
}
