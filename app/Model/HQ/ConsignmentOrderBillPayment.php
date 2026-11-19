<?php

namespace App\Model\HQ;

use Illuminate\Database\Eloquent\Model;

class ConsignmentOrderBillPayment extends Model
{
    protected $table = 'consignment_order_bill_payments';

    protected $fillable = [
        'consignment_order_bill_id',
        'payment_number',
        'payment_date',
        'total',
        'information',
        'payment_type_id',
        'bank_id',
        'staff_id',
    ];

    // belongsTo
    public function cobillpayment_cobill() {
        return $this->belongsTo('App\Model\HQ\ConsignmentOrderBill', 'consignment_order_bill_id');
    }

    public function cobillpayment_paymentmethod() {
        return $this->belongsTo('App\Model\MasterData\PaymentMethod', 'payment_type_id');
    }

    public function cobillpayment_bank() {
        return $this->belongsTo('App\Model\MasterData\Bank', 'bank_id');
    }

    public function cobillpayment_staff() {
        return $this->belongsTo('App\User', 'staff_id');
    }
}
