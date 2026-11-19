<?php

namespace App\Model\Report;

use Illuminate\Database\Eloquent\Model;

class CAManagement extends Model
{
    protected $table = 'ca_managements';

    protected $fillable = [
        'club_id',
        'type',
        'code',
        'ca_date',
    ];

    public function camanagement_club() {
        return $this->belongsTo('App\Model\Club\ClubInfo', 'club_id');
    }
}
