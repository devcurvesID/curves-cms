<?php

namespace App\Model\MasterData;

use Illuminate\Database\Eloquent\Model;

class ThirtyDay extends Model
{
    protected $table = 'mst_thirty_days';

    protected $fillable = [
        'menu',
        'sequence',
    ];

    public function thirtyday_memberthirtyday() {
        return $this->hasMany('App\Model\Member\MemberThirtyDay', 'thirty_days_id');
    }
}
