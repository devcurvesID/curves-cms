<?php

namespace App\Model\Guest;

use Illuminate\Database\Eloquent\Model;

class GuestMonitoringDetail extends Model
{
    protected $table = 'guest_monitoring_details';

    protected $fillable = [
        'guest_monitoring_id',
        'staff_id',
        'start_call',
        'end_call',
        'duration',
        'status',
    ];

    // belongsTo
    public function gmdetail_brl() {
        return $this->belongsTo('App\Model\Guest\BuddyReferralLead', 'guest_monitoring_id');
    }

    public function gmdetail_staff() {
        return $this->belongsTo('App\User', 'staff_id');
    }
}
