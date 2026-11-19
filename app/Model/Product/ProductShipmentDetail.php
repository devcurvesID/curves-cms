<?php

namespace App\Model\Product;

use Illuminate\Database\Eloquent\Model;

class ProductShipmentDetail extends Model
{
    protected $table = 'product_shipment_details';

    protected $primaryKey = 'product_request_detail_id';

    protected $fillable = [
    	'product_request_detail_id',
    	'send_amount',
    ];

    public function productshipmentdetail_productrequestdetail(){
    	return $this->belongsTo('App\Model\Product\ProductRequestDetail','product_request_detail_id');
    }
}
