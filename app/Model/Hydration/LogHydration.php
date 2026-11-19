<?php

namespace App\Model\Hydration;

use Illuminate\Database\Eloquent\Model;

class LogHydration extends Model
{
    protected $table = 'log_hydrations';

    protected $fillable = [
    	'user_id',
    	'hydration_datetime',
    	'hydration_value'
    ];

    public function loghydration_user(){
    	return $this->belongsTo('App\User','user_id');
    }
}
