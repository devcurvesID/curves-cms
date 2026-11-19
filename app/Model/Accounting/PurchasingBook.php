<?php

namespace App\Model\Accounting;

use Illuminate\Database\Eloquent\Model;

class PurchasingBook extends Model
{
    protected $table = 'purchasing_books';

    protected $fillable = [
        'purchasing_date',
        'coa_id',
        'purchasing_info',
        'amount_due',
        'amount_paid',
        'rest_of_bill',
        'payment_status',
        'club_id',
        'invoice_number',
        'jubilant_id'
    ];

    //hasMany
    public function purchasingbook_purchasingbookpayment() {
        return $this->hasMany('App\Model\Accounting\PurchasingBookPayment', 'purchasing_book_id');
    }

    //belongsTo
    public function purchasingbook_chartofaccount() {
        return $this->belongsTo('App\Model\Accounting\ChartOfAccount', 'coa_id');
    }

    public function purchasingbook_club(){
        return $this->belongsTo('App\Model\Club\ClubInfo','club_id');
    }
}
