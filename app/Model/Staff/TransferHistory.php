<?php

namespace App\Model\Staff;

use Illuminate\Database\Eloquent\Model;

class TransferHistory extends Model
{
    protected $table = 'transfer_histories';

    protected $fillable = [
        'user_id',
        'role_id',
        'club_id',
        'from_date',
    ];

    public function transferhistory_user() {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function transferhistory_role() {
        return $this->belongsTo('App\Model\Role\Role', 'role_id');
    }

    public function transferhistory_club() {
        return $this->belongsTo('App\Model\Club\ClubInfo', 'club_id');
    }
}
