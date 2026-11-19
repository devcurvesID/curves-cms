<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccurateAuth extends Model
{
    protected $table = 'accurate';

    protected $fillable = [
        'code',
        'access_token',
        'refresh_token',
    ];

    public static function findOrCreate($id)
    {
        $obj = static::find($id);
        return $obj ?: new static;
    }
}
