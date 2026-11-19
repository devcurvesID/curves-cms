<?php

namespace App\Model\MasterData;

use Illuminate\Database\Eloquent\Model;

class AppointmentStatus extends Model
{
    protected $table = 'mst_appointment_status';

    protected $fillable = [
        'status'
    ];

    public function appointmentstatus_gastatus() {
        return $this->hasMany('App\Model\Appointment\GuestAppointmentStatus', 'status_id');
    }

    public function appointmentstatus_rescheduledappointment() {
        return $this->hasMany('App\Model\Appointment\RescheduledAppointment', 'last_status');
    }

    public function appointmentstatus_logcontactedlead() {
        return $this->hasMany('App\Model\Log\LogContactedLead', 'status_id');
    }
}
