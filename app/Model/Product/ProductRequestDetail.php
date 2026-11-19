<?php

namespace App\Model\Product;

use Illuminate\Database\Eloquent\Model;

class ProductRequestDetail extends Model
{
    protected $table = 'product_request_details';

    protected $fillable = [
    	'product_id',
    	'product_request_id',
    	'amount',
    ];

    public function productrequestdetail_product(){
    	return $this->belongsTo('App\Model\Product\Product','product_id');
    }

    public function productrequestdetail_productrequest(){
    	return $this->belongsTo('App\Model\Product\ProductRequest','product_request_id');
    }

    public function productrequestdetail_productshipmentdetail(){
        return $this->hasOne('App\Model\Product\ProductShipmentDetail','product_request_detail_id');
    }

    public function productrequestdetail_productshipmentarrivaldetail(){
        return $this->hasOne('App\Model\Product\ProductShipmentArrivalDetail','product_request_detail_id');
    }
}
