<?php

namespace App\Model\Member;

use Illuminate\Database\Eloquent\Model;

class MemberMonitoring extends Model
{
    protected $table = 'member_monitorings';

    protected $fillable = [
    	'user_id',
    	'staff_id',
    	'contact_date',
    	'call_count',
    	'message_count',
    	'contact_type_id',
    ];

    public function membermonitoring_user(){
    	return $this->belongsTo('App/User','user_id');
    }

    public function membermonitoring_staff(){
    	return $this->belongsTo('App\User','staff_id');
    }

    public function membermonitoring_contacttype(){
    	return $this->belongsTo('App\Model\Member\ContactType','contact_type_id');
    }
}
