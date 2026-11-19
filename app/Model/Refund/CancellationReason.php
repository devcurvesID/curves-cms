<?php

namespace App\Model\Refund;

use Illuminate\Database\Eloquent\Model;

class CancellationReason extends Model
{
    protected $table = 'cancellation_reasons';

    protected $primaryKey = 'refund_submission_id';

    protected $fillable = [
        'refund_submission_id',
        'membership_type_id',
        'cancellation_reason_id',
        'cancellation_reason',
    ];

    public function cancellationreason_refundsubmission() {
        return $this->belongsTo('App\Model\Refund\RefundSubmission', 'refund_submission_id');
    }

    public function cancellationreason_membershiptype() {
        return $this->belongsTo('App\Model\MasterData\MembershipType', 'membership_type_id');
    }

    public function cancellationreason_nosalereason() {
        return $this->belongsTo('App\Model\MasterData\NoSaleReason', 'cancellation_reason_id');
    }
}
