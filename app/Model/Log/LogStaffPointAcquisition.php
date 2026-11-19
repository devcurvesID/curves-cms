<?php

namespace App\Model\Log;

use Illuminate\Database\Eloquent\Model;

class LogStaffPointAcquisition extends Model
{
    protected $table = 'log_staff_point_acquisitions';

    protected $fillable = [
    	'acquire_date',
    	'amount',
    	'user_id',
    	'point_id',
    ];

    //hasOne

    //hasMany

    //belongsToOne
    public function logstaffpointacquisition_user(){
    	return $this->belongsTo('App\User','user_id');
    }

    public function logstaffpointacquisition_point(){
    	return $this->belongsTo('App\Model\MasterData\Point','point_id');
    }

    //belongsToMany
}
