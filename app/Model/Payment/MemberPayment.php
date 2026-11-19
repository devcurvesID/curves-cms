<?php

namespace App\Model\Payment;

use Illuminate\Database\Eloquent\Model;

class MemberPayment extends Model
{
    protected $table = 'member_payments';

    protected  $fillable = [
        'user_id',
        'sf_amount',
        'mf_amount',
        'dc_amount',
        'tax_type_id',
        'tax_amount',
        'tax_percentage',
        'amount_due',
        'payment_category',
        'payment_date',
        'payment_method_id',
        'amount_paid',
        'rest_of_bill',
        'payment_status',
        'bank_id',
        'renewal',
        'conversion',
        'deposit_amount',
    ];

    public function memberpayment_memberpaymentvoucher() {
        return $this->hasOne('App\Model\Payment\MemberPaymentVoucher', 'member_payment_id');
    }

    public function memberpayment_membershipchangingdeposit() {
        return $this->hasOne('App\Model\Payment\MembershipChangingDeposit', 'payment_id');
    }

    public function memberpayment_digitalproductpayment() {
        return $this->hasOne('App\Model\DigitalProduct\DigitalProductPayment', 'member_payment_id');
    }

    public function memberpayment_continuemembershiprefund() {
        return $this->hasMany('App\Model\Refund\ContinueMembershipRefund', 'payment_id');
    }

    public function memberpayment_memberdebtpayment() {
        return $this->hasMany('App\Model\Payment\MemberDebtPayment', 'member_payment_id');
    }

    public function memberpayment_user() {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function memberpayment_taxtype() {
        return $this->belongsTo('App\Model\Tax\Taxtype', 'tax_type_id');
    }

    public function memberpayment_paymentmethod() {
        return $this->belongsTo('App\Model\MasterData\PaymentMethod', 'payment_method_id');
    }

    public function memberpayment_bank(){
        return $this->belongsTo('App\Model\MasterData\Bank', 'bank_id');
    }
}
