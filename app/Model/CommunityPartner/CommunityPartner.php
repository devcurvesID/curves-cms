<?php

namespace App\Model\CommunityPartner;

use Illuminate\Database\Eloquent\Model;

class CommunityPartner extends Model
{
    protected $table = 'community_partners';

    protected $fillable = [
    	'cp_name',
    	'pic_name',
    	'address',
    	'phone_number',
    	'e-mail',
        'status'
    ];

    public function communitypartner_communitypartnermessage() {
        return $this->hasMany('App\Model\CommunityPartner\CommunityPartnerMessage', 'cp_id');
    }
}
