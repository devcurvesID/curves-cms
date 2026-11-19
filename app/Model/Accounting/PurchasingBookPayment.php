<?php

namespace App\Model\Accounting;

use Illuminate\Database\Eloquent\Model;

class PurchasingBookPayment extends Model
{
    protected $table = 'purchasing_book_payments';

    protected $fillable = [
        'purchasing_book_id',
        'coa_id',
        'payment_date',
        'amount_paid',
    ];

    //belongsTo
    public function purchasingbookpayment_purchasingbook() {
        return $this->belongsTo('App\Model\Accounting\PurchasingBook', 'purchasing_book_id');
    }

    public function purchasingbookpayment_chartofaccount() {
        return $this->belongsTo('App\Model\Accounting\ChartOfAccount', 'coa_id');
    }
}
