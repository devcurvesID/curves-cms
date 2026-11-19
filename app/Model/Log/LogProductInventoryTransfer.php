<?php

namespace App\Model\Log;

use Illuminate\Database\Eloquent\Model;

class LogProductInventoryTransfer extends Model
{
    protected $table = 'log_product_inventory_transfers';

    protected $fillable = [
        'product_inventory_id',
        'qty',
        'from_club',
        'to_club',
        'update_date'
    ];

    public function logproductinvtransfer_productinv() {
        return $this->belongsTo('App\Model\Product\ProductInventory', 'product_inventory_id');
    }

    public function logproductinvtransfer_fromclub() {
        return $this->belongsTo('App\Model\Club\ClubInfo', 'from_club');
    }

    public function logproductinvtransfer_toclub() {
        return $this->belongsTo('App\Model\Club\ClubInfo', 'to_club');
    }
}
