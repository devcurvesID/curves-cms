<?php

namespace App\Model\Product;

use Illuminate\Database\Eloquent\Model;

class ProductTypes extends Model
{
    protected $table = 'mst_product_types';

    protected $fillable = [
        'type',
        'icon',
    ];

    public function producttype_product() {
        return $this->hasMany('App\Model\Product\Product', 'product_type_id');
    }
}
