<?php

namespace App\Model\Accounting;

use Illuminate\Database\Eloquent\Model;

class AmortizationDetail extends Model
{
    protected $table = 'amortization_details';

    protected $fillable = [
        'amortization_header_id',
        'club_id',
        'amortization_date',
        'month_number',
        'amount_paid',
        'status',
        'accounting_status',
    ];

    // belongsTo
    public function amordetail_amorheader() {
        return $this->belongsTo('App\Model\Accounting\AmortizationHeader', 'amortization_header_id');
    }

    public function amordetail_club() {
        return $this->belongsTo('App\Model\Club\ClubInfo', 'club_id');
    }
}
