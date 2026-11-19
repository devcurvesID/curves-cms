<?php

namespace App\Model\Member;

use Illuminate\Database\Eloquent\Model;

class MemberPersonal extends Model
{
    protected $table = 'member_personals';

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'user_id',
        'birth',
        'sex',
        'address',
        'postal',
        'phone',
        'cellphone',
        'office',
        'nik',
        'photo',
        'key_tag_id',
        'sales_person_id',
        'guest_id',
        'joined',
        'created_by',
        'tshirt_size',
        'flag',
    ];

    public function memberpersonal_user() {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function memberpersonal_sales() {
        return $this->belongsTo('App\User', 'sales_person_id');
    }

    public function memberpersonal_buddyreferrallead() {
        return $this->belongsTo('App\Model\Guest\BuddyReferralLead', 'guest_id');
    }

    public function memberpersonal_createdby() {
        return $this->belongsTo('App\User', 'created_by');
    }

    public function memberpersonal_size() {
        return $this->belongsTo('App\Model\MasterData\Size', 'tshirt_size');
    }
}
