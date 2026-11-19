<?php

namespace App\Model\GuestMembership;

use Illuminate\Database\Eloquent\Model;

class ClubGuestMembership extends Model
{
//    protected $table = 'club_guest_memberships';

//    protected $fillable = [
//        'club_id',
//        'gm_id',
//        'price',
//        'from_date',
//        'thru_date',
//    ];

    //hasMany
//    public function clubgm_gmmember() {
//        return $this->hasMany('App\Model\GuestMembership\GuestMembershipMember', 'club_gm_id');
//    }

    //belongsTo
//    public function clubgm_club() {
//        return $this->belongsTo('App\Model\Club\ClubInfo', 'club_id');
//    }
//
//    public function clubgm_gm() {
//        return $this->belongsTo('App\Model\GuestMembership\GuestMembership', 'gm_id');
//    }
}
