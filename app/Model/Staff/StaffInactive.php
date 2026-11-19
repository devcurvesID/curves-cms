<?php

namespace App\Model\Staff;

use Illuminate\Database\Eloquent\Model;

class StaffInactive extends Model
{
    protected $table = 'staff_inactives';

    protected $fillable = [
        'staff_id',
        'inactive_type',
        'reason',
        'from_date',
        'thru_date',
        'status',
    ];

    public function staffinactive_user() {
        return $this->belongsTo('App\User', 'staff_id');
    }
}
