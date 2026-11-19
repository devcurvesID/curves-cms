<?php

namespace App\Model\GuestMembership;

use Illuminate\Database\Eloquent\Model;

class GuestMembership extends Model
{
    protected $table = 'guest_memberships';

    protected $fillable = [
        'gm_name',
        'duration_type',
        'duration_long',
        'max_workout',
        'from_date',
        'thru_date',
        'price',
    ];

    //hasMany
    public function gm_gmmember() {
        return $this->hasMany('App\Model\GuestMembership\GuestMembershipMember', 'gm_id');
    }

    //belongsToMany
    public function gm_clubgm() {
        return $this->belongsToMany('App\Model\Club\ClubInfo', 'club_guest_memberships', 'gm_id', 'club_id')->withTimestamps();
    }
}
