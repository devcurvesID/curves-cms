<?php

namespace App\Model\Appointment;

use Illuminate\Database\Eloquent\Model;

class GuestAppointment extends Model
{
    protected $table = 'guest_appointments';

    protected $fillable = [
        'guest_id',
        'staff_id',
        'fa_status',
        'fa_date',
        'fa_hour',
        'guest_status_after',
        'club_id',
    ];

    public function guestappointment_ganotes() {
        return $this->hasOne('App\Model\Appointment\GuestAppointmentNote', 'ga_id');
    }

    public function guestappointment_gastatus() {
        return $this->hasOne('App\Model\Appointment\GuestAppointmentStatus', 'ga_id');
    }

    public function guestappointment_guestnosalereason() {
        return $this->hasOne('App\Model\Appointment\GuestNoSaleReason', 'guest_appointment_id');
    }

    public function guestappointment_logcontactedlead() {
        return $this->hasMany('App\Model\Log\LogContactedLead', 'guest_appointment_id');
    }

    /*public function guestappointment_rescheduledappointment() {
        return $this->hasMany('App\Model\Appointment\RescheduledAppointment', 'appointment_id');
    }*/

    public function guestappointment_buddyreferrallead() {
        return $this->belongsTo('App\Model\Guest\BuddyReferralLead', 'guest_id');
    }

    public function guestappointment_user() {
        return $this->belongsTo('App\User', 'staff_id');
    }

    public function guestappointment_clubinfo() {
        return $this->belongsTo('App\Model\Club\ClubInfo', 'club_id');
    }
}
