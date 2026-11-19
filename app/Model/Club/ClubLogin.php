<?php

namespace App\Model\Club;

use Illuminate\Database\Eloquent\Model;

class ClubLogin extends Model
{
    protected $table = 'club_logins';

    protected $primaryKey = 'club_id';

    protected $fillable = [
        'club_id',
        'username',
        'password',
        'encrypted_password',
    ];

    public function clublogin_clubinfo() {
        return $this->belongsTo('App\Model\Club\ClubInfo', 'club_id');
    }
}
