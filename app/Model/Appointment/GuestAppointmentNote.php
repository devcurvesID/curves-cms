<?php

namespace App\Model\Appointment;

use Illuminate\Database\Eloquent\Model;

class GuestAppointmentNote extends Model
{
    protected $table = 'guest_appointment_notes';

    protected $primaryKey = 'ga_id';

    protected $fillable = [
        'ga_id',
        'note',
    ];

    public function ganotes_guestappointment() {
        return $this->belongsTo('App\Model\Appointment\GuestAppointment', 'ga_id');
    }
}
