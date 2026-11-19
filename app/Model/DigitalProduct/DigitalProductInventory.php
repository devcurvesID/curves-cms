<?php

namespace App\Model\DigitalProduct;

use Illuminate\Database\Eloquent\Model;

class DigitalProductInventory extends Model
{
    protected $table = 'digital_product_inventories';

    protected $fillable = [
        'club_id',
        'digital_product_id',
        'selling_price',
        'qty',
        'status',
    ];
    public function digitalproductinventory_club() {
        return $this->belongsTo('App\Model\Club\ClubInfo','club_id');
    }
    public function digitalproductinventory_digitalproduct() {
        return $this->belongsTo('App\Model\DigitalProduct\DigitalProduct', 'digital_product_id');
    }

    public function digitalproductinventory_logdigitalproduct() {
        return $this->hasMany('App\Model\Log\LogDigitalProductInventory', 'digital_product_inventory_id');
    }

    public function digitalproductinventory_digitalproductpayment() {
        return $this->hasMany('App\Model\DigitalProduct\DigitalProductPayment', 'digital_product_inventory_id');
    }
}
