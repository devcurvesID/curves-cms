<?php

namespace App\Model\Member;

use Illuminate\Database\Eloquent\Model;

class VisitingMember extends Model
{
    protected $table = 'visiting_members';

    protected $fillable = [
    	'member_name',
    	'member_id',
    	'membership_expiration_date',
    	'origin_club',
    	'origin_club_address',
    	'origin_club_phone_number',
    	'origin_club_fax_number',
    	'origin_club_email',
    	'first_use_date',
    	'validity_date',
    ];

    public function visitingmember_visitingmemberworkout(){
    	return $this->hasMany('App\Model\Member\VisitingMemberWorkout','visiting_member_id');
    }
}
