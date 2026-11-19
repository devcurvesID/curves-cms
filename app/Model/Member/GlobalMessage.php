<?php

namespace App\Model\Member;

use Illuminate\Database\Eloquent\Model;

class GlobalMessage extends Model
{
    protected $table = 'global_messages';

    protected $fillable = [
    	'message',
    	'from_date',
    	'thru_date',
    ];

    public function globalmessage_club(){
    	return $this->belongsToMany('App\Model\Club\ClubInfo','club_global_messages','global_message_id','club_id')->withTimestamps();
    }
}
