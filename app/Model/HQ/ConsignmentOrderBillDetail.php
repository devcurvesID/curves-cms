<?php

namespace App\Model\HQ;

use Illuminate\Database\Eloquent\Model;

class ConsignmentOrderBillDetail extends Model
{
    protected $table = 'consignment_order_bill_details';

    protected $fillable = [
        'consignment_order_bill_id',
        'product_payment_id',
    ];

    // belongsTo
    public function cobilldetail_cobill() {
        return $this->belongsTo('App\Model\HQ\ConsignmentOrderBill', 'consignment_order_bill_id');
    }

    public function cobilldetail_productpayment() {
        return $this->belongsTo('App\Model\Product\ProductPayment', 'product_payment_id');
    }
}
