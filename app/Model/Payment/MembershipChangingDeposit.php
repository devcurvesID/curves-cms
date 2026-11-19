<?php

namespace App\Model\Payment;

use Illuminate\Database\Eloquent\Model;

class MembershipChangingDeposit extends Model
{
    protected $table = 'membership_changing_deposits';

    protected $primaryKey = 'payment_id';

    protected $fillable = [
        'payment_id',
        'deposit_amount',
    ];

    public function membershipchangingdeposit_memberpayment() {
        return $this->belongsTo('App\Model\Payment\MemberPayment', 'payment_id');
    }
}
