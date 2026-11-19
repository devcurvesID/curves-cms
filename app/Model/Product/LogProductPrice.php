<?php

namespace App\Model\Product;

use Illuminate\Database\Eloquent\Model;

class LogProductPrice extends Model
{
    protected $table = 'log_mst_product_prices';

    protected $fillable = [
        'product_id',
        'price',
        'from_date',
        'thru_date',
        'consignment_price',
    ];

    public function logproductprice_product() {
        return $this->belongsTo('App\Model\Product\Product', 'product_id');
    }
}
