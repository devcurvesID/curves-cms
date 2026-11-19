<?php

namespace App\Model\Refund;

use Illuminate\Database\Eloquent\Model;

class ContinueMembershipRefund extends Model
{
    protected $table = 'continue_membership_refunds';

    protected $fillable = [
        'continue_id',
        'payment_id',
        'refund_status',
        'refund_process_date',
        'submit_by',
        'cancellation_reason_id',
        'cancellation_reason',
    ];

    public function continuemembershiprefund_continuemembership() {
        return $this->belongsTo('App\Model\Member\ContinueMembership', 'continue_id');
    }

    public function continuemembershiprefund_memberpayment() {
        return $this->belongsTo('App\Model\Payment\MemberPayment', 'payment_id');
    }

    public function continuemembershiprefund_staff() {
        return $this->belongsTo('App\User', 'submit_by');
    }

    public function continuemembershiprefund_nosalereason() {
        return $this->belongsTo('App\Model\MasterData\NoSaleReason', 'cancellation_reason_id');
    }
}
