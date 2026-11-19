<?php

namespace App\Model\MasterData;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = 'mst_cities';

    protected $fillable = [
        'province_id',
        'city'
    ];

    public function city_clubinfo() {
        return $this->hasMany('App\Model\Club\ClubInfo', 'city_id');
    }

    public function city_province() {
        return $this->belongsTo('App\Model\MasterData\Province', 'province_id');
    }
}
