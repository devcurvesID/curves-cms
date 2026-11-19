<?php

namespace App\Model\MasterData;

use Illuminate\Database\Eloquent\Model;

class NoSaleReason extends Model
{
    protected $table = 'mst_no_sale_reasons';

    protected $fillable = [
        'nosale_reason',
    ];

    public function nosalereason_guestnosalereason() {
        return $this->hasMany('App\Model\Appointment\GuestNoSaleReason', 'no_sale_reason_id');
    }

    public function nosalereason_cancellationreason() {
        return $this->hasMany('App\Model\Refund\CancellationReason', 'cancellation_reason_id');
    }

    public function nosalereason_continuemembershiprefund() {
        return $this->hasMany('App\Model\Refund\ContinueMembershipRefund', 'cancellation_reason_id');
    }

    public function reason_inactivereason() {
        return $this->hasMany('App\Model\Member\InactiveMembershipReason', 'reason_id');
    }
}
