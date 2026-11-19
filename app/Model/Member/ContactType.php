<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContactType extends Model
{
    protected $table = 'contact_types';

    protected $fillable = [
    	'contact_type'
    ];

    public function contacttype_membermonitoring(){
    	return $this->hasMany('App\Model\Member\MemberMonitoring','contact_type_id');
    }
}
