<?php

namespace App\Model\Staff;

use Illuminate\Database\Eloquent\Model;

class StaffPersonal extends Model
{
    protected $table = 'staff_personals';

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'user_id',
        /*'birth',
        'sex',
        'address',
        'postal',
        'phone',
        'cellphone',*/
        'email_personal',
        /*'nik',*/
        'npwp',
        /*'photo',
        'key_tag_id',
        'joined'*/
    ];

    public function staffpersonal_user() {
        return $this->belongsTo('App\User', 'user_id');
    }
}
