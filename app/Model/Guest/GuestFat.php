<?php

namespace App\Model\Guest;

use Illuminate\Database\Eloquent\Model;

class GuestFat extends Model
{
    protected $table = 'guest_fats';

    protected $primaryKey = 'guest_id';

    protected $fillable = [
        'weight',
        'body_fat',
        'visceral',
    ];

    public function guestfat_buddyreferrallead() {
        return $this->belongsTo('App\Model\Guest\BuddyReferralLead', 'guest_id');
    }
}
