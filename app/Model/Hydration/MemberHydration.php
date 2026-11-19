<?php

namespace App\Model\Hydration;

use Illuminate\Database\Eloquent\Model;

class MemberHydration extends Model
{
	protected $primaryKey = 'user_id';

    protected $table = 'member_hydrations';

    protected $fillable = [
    	'user_id',
    	'goal',
    ];

    public function memberhydration_user(){
    	return $this->belongsTo('App\User','user_id');
    }
}
