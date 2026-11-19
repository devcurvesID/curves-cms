<?php

namespace App\Model\Payment;

use Illuminate\Database\Eloquent\Model;

class PaymentReceipts extends Model
{
    protected $table = 'payment_receipts';

    public $timestamps = true;
    protected $dates = ['deleted_at', 'created_at', 'updated_at'];

    protected $fillable = [
        'transId', 'details', 'details_ezypay'
    ];

}
