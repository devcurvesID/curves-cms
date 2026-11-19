<?php

namespace App\Model\Payment;

use Illuminate\Database\Eloquent\Model;

class MemberDebtPayment extends Model
{
    protected $table = 'member_debt_payments';

    protected $fillable = [
        'member_payment_id',
        'amount_due',
        'amount_paid',
        'rest_of_bill',
        'payment_date',
        'payment_method_id',
        'bank_id',
    ];

    public function memberdebtpayment_memberpayment() {
        return $this->belongsTo('App\Model\Payment\MemberPayment', 'member_payment_id');
    }

    public function memberdebtpayment_paymentmethod() {
        return $this->belongsTo('App\Model\MasterData\PaymentMethod', 'payment_method_id');
    }

    public function memberdebtpayment_bank() {
        return $this->belongsTo('App\Model\MasterData\Bank', 'bank_id');
    }
}
