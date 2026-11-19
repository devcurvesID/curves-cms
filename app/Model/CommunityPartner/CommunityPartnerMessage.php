<?php

namespace App\Model\CommunityPartner;

use Illuminate\Database\Eloquent\Model;

class CommunityPartnerMessage extends Model
{
    protected $table = 'community_partner_messages';

    protected $fillable = [
        'cp_id',
        'message',
        'from_date',
        'thru_date'
    ];

    public function communitypartnermessage_communitypartner() {
        return $this->belongsTo('App\Model\CommunityPartner\CommunityPartner', 'cp_id');
    }
}