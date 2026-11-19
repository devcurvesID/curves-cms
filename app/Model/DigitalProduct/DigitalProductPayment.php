<?php

namespace App\Model\DigitalProduct;

use Illuminate\Database\Eloquent\Model;

class DigitalProductPayment extends Model
{
    protected $table = 'digital_product_payments';

    protected $primaryKey = 'member_payment_id';

    protected $fillable = [
        'member_payment_id',
        'digital_product_inventory_id',
        'price',
        'qty'
    ];

    // belongsTo
    public function digitalproductpayment_memberpayment() {
        return $this->belongsTo('App\Model\Payment\MemberPayment', 'member_payment_id');
    }

    public function digitalproductpayment_digitalproductinventory() {
        return $this->belongsTo('App\Model\DigitalProduct\DigitalProductInventory', 'digital_product_inventory_id');
    }
}
