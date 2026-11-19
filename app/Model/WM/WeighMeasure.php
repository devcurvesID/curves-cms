<?php

namespace App\Model\WM;

use Illuminate\Database\Eloquent\Model;

class WeighMeasure extends Model
{
    protected $table = 'weigh_measures';

    protected $fillable = [
        'user_id',
        'wm_date',
        'age',
        'height',
        'bp_high',
        'bp_low',
        'rhr',
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
        'three_times_a_week',
        'total_wo_per_month',
        'total_measurement',
        'total_body_fat',
        'total_hydration',
        'measured_by',
        'pro_workout',
    ];

    public function weighmeasure_member() {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function weighmeasure_measuredby() {
        return $this->belongsTo('App\User', 'measured_by');
    }

    public function weighmeasure_weighmeasureattribute() {
        return $this->belongsToMany('App\Model\MasterData\WeighMeasureAttribute', 'weigh_measure_attributes', 'wm_id', 'wm_attribute_id')->withTimestamps();
    }
}
