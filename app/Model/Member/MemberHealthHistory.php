<?php

namespace App\Model\Member;

use Illuminate\Database\Eloquent\Model;

class MemberHealthHistory extends Model
{
    protected $table = 'member_health_histories';

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'user_id',
        'doctor_name',
        'doctor_contact_number',
        'comment',
    ];

    public function memberhealthhistory_user() {
        return $this->belongsTo('App\User', 'user_id');
    }
}
