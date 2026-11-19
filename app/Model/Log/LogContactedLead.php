<?php

namespace App\Model\Log;

use Illuminate\Database\Eloquent\Model;

class LogContactedLead extends Model
{
    protected $table = 'log_contacted_leads';

    protected $fillable = [
        'lead_id',
        'staff_id',
        'status_id',
    ];

    public function logcontactedlead_guestappointment() {
        return $this->belongsTo('App\Model\Appointment\GuestAppointment', 'guest_appointment_id');
    }

    public function logcontactedlead_user() {
        return $this->belongsTo('App\User', 'staff_id');
    }

    public function logcontactedlead_appointmentstatus() {
        return $this->belongsTo('App\Model\MasterData\AppointmentStatus', 'status_id');
    }
}
