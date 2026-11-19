<?php

namespace App\Model\HQ;

use Illuminate\Database\Eloquent\Model;

class HQCredit extends Model
{
    protected $table = 'hq_credits';

    protected $fillable = [
        'staff_id',
        'jubilant_id',
        'invoice_date',
        'invoice_number',
        'coa_id',
        'information',
        'subtotal',
        'amount_paid',
        'rest_of_bill',
        'credit_status',
    ];

    // hasMany
    public function hqcredit_hqcreditpayment() {
        return $this->hasMany('App\Model\HQ\HQCreditPayment', 'hq_credit_id');
    }

    // belongsTo
    public function hqcredit_user() {
        return $this->belongsTo('App\User', 'staff_id');
    }

    public function hqcredit_jubilant() {
        return $this->belongsTo('App\Model\Jubilant\Jubilant', 'jubilant_id');
    }

    public function hqcredit_coa() {
        return $this->belongsTo('App\Model\Accounting\ChartOfAccount', 'coa_id');
    }
}
