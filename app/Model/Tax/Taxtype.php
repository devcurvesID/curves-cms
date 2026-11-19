<?php

namespace App\Model\Tax;

use Illuminate\Database\Eloquent\Model;

class Taxtype extends Model
{
    protected $table = 'tax_types';

    protected $fillable = [
        'tax_type',
    ];

    public function taxtype_taxconfig() {
        return $this->hasMany('App\Model\Tax\TaxConfig', 'tax_type_id');
    }

    public function taxtype_memberpayment() {
        return $this->hasMany('App\Model\Payment\MemberPayment', 'tax_type_id');
    }

    public function taxtype_productpayment() {
        return $this->hasMany('App\Model\Product\ProductPayment', 'tax_type_id');
    }

    public function taxtype_lockerpayment() {
        return $this->hasMany('App\Model\Locker\LockerPayment', 'taxtype_id');
    }
}
