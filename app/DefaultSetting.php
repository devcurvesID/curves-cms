<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DefaultSetting extends Model
{
    protected $table = 'default_settings';

    protected $fillable = [
        'key_name',
        'value',
        'category',
    ];
}
