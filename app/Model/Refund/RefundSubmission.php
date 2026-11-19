<?php

namespace App\Model\Refund;

use Illuminate\Database\Eloquent\Model;

class RefundSubmission extends Model
{
    protected $table = 'refund_submissions';

    protected $fillable = [
        'user_id',
        'refund_amount',
        'refund_diff_days',
        'refund_submission_date',
        'refund_status',
        'refund_process_date',
        'submit_by',
        'billing_type',
        'membership_type_id',
        'start_membership_date',
        'end_membership_date',
        'refund_number',
    ];

    public function refundsubmission_cancellationreason() {
        return $this->hasOne('App\Model\Refund\CancellationReason', 'refund_submission_id');
    }

    public function refundsubmission_user() {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function refundsubmission_submitter() {
        return $this->belongsTo('App\User', 'submit_by');
    }

    public function refundsubmission_membershiptype() {
        return $this->belongsTo('App\Model\MasterData\MembershipType', 'membership_type_id');
    }
}
