<?php

namespace App\Model\Staff;

use Illuminate\Database\Eloquent\Model;

class StaffCode extends Model
{
    protected $table = 'staff_codes';


    protected $primaryKey = 'user_id';

    protected $fillable = [
        'user_id',
        'code'
    ];

    //belongsTo
    public function staffcode_user(){
        return $this->belongsTo('App\User','user_id');
    }
}
