<?php

namespace App\Model\Appointment;

use Illuminate\Database\Eloquent\Model;

class MemberAppointmentNote extends Model
{
    protected $table = 'member_appointment_notes';

    protected $primaryKey = 'ma_id';

    protected $fillable = [
        'ma_id',
        'notes',
    ];

    public function memberappointmentnote_memberappointment() {
        return $this->belongsTo('App\Model\Appointment\MemberAppointment', 'ma_id');
    }
}
