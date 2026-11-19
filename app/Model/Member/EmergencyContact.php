<?php

namespace App\Model\Member;

use Illuminate\Database\Eloquent\Model;

class EmergencyContact extends Model
{
    protected $table = 'emergency_contacts';

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'user_id',
        'family_name',
        'relation',
        'emergency_contact_number',
    ];

    public function emergencycontact_user() {
        return $this->belongsTo('App\User', 'user_id');
    }
}
