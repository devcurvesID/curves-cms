<?php

namespace App\Model\Product;

use Illuminate\Database\Eloquent\Model;

class ProductRefundInventory extends Model
{
    protected $table = 'product_refund_inventories';

    protected $fillable = [
    	'product_id',
    	'quantity',
    	'product_refund_id'
    ];

    public function productrefundinventory_product(){
    	return $this->belongsTo('App\Model\Product\Product','product_id');
    }

    public function productrefundinventory_productrefund(){
    	return $this->belongsTo('App\Model\Product\ProductRefund','product_refund_id');
    }
}
