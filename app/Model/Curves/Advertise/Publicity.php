<?php

namespace App\Model\Curves\Advertise;

use Illuminate\Database\Eloquent\Model;

class Publicity extends Model
{
    protected $table = "publicities";

    protected $fillable = [
        'photo',
        'headline',
        'from_date',
        'thru_date',
        'note',
        'status',
    ];
}
