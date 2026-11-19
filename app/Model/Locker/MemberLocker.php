<?php

namespace App\Model\Locker;

use Illuminate\Database\Eloquent\Model;

class MemberLocker extends Model
{
    protected $table = 'member_lockers';

    protected $fillable = [
        'user_id',
        'from_date',
        'thru_date',
        'locker_info_id',
        'is_key_returned',
    ];

    public function getPrettyMemberlockerAttribute(){
        return $this->memberlocker_lockerinfo->locker_name .' ('. $this->memberlocker_lockerPayment[0]->payment_date .')';
    }

    public function memberlocker_user(){
    	return $this->belongsTo('App\User', 'user_id');
    }

    public function memberlocker_lockerPayment(){
    	return $this->hasMany('App\Model\Locker\LockerPayment', 'memberlocker_id');
    }

    public function memberlocker_lockerinfo(){
        return $this->belongsTo('App\Model\Locker\LockerInfo','locker_info_id');
    }
}
