<?php

namespace App\Model\Locker;

use Illuminate\Database\Eloquent\Model;

class LockerPayment extends Model
{
    protected $table = 'locker_payments';

    protected $fillable = [
        'memberlocker_id',
        'locker_id',
        'payment_number',
        'staff_id',
        'price',
        'quantity',
        'subtotal',
        'pay',
        'paymentmethod_id',
        'payment_date',
        'taxtype_id',
        'tax_amount',
        'bank_id',
    ];

    public function lockerpayment_locker() {
        return $this->belongsTo('App\Model\MasterData\Locker', 'locker_id');
    }

    public function lockerpayment_memberlocker() {
        return $this->belongsTo('App\Model\Locker\MemberLocker', 'memberlocker_id');
    }

    public function lockerpayment_user(){
        return $this->belongsTo('App\User','staff_id');
    }

    public function lockerpayment_taxtype() {
        return $this->belongsTo('App\Model\Tax\Taxtype', 'tax_type_id');
    }

    public function lockerpayment_paymentmethod() {
        return $this->belongsTo('App\Model\MasterData\PaymentMethod', 'paymentmethod_id');
    }

    public function  lockerpayment_bank(){
        return $this->belongsTo('App\Model\MasterData\Bank', 'bank_id');
    }
}
