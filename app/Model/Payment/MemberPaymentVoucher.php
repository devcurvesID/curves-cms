<?php

namespace App\Model\Payment;

use Illuminate\Database\Eloquent\Model;

class MemberPaymentVoucher extends Model
{
    protected $table = 'member_payment_vouchers';

    protected $primaryKey = 'member_payment_id';

    protected $fillable = [
        'member_payment_id',
        'voucher_code',
        'voucher_value',
    ];

    public function memberpaymentvoucher_memberpayment() {
        return $this->belongsTo('App\Model\Payment\MemberPayment', 'member_payment_id');
    }
}
