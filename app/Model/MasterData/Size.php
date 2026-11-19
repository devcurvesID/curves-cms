<?php

namespace App\Model\MasterData;

use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    protected $table = 'mst_sizes';

    protected $fillable = [
        'size',
        'description',
    ];

    public function size_memberpersonal() {
        return $this->hasMany('App\Model\Member\MemberPersonal', 'tshirt_size');
    }
}
