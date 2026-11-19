<?php

namespace App\Model\Product;

use Illuminate\Database\Eloquent\Model;

class ProductShipmentArrivalDetail extends Model
{
    protected $table = 'product_shipment_arrival_details';

    protected $primaryKey = 'product_request_detail_id';

    protected $fillable = [
    	'product_request_detail_id',
    	'received_amount',
    	'receiver_notes',
    	'deficit'
	];

	public function productshipmentarrivaldetail_productrequestdetail(){
		return $this->belongsTo('App\Model\Product\ProductRequestDetail','product_request_detail_id');
	}
}
