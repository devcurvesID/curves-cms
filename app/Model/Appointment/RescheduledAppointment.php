<?php

namespace App\Model\Appointment;

use Illuminate\Database\Eloquent\Model;

class RescheduledAppointment extends Model
{
    protected $table = 'rescheduled_appointments';

    protected $fillable = [
        'appointment_type',
        'appointment_id',
        'original_schedule_date',
        'original_schedule_time',
        'new_schedule_date',
        'new_schedule_time',
        'last_status',
        'rescheduled_by',
    ];

    public function rescheduledappointment_appointmentstatus() {
        return $this->belongsTo('App\Model\MasterData\AppointmentStatus', 'last_status');
    }

    public function rescheduledappointment_user() {
        return $this->belongsTo('App\User', 'rescheduled_by');
    }

    /*public function rescheduledappointment_guestappointment() {
        return $this->belongsTo('App\Model\Appointment\GuestAppointment', 'appointment_id');
    }*/
}
