<?php

namespace App\Model\HQ;

use Illuminate\Database\Eloquent\Model;

class ConsignmentOrder extends Model
{
    protected $table = 'consignment_orders';

    protected $fillable = [
        'club_dest_id',
        'hq_from_id',
        'staff_id',
        'order_number',
        'order_date',
        'subtotal',
        'discount',
        'total',
        'delivery_order',
        'is_receive'
    ];

    // hasMany
    public function co_codetail() {
        return $this->hasMany('App\Model\HQ\ConsignmentOrderDetail', 'consignment_order_id');
    }

    // belongsTo
    public function co_club() {
        return $this->belongsTo('App\Model\Club\ClubInfo', 'club_dest_id');
    }

    public function co_hq() {
        return $this->belongsTo('App\Model\Club\ClubInfo', 'hq_from_id');
    }

    public function co_staff() {
        return $this->belongsTo('App\User', 'staff_id');
    }
}
