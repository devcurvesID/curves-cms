<?php

namespace App\Model\MasterData;

use Illuminate\Database\Eloquent\Model;

class Certification extends Model
{
    protected $table = 'mst_certifications';

    protected $fillable = [
    	'certification_name',
    	'notes',
    ];

    public function certification_staffcertification(){
    	return $this->hasMany('App\Model\Staff\StaffCertification','certification_id');
    }
}
