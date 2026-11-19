<?php

namespace App\Model\Corporation;

use Illuminate\Database\Eloquent\Model;

class Corporation extends Model
{
    protected $table = 'corporations';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'address',
        'phone_number',
        'email',
    ];

    public function corporation_membercorporations(){
    	return $this->hasMany('App\Model\Corporation\MemberCorporation','corporation_id');
    }
}
