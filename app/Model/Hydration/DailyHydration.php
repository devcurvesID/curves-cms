<?php

namespace App\Model\Hydration;

use Illuminate\Database\Eloquent\Model;

class DailyHydration extends Model
{
    protected $table = 'daily_hydrations';

    protected $fillable = [
    	'user_id',
    	'hydration_date',
    	'total_hydration',
    	'target_hydration'
    ];

    public function dailyhydration_user(){
    	return $this->belongsTo('App\User','user_id');
    }
}
