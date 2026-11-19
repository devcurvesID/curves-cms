<?php

namespace App\Model\MasterData;

use Illuminate\Database\Eloquent\Model;

class Locker extends Model
{
    protected $table = 'mst_lockers';

    protected $fillable = [
        'name',
        'duration',
        'duration_type',
        'price',
        'club_id',
    ];

    public function locker_lockerpayment() {
        return $this->hasMany('App\Model\Locker\LockerPayment', 'locker_id');
    }

    public function locker_clubinfo() {
        return $this->belongsTo('App\Model\Club\ClubInfo', 'club_id');
    }
}
