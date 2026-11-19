<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LogProductInventoryPrice extends Model
{
    protected $table = 'log_product_inventory_prices';

    protected $fillable = [
        'product_inventory_id',
        'price',
        'from_date',
        'thru_date',
    ];

    public function logproductinvprice_productinv() {
        return $this->belongsTo('App\Model\Product\ProductInventory', 'product_inventory_id');
    }
}
