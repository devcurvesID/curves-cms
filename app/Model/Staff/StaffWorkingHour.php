<?php

namespace App\Model\Staff;

use Illuminate\Database\Eloquent\Model;

class StaffWorkingHour extends Model
{
    protected $table = 'staff_working_hours';

    protected $fillable = [
        'user_id',
        'from_day',
        'thru_day',
        'from_hour',
        'thru_hour',
    ];

    public function staffworkinghour_user() {
        return $this->belongsTo('App\User', 'user_id');
    }
}
