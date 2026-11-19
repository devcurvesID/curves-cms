<?php

namespace App\Model\Accounting;

use Illuminate\Database\Eloquent\Model;

class AmortizationHeader extends Model
{
    protected $table = 'amortization_headers';

    protected $fillable = [
        'user_id',
        'membership_type_id',
        'amortization_status',
        'contract_start',
        'membership_duration',
        'amortization_month',
        'rest_of_amortization_month',
        'total_amount_due',
        'monthly_bill',
        'status',
    ];

    // belongsTo
    public function amorheader_user() {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function amorheader_membershiptype() {
        return $this->belongsTo('App\Model\MasterData\MembershipType', 'membership_type_id');
    }

    // hasMany
    public function amorheader_amordetail() {
        return $this->hasMany('App\Model\Accounting\AmortizationDetail', 'amortization_header_id');
    }
}
