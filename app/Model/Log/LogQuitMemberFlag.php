<?php

namespace App\Model\Log;

use Illuminate\Database\Eloquent\Model;

class LogQuitMemberFlag extends Model
{
    protected $table = 'log_quit_member_flags';

    protected $fillable = [
        'staff_id',
        'member_id',
        'flag',
        'club',
        'membership_type',
        'cancellation_date',
    ];
}
