<?php

namespace App\Model\Workout;

use Illuminate\Database\Eloquent\Model;
use App\Model\Payment\MemberPayment;

class Workout extends Model
{
    protected $table = 'workouts';

    protected $fillable = [
        'user_id',
        'workout_date',
        'club_id',
    ];

    // hasMany
    public function workout_survey() {
        return $this->hasOne('App\Model\Survey\Survey', 'workout_id');
    }

    public function workout_surveyanswer() {
        return $this->hasMany('App\Model\Survey\SurveyAnswer', 'workout_id');
    }

    // belongsTo
    public function workout_user() {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function workout_club() {
        return $this->belongsTo('App\Model\Club\ClubInfo', 'club_id');
    }

    public function pending_payment() {
        return $this->belongsTo('App\Model\Payment\MemberPayment', 'user_id', 'user_id');
    }
}
