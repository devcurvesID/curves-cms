<?php

namespace App\Model\WM;

use Illuminate\Database\Eloquent\Model;

class WeighMeasureGoal extends Model
{
    protected $table = 'weigh_measure_goals';

    protected $fillable = [
        'user_id',
        'set_goal_date',
        'chest',
        'waist',
        'abdomen',
        'hip',
        'thigh',
        'arm',
        /*'left_thigh',
        'right_thigh',
        'left_arm',
        'right_arm',*/
        'weight',
        'body_fat',
        /*'body_fat_left_arm',
        'body_fat_right_arm',
        'body_fat_left_leg',
        'body_fat_right_leg',*/
        'muscle_mass',
        'bone_mass',
        'bmi',
        'dci',
        'metabolic',
        'body_water',
        'visceral',
    ];

    public function weighmeasuregoal_member() {
        return $this->belongsTo('App\User', 'user_id');
    }
}
