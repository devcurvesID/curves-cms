<?php

namespace App\Model\MasterData;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'mst_countries';

    protected $fillable = [
        'country'
    ];

    public function country_province() {
        return $this->hasMany('App\Model\MasterData\Province', 'country_id');
    }

    public function country_clubinfo() {
        return $this->hasMany('App\Model\Club\ClubInfo', 'country_id');
    }
}
