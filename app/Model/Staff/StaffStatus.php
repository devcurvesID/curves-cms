<?php

namespace App\Model\Staff;

use Illuminate\Database\Eloquent\Model;

class StaffStatus extends Model
{
    protected $table = 'staff_status';

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'user_id',
        'status',
        'from_date',
        'thru_date',
    ];

    public function staffstatus_user() {
        return $this->belongsTo('App\User', 'user_id');
    }
}
