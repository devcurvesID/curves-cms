<?php

namespace App\Model\MasterData;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $table = 'mst_provinces';

    protected $fillable = [
        'country_id',
        'province'
    ];

    public function province_city() {
        return $this->hasMany('App\Model\MasterData\City', 'province_id');
    }

    public function province_clubinfo() {
        return $this->hasMany('App\Model\Club\ClubInfo', 'province_id');
    }

    public function province_country() {
        return $this->belongsTo('App\Model\MasterData\Country', 'country_id');
    }
}
