<?php

namespace App\Model\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Services\ApiClientService;

class PaymentMethod extends Model
{
    protected $table = 'mst_payment_methods';

    protected $fillable = [
        'payment_method',
    ];

    public function scopeAvailableForPayment(Builder $query): Builder
    {
        if (!ApiClientService::isEnabled()) {
            $query->whereRaw("UPPER(TRIM(payment_method)) <> ?", ['EZYPAY']);
        }

        return $query;
    }

    public function paymentmethod_memberpayment() {
        return $this->hasMany('App\Model\Payment\MemberPayment', 'payment_method_id');
    }

    public function paymentmethod_productpayment() {
        return $this->hasMany('App\Model\Product\ProductPayment', 'payment_method_id');
    }

    public function paymentmethod_memberdebtpayment() {
        return $this->hasMany('App\Model\Payment\MemberDebtPayment', 'payment_method_id');
    }

    public function paymentmethod_lockerpayment() {
        return $this->hasMany('App\Model\Locker\LockerPayment', 'paymentmethod_id');
    }

    public function paymentmethod_gmpayment() {
        return $this->hasMany('App\Model\GuestMembership\GuestMemberPayment', 'payment_type_id');
    }

    public function paymentmethod_hqcreditpayment() {
        return $this->hasMany('App\Model\HQ\HQCreditPayment', 'payment_type_id');
    }

    public function paymentmethod_cobillpayment() {
        return $this->hasMany('App\Model\HQ\ConsignmentOrderBillPayment', 'payment_type_id');
    }
}
