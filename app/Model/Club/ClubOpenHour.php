<?php

namespace App\Model\Club;

use Illuminate\Database\Eloquent\Model;

class ClubOpenHour extends Model
{
    protected $table = 'club_open_hours';

    protected $fillable = [
        'club_id',
        'from_day',
        'thru_day',
        'from_hour',
        'thru_hour',
    ];

    /*public function clubopenhour_clubappointmenthour() {
        return $this->hasOne('App\ClubAppointmentHour', 'club_open_hour_id');
    }*/

    public function clubopenhour_clubinfo() {
        return $this->belongsTo('App\Model\Club\ClubInfo', 'club_id');
    }
}
