<?php

namespace App\Model\DigitalProduct;

use Illuminate\Database\Eloquent\Model;

class DigitalProduct extends Model
{
    protected $table = 'digital_products';

    protected $fillable = [
        'item_name',
        'default_price',
        'item_image',
        'notes',
        'deleted_date',
    ];
    public function digitalproduct_digitalproductinventory() {
        return $this->hasMany('App\Model\DgitalProduct\DigitalProductInventory', 'digital_product_id');
    }
}
