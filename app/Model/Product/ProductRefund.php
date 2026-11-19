<?php

namespace App\Model\Product;

use Illuminate\Database\Eloquent\Model;

class ProductRefund extends Model
{
    protected $table = 'product_refunds';

    protected $fillable = [
        'product_payment_id',
        'staff_id',
        'refund_number',
        'refund_date',
        'refund_reason',
        'refund_amount',
        'refund_status',
        'refund_type',
        'voucher_id'
    ];

    public function productrefund_productpayment() {
        return $this->belongsTo('App\Model\Product\ProductPayment', 'product_payment_id');
    }

    public function productrefund_user() {
        return $this->belongsTo('App\User', 'staff_id');
    }

    public function productrefund_productrefunddetail() {
        return $this->hasMany('App\Model\Product\ProductRefundDetail', 'product_refund_id');
    }

    public function productrefund_productrefundinventory(){
        return $this->hasMany('App\Model\Product\ProductRefundInventory','product_refund_id');
    }

    public function productrefund_voucher(){
        return $this->belongsTo('App\Model\Voucher\Voucher','voucher_id');
    }
}
