<?php

namespace App\Model\GuestMembership;

use Illuminate\Database\Eloquent\Model;

class GuestMemberPayment extends Model
{
    protected $table = 'guest_member_payments';

    protected $fillable = [
        'guest_membership_member_id',
        'payment_type_id',
        'bank_id',
        'payment_date',
        'amount_due',
        'amount_paid',
        'rest_of_bill',
        'payment_status',
        'payment_number',
    ];

    //belongsTo
    public function gmpayment_gmmember() {
        return $this->belongsTo('App\Model\GuestMembership\GuestMembershipMember', 'guest_membership_member_id');
    }

    public function gmpayment_paymentmethod() {
        return $this->belongsTo('App\Model\MasterData\PaymentMethod', 'payment_type_id');
    }

    public function gmpayment_bank() {
        return $this->belongsTo('App\Model\MasterData\Bank', 'bank_id');
    }
}
