<?php

namespace App\Model\Member;

use Illuminate\Database\Eloquent\Model;

class MemberPhoto extends Model
{
    protected $table = 'member_photos';

    protected $fillable = [
        'member_id',
        'photo',
        'album_group',
        'description',
        'date_taken',
    ];

    public function memberphoto_user() {
        return $this->belongsTo('App\User', 'member_id');
    }
}
