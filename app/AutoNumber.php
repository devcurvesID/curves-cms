<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AutoNumber extends Model
{
    protected $table = 'auto_numbers';

    protected $fillable = [
        'name',
        'current_number',
    ];
}
