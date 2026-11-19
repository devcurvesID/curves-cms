<?php

namespace App\Model\Member;

use Illuminate\Database\Eloquent\Model;

class VisitingMemberWorkout extends Model
{
    protected $table = 'visiting_member_workouts';

    protected $fillable = [
    	'visiting_member_id',
    	'workout_date',
    	'club_id',
    ];

    public function visitingmemberworkout_visitingmember(){
    	return $this->belongsTo('App\Model\Member\VisitingMember','visiting_member_id');
    }

    public function visitingmemberworkout_club(){
    	return $this->belongsTo('App\Model\Club\ClubInfo','club_id');
    }
}
