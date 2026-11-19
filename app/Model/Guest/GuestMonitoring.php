<?php

namespace App\Model\Guest;

use Illuminate\Database\Eloquent\Model;

class GuestMonitoring extends Model
{
    protected $table = 'guest_monitorings';

    protected $primaryKey = 'guest_id';

    protected $fillable = [
        'guest_id',
        'success',
        'rejected',
        'hanging_out',
        'invalid',
        'call_count',
        'last_call',
        'last_call_status',
    ];

    // belongsTo
    public function guestmonitoring_lead() {
        return $this->belongsTo('App\Model\Guest\BuddyReferralLead', 'guest_id');
    }
}
