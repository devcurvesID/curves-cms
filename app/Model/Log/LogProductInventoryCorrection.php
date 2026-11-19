<?php

namespace App\Model\Log;

use Illuminate\Database\Eloquent\Model;

class LogProductInventoryCorrection extends Model
{
    protected $table = 'log_product_inventory_corrections';

    protected $fillable = [
    	'product_inventory_id',
    	'staff_id',
    	'qty',
    	'correction_date',
    	'correction_reason'
    ];

    public function logproductinventorycorrection_productinventory(){
    	return $this->belongsTo('App\Model\Product\ProductInventory', 'product_inventory_id');
    }

    public function logproductinventorycorrection_user(){
    	return $this->belongsTo('App\User','staff_id');
    }
}
