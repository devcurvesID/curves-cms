<?php

namespace App\Model\GuestMembership;

use Illuminate\Database\Eloquent\Model;

class GuestWM extends Model
{
    protected $table = 'guest_wms';

    protected $fillable = [
        'guest_id',
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
        'weight',
        'body_fat',
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
        'recommendation',
    ];

    //belongsTo
    public function guestwm_guest() {
        return $this->belongsTo('App\Model\Guest\BuddyReferralLead', 'guest_id');
    }

    public function guestwm_measurer() {
        return $this->belongsTo('App\User', 'measured_by');
    }
}
