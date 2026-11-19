<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LogDelete extends Model
{
    protected $table = 'log_deletes';

    protected $fillable = [
        'user_id',
        'information',
    ];
}
