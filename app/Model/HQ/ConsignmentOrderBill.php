<?php

namespace App\Model\HQ;

use Illuminate\Database\Eloquent\Model;

class ConsignmentOrderBill extends Model
{
    protected $table = 'consignment_order_bills';

    protected $fillable = [
//        'club_dest_id',
        'jubilant_id',
        'hq_from_id',
        'staff_id',
        'co_bill_number',
        'co_bill_date',
        'co_bill_due_date',
        'total',
        'amount_paid',
        'rest_of_bill',
        'status',
    ];

    // hasMany
    public function cobill_cobilldetail() {
        return $this->hasMany('App\Model\HQ\ConsignmentOrderBillDetail', 'consignment_order_bill_id');
    }

    public function cobill_cobillpayment() {
        return $this->hasMany('App\Model\HQ\ConsignmentOrderBillPayment', 'consignment_order_bill_id');
    }

    // belongsTo
//    public function cobill_club() {
//        return $this->belongsTo('App\Model\Club\ClubInfo', 'club_dest_id');
//    }

    public function cobill_jubilant() {
        return $this->belongsTo('App\Model\Jubilant\Jubilant', 'jubilant_id');
    }

    public function cobill_hq() {
        return $this->belongsTo('App\Model\Club\ClubInfo', 'hq_from_id');
    }

    public function cobill_staff() {
        return $this->belongsTo('App\User', 'staff_id');
    }
}
