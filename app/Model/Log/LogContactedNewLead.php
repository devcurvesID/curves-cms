<?php

namespace App\Model\Log;

use Illuminate\Database\Eloquent\Model;

class LogContactedNewLead extends Model
{
    protected $table = 'log_contacted_new_leads';

    protected $fillable = [
        'guest_id',
        'staff_id',
        'status',
    ];

    public function logcontactednewlead_buddyreferrallead() {
        return $this->belongsTo('App\Model\Guest\BuddyReferralLead', 'guest_id');
    }

    public function logcontactednewlead_user() {
        return $this->belongsTo('App\User', 'staff_id');
    }
}
