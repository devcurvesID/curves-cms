<?php

namespace App\Model\Appointment;

use Illuminate\Database\Eloquent\Model;

class GuestNoSaleReason extends Model
{
    protected $table = 'no_sale_reasons';

    protected $primaryKey = 'guest_appointment_id';

    protected $fillable = [
        'guest_appointment_id',
        'no_sale_reason_id',
    ];

    public function guestnosalereason_guestappointment() {
        return $this->belongsTo('App\Model\Appointment\GuestAppointment', 'guest_appointment_id');
    }

    public function guestnosalereason_nosalereason() {
        return $this->belongsTo('App\Model\MasterData\NoSaleReason', 'no_sale_reason_id');
    }
}
