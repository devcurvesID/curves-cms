<?php

namespace App\Model\Member;

use Illuminate\Database\Eloquent\Model;

class MIAMonitoring extends Model
{
    protected $table = 'mia_monitorings';

    protected $fillable = [
        'caller_id',
        'member_id',
        'calling_date',
        'reason',
    ];

    // belongsTo
    public function miamonitoring_staff() {
        return $this->belongsTo('App\User', 'caller_id');
    }

    public function miamonitoring_member() {
        return $this->belongsTo('App\User', 'member_id');
    }
}
