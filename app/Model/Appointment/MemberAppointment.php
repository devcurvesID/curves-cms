<?php

namespace App\Model\Appointment;

use Illuminate\Database\Eloquent\Model;

class MemberAppointment extends Model
{
    protected $table = 'member_appointments';

    protected $fillable = [
        'user_id',
        'staff_id',
        'ap_type_id',
        'app_date',
        'app_hour',
    ];

    public function memberappointment_memberappointmentnote() {
        return $this->hasOne('App\Model\Appointment\MemberAppointmentNote', 'ma_id');
    }

    public function memberappointment_mastatus() {
        return $this->hasOne('App\Model\Appointment\MemberAppointmentStatus', 'ma_id');
    }

    public function memberappointment_user() {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function memberappointment_staff() {
        return $this->belongsTo('App\User', 'staff_id');
    }

    public function memberappointment_appointmenttype() {
        return $this->belongsTo('App\Model\MasterData\AppointmentType', 'ap_type_id');
    }
}
