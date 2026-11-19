<?php

namespace App\Model\Appointment;

use Illuminate\Database\Eloquent\Model;

class MemberAppointmentStatus extends Model
{
    protected $table = 'member_appointment_status';

    protected $primaryKey = 'ma_id';

    protected $fillable = [
        'ma_id',
        'status_id',
        'c_date',
        's_date',
        'sa_date',
    ];

    public function mastatus_memberappointment() {
        return $this->belongsTo('App\Model\Appointment\MemberAppointment', 'ma_id');
    }

    public function mastatus_appointmentstatus() {
        return $this->belongsTo('App\Model\MasterData\AppointmentStatus', 'status_id');
    }
}
