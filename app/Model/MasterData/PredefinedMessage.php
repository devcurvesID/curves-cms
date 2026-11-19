<?php

namespace App\Model\MasterData;

use Illuminate\Database\Eloquent\Model;

class PredefinedMessage extends Model
{
    protected $table = 'mst_predefined_messages';

    protected $fillable = [
        'message'
    ];

    public function predefinedmessage_membermessagenote() {
        return $this->hasMany('App\Model\Member\MemberMessageNote', 'predefined_message');
    }
}
