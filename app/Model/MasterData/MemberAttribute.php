<?php

namespace App\Model\MasterData;

use Illuminate\Database\Eloquent\Model;

class MemberAttribute extends Model
{
    protected $table = 'mst_member_attributes';

    protected $fillable = [
        'attribute'
    ];

    public function attribute_member() {
        return $this->belongsToMany('App\User', 'member_attributes', 'attribute_id', 'user_id')->withTimestamps();
    }

    public function attribute_lead() {
        return $this->belongsToMany('App\Model\Guest\BuddyReferralLead', 'guest_attributes', 'attribute_id', 'guest_id')->withTimestamps();
    }
}
