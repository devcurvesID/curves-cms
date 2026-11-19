<?php

namespace App\Model\Staff;

use Illuminate\Database\Eloquent\Model;

class StaffNote extends Model
{
    protected $table = 'staff_notes';

    protected $fillable = [
        'user_id',
        'note',
        'status',
    ];

    public function staffnote_user() {
        return $this->belongsTo('App\User', 'user_id');
    }
}
