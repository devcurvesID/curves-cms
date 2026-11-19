<?php

namespace App\Model\Locker;

use Illuminate\Database\Eloquent\Model;

class LockerInfo extends Model
{
    protected $table = 'locker_infos';

    protected $fillable = [
    	'locker_name',
    	'club_id',
    ];

    public function lockerinfo_club(){
    	return $this->belongsTo('App\Model\Club\ClubInfo','club_id');
    }

    public function lockerinfo_memberlocker(){
    	return $this->hasMany('App\Model\Locker\MemberLocker','locker_info_id');
    }
}
