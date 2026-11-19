<?php

namespace App\Model\Guest;

use Illuminate\Database\Eloquent\Model;

class GuestDetail extends Model
{
    protected $table = 'guest_details';

    protected $primaryKey = 'guest_id';

    protected $fillable = [
        'birth',
        'email',
        'occupation',
        'status',
    ];

    public function guestdetail_buddyreferrallead() {
        return $this->belongsTo('App\Model\Guest\BuddyReferralLead', 'guest_id');
    }
}
