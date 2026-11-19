<?php

namespace App\Model\Product;

use Illuminate\Database\Eloquent\Model;

class PromoProduct extends Model
{
    protected $table = 'mst_promo_products';

    protected $fillable = [
        'promo_id',
        'product_id',
        'qty',
    ];

    // belongsTo
    public function promoproduct_product() {
        return $this->belongsTo('App\Model\Product\Product', 'promo_id');
    }

    public function productpromo_product() {
        return $this->belongsTo('App\Model\Product\Product', 'product_id');
    }
}
