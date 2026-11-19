<?php

namespace App\Model\Product;

use Illuminate\Database\Eloquent\Model;

class ProductRefundDetail extends Model
{
    protected $table = 'product_refund_details';

    protected $fillable = [
        'product_refund_id',
        'product_payment_detail_id',
        'refund_quantity',
        'price'
    ];

    public function productrefunddetail_productrefund() {
        return $this->belongsTo('App\Model\Product\ProductRefund', 'product_refund_id');
    }

    public function productrefunddetail_productpaymentdetail() {
        return $this->belongsTo('App\Model\Product\ProductPaymentDetail', 'product_payment_detail_id');
    }
}
