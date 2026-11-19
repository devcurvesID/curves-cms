<?php

namespace App\Model\Member;

use Illuminate\Database\Eloquent\Model;

class MemberMessageNote extends Model
{
    protected $table = 'member_message_notes';

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'user_id',
        'notes',
        'from_date',
        'thru_date',
        'predefined_message',
    ];

    public function membermessagenote_user() {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function membermessagenote_predefinedmessage() {
        return $this->belongsTo('App\Model\MasterData\PredefinedMessage', 'predefined_message');
    }
}
