<?php

namespace App\Model\Guest;

use Illuminate\Database\Eloquent\Model;

class GuestAttribute extends Model
{
    protected $table = 'guest_attribute';

    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'member_attribute_id',
    ];
}
