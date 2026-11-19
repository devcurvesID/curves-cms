<?php

namespace App\Model\Product;

use Illuminate\Database\Eloquent\Model;

class ProductRequest extends Model
{
    protected $table ='product_requests';

    protected $fillable = [
    	'request_number',
    	'club_id',
    	'request_status',
    	'request_date',
        'target_club'
    ];

    public function productrequest_club(){
    	return $this->belongsTo('App\Model\Club\ClubInfo','club_id');
    }

    public function productrequest_targetclub(){
        return $this->belongsTo('App\Model\Club\ClubInfo','target_club');
    }

    public function productrequest_productrequestdetail(){
    	return $this->hasMany('App\Model\Product\ProductRequestDetail','product_request_id');
    }

    public function productrequest_productshipment(){
        return $this->hasOne('App\Model\Product\ProductShipment','product_request_id');
    }

    public function productrequest_productshipmentarrival(){
        return $this->hasOne('App\Model\Product\ProductShipmentArrival','product_request_id');
    }
}
