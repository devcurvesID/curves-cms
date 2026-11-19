<?php

namespace App\Model\WM;

use Illuminate\Database\Eloquent\Model;

class Xfactor extends Model
{
    protected $table = 'xfactors';

    protected $fillable = [
        'user_id',
        'xfactor',
    ];

    public function xfactor_member() {
        return $this->belongsTo('App\User', 'user_id');
    }
}
