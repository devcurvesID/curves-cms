<?php

namespace App\Model\Member;

use Illuminate\Database\Eloquent\Model;

class StaffReport extends Model
{
    protected $table = 'staff_reports';

    protected $fillable = [
        'member_id',
        'staff_id',
        'report',
        'note',
        'contact_date',
    ];

    public function staffreport_member() {
        return $this->belongsTo('App\User', 'member_id');
    }

    public function staffreport_staff() {
        return $this->belongsTo('App\User', 'staff_id');
    }
}
