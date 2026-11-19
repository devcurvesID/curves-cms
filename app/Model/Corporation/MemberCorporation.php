<?php

namespace App\Model\Corporation;

use Illuminate\Database\Eloquent\Model;

class MemberCorporation extends Model
{
    protected $table = 'member_corporations';

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'user_id',
        'corporation_id',
        'group_name',
        'sequence_group',
    ];

    public function membercorporation_user() {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function membercorporation_corporation() {
        return $this->belongsTo('App\Model\Corporation\Corporation', 'corporation_id');
    }
}
