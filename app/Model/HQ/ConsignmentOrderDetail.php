<?php

namespace App\Model\HQ;

use Illuminate\Database\Eloquent\Model;

class ConsignmentOrderDetail extends Model
{
    protected $table = 'consignment_order_details';

    protected $fillable = [
        'consignment_order_id',
        'product_id',
        'qty',
        'price_per_pcs',
    ];

    // belongsTo
    public function codetail_co() {
        return $this->belongsTo('App\Model\HQ\ConsignmentOrder', 'consignment_order_id');
    }

    public function codetail_product() {
        return $this->belongsTo('App\Model\Product\Product', 'product_id');
    }
}
