<?php

namespace App\Model\Log;

use Illuminate\Database\Eloquent\Model;

class LogMemberTShirtSize extends Model
{
    protected $table = 'log_member_tshirt_sizes';

    protected $fillable = [
        'member_id',
        'tshirt_size',
        'size_date',
    ];
}
