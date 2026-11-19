<?php

namespace App\Model\HQ;

use Illuminate\Database\Eloquent\Model;

class HQCreditPayment extends Model
{
    protected $table = 'hq_credit_payments';

    protected $fillable = [
        'hq_credit_id',
        'payment_number',
        'payment_date',
        'total',
        'information',
        'payment_type_id',
        'bank_id',
        'staff_id',
    ];

    // belongsTo
    public function hqcreditpayment_hqcredit() {
        return $this->belongsTo('App\Model\HQ\HQCredit', 'hq_credit_id');
    }

    public function hqcreditpayment_paymentmethod() {
        return $this->belongsTo('App\Model\MasterData\PaymentMethod', 'payment_type_id');
    }

    public function hqcreditpayment_bank() {
        return $this->belongsTo('App\Model\MasterData\Bank', 'bank_id');
    }

    public function hqcreditpayment_staff() {
        return $this->belongsTo('App\User', 'staff_id');
    }
}
