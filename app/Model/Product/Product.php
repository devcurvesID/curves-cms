<?php

namespace App\Model\Product;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'mst_products';

    protected $fillable = [
        'product_type_id',
        'item_name',
        'default_price',
        'is_taxable',
        'accessibility',
        'status',
        'product_image',
        'notes',
        'is_promo',
        'consignment_price',
    ];

    public function product_rewardhistory() {
        return $this->hasMany('App\Model\Log\RewardHistory', 'product_id');
    }

    public function product_logproductprice() {
        return $this->hasMany('App\Model\Product\LogProductPrice', 'product_id');
    }

    public function product_productinv() {
        return $this->hasMany('App\Model\Product\ProductInventory', 'product_id');
    }

    public function product_productpaymentdetail() {
        return $this->hasMany('App\Model\Product\ProductPaymentDetail', 'product_id');
    }

    public function product_productrequestdetail(){
        return $this->hasMany('App\Model\Product\ProductRequestDetail','product_id');
    }

    public function product_productrefundinventory(){
        return $this->hasMany('App\Model\Product\ProductRefundInventory','product_id');
    }

    public function product_codetail() {
        return $this->hasMany('App\Model\HQ\ConsignmentOrderDetail', 'product_id');
    }

    public function product_promoproduct() {
        return $this->hasMany('App\Model\Product\PromoProduct', 'promo_id');
    }

    public function product_productpromo() {
        return $this->hasMany('App\Model\Product\PromoProduct', 'product_id');
    }

    public function product_producttype() {
        return $this->belongsTo('App\Model\Product\ProductTypes', 'product_type_id');
    }
}
