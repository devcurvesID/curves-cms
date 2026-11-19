<?php

namespace App\Model\MasterData;

use Illuminate\Database\Eloquent\Model;

class AppointmentType extends Model
{
    protected $table = 'mst_appointment_types';

    protected $fillable = [
        'type'
    ];

    public function appointmenttype_memberappointment() {
        return $this->hasMany('App\Model\Appointment\MemberAppointment', 'ap_type_id');
    }
}
