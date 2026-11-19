<?php

namespace App\Model\Voucher;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $table = 'vouchers';

    protected $fillable = [
        'identity_number',
        'from_date',
        'thru_date',
        'type_id',
        'status',
        'voucher_base',
        'value',
        'percent',
    ];

    public function voucher_vouchertype() {
        return $this->belongsTo('App\Model\Voucher\VoucherType', 'type_id');
    }

    public function voucher_productrefund(){
        return $this->hasMany('App\Model\Product\ProductRefund','voucher_id');
    }
}
