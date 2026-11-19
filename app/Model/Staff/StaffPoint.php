<?php

namespace App\Model\Staff;

use Illuminate\Database\Eloquent\Model;

class StaffPoint extends Model
{
	protected $primaryKey = 'user_id';

    protected $table = 'staff_points';

    protected $fillable = [
    	'user_id',
    	'current_total_point',
    	'last_months_point',
    	'last_two_months_point',
    	'last_months_date',
    	'last_two_months_date',
        'current_point',
        'last_week_point',
    ];

    //hasOne

    //hasMany

    //belongsTo
    public function staffpoint_user(){
    	return $this->belongsTo('App\User','user_id');
    }

    //belongsToMany
}
