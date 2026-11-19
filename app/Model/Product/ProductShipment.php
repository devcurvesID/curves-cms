<?php

namespace App\Model\Product;

use Illuminate\Database\Eloquent\Model;

class ProductShipment extends Model
{
    protected $table = 'product_shipments';

    protected $primaryKey = 'product_request_id';

    protected $fillable = [
    	'product_request_id',
    	'staff_id',
    	'club_id',
    	'accept_date',
    	'send_date',
    	'shipment_status',
    	'notes',
    ];

    public function productshipment_productrequest(){
    	return $this->belongsTo('App\Model\Product\ProductRequest','product_request_id');
    }

    public function productshipment_user(){
    	return $this->belongsTo('App\User','staff_id');
    }

    public function productshipment_club(){
    	return $this->belongsTo('App\Model\Club\ClubInfo','club_id');
    }
}
