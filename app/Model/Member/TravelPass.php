<?php

namespace App\Model\Member;

use Illuminate\Database\Eloquent\Model;

class TravelPass extends Model
{
    protected $table = 'travel_pass';

    protected $fillable = [
    	'user_id',
    	'from_date',
    	'thru_date',
    	'active_from_date',
    	'active_thru_date',
    	'notes',
    	'status'
    ];

    public function travelpass_user(){
    	return $this->belongsTo('App\User','user_id');
    }
}
