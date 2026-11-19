<?php

namespace App\Model\Appointment;

use Illuminate\Database\Eloquent\Model;

class GuestAppointmentStatus extends Model
{
    protected $table = 'guest_appointment_status';

    protected $primaryKey = 'ga_id';

    protected $fillable = [
        'ga_id',
        'status_id',
        'c_date',
        's_date',
        'sa_date',
    ];

    public function gastatus_guestappointment() {
        return $this->belongsTo('App\Model\Appointment\GuestAppointment', 'ga_id');
    }

    public function gastatus_appointmentstatus() {
        return $this->belongsTo('App\Model\MasterData\AppointmentStatus', 'status_id');
    }
}
