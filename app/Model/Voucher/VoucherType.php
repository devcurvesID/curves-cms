<?php

namespace App\Model\Voucher;

use Illuminate\Database\Eloquent\Model;

class VoucherType extends Model
{
    protected $table = 'voucher_types';

    protected $fillable = [
        'voucher_type',
        'description',
    ];

    public function vouchertype_voucher() {
        return $this->hasMany('App\Model\Voucher\Voucher', 'type_id');
    }
}
