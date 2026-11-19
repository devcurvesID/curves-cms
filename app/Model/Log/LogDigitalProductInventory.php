<?php

namespace App\Model\Log;

use Illuminate\Database\Eloquent\Model;

class LogDigitalProductInventory extends Model
{
    protected $table = 'log_digital_product_inventories';

    protected $fillable = [
        'digital_product_inventory_id',
        'user_id',
        'qty_before',
        'qty_after',
        'date',
    ];

    public function logdigitalproduct_digitalproductinventory() {
        return $this->belongsTo('App\Model\DigitalProduct\DigitalProductInventory', 'digital_product_inventory_id');
    }

    public function logdigitalproduct_user() {
        return $this->belongsTo('App\User', 'user_id');
    }
}
