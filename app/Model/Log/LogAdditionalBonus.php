<?php

namespace App\Model\Log;

use Illuminate\Database\Eloquent\Model;

class LogAdditionalBonus extends Model
{
    protected $table = 'log_additional_bonuses';

    protected $fillable = [
        'user_id',
    ];

    //belongsTo
    public function logadditionalbonus_user() {
        return $this->belongsTo('App\User', 'user_id');
    }
}
