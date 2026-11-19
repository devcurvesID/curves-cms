<?php

namespace App\Model\MasterData;

use Illuminate\Database\Eloquent\Model;

class Point extends Model
{
    protected $table = 'mst_points';

    protected $fillable = [
    	'name',
    	'type',
    	'type_detail',
    	'target_point',
    	'point'
    ];

    //hasObe

    //hasMany
    public function point_logstaffpointacquisition(){
    	return $this->hasMany('App\Model\Log\LogStaffPointAcquisition','point_id');
    }

    public function point_staffpointhis() {
        return $this->hasMany('App\Model\Staff\StaffPointHistory', 'point_id');
    }

    //belongsTo

    //belongsToMany
}
