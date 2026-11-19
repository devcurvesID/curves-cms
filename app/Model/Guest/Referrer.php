<?php

namespace App\Model\Guest;

use Illuminate\Database\Eloquent\Model;

class Referrer extends Model
{
    protected $table = 'referrers';

    protected $primaryKey = 'referral_id';

    protected $fillable = [
        'referral_id',
        'user_id',
        'status',
    ];

    public function referrer_buddyreferrallead() {
        return $this->belongsTo('App\Model\Guest\BuddyReferralLead', 'referral_id');
    }

    public function referrer_user() {
        return $this->belongsTo('App\User', 'user_id');
    }
}
