<?php

namespace App\Model\Staff;

use Illuminate\Database\Eloquent\Model;

class StaffCertification extends Model
{
    protected $table = 'staff_certifications';

    protected $fillable = [
    	'staff_id',
    	'certification_id',
    	'certiication_number',
    	'level',
    	'start_date',
    	'thru_date',
    ];

    public function staffcertification_user(){
    	return $this->belongsTo('App\User','staff_id');
    }

    public function staffcertification_certification(){
    	return $this->belongsTo('App\Model\MasterData\Certification','certification_id');
    }
}
