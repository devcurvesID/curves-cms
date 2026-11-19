<?php

namespace App\Model\Product;

use Illuminate\Database\Eloquent\Model;

class ProductShipmentArrival extends Model
{
    protected $table = 'product_shipment_arrivals';

    protected $primaryKey = 'product_request_id';

    protected $fillable = [
    	'product_request_id',
    	'arrival_date',
    ];

    public function productshipmentarrival_productrequest(){
    	return $this->belongsTo('App\Model\Product\ProductRequest','product_request_id');
    }
}
