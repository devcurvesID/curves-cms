<?php

namespace App\Model\Product;

use Illuminate\Database\Eloquent\Model;

class ProductDiscount extends Model
{
    protected $table = 'product_discounts';

    protected $primaryKey = 'product_inventory_id';

    protected $fillable = [
        'product_inventory_id',
        'discount_percent',
        'discount_value',
        'discount_category',
        'discount_type',
        'from_date',
        'thru_date',
    ];

    public function productdiscount_productinv() {
        return $this->belongsTo('App\Model\Product\ProductInventory', 'product_inventory_id');
    }
}
