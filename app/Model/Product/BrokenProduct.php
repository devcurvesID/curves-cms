<?php

namespace App\Model\Product;

use Illuminate\Database\Eloquent\Model;

class BrokenProduct extends Model
{
    protected $table = 'broken_products';

    protected $fillable = [
    	'product_inventory_id',
    	'quantoty',
    	'input_date',
    	'staff_id',
    ];

    public function brokenproduct_productinventory(){
    	return $this->belongsTo('App\Model\Product\ProductInventory','product_inventory_id');
    }

    public function brokenproduct_user(){
    	return $this->belongsTo('App\User','staff_id');
    }
}
