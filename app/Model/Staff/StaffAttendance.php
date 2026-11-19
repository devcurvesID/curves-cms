<?php

namespace App\Model\Staff;

use Illuminate\Database\Eloquent\Model;

class StaffAttendance extends Model
{
    protected $table = 'staff_attendances';

    protected $fillable = [
        'user_id',
        'sign_in',
        'sign_out',
        'login_ip_address',
        'club_id',
    ];

    public function staffattendance_user() {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function staffattendance_club(){
        return $this->belongsTo('App\Model\Club\ClubInfo','club_id');
    }
}
