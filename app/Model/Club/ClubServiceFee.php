<?php

namespace App\Model\Club;

use Illuminate\Database\Eloquent\Model;

class ClubServiceFee extends Model
{
    protected $table = 'club_service_fees';

    protected $fillable = [
        'club_id',
        'membership_type_id',
        'sf_amount',
        'sf_freq',
        'mf_amount',
        'mf_freq',
        'pre_pay_discount',
        'pre_pay_discount_value',
        'agreement_renewal_type_id',
        'is_taxable',
        'from_date',
        'thru_date',
    ];

    public function clubservicefee_clubinfo() {
        return $this->belongsTo('App\Model\Club\ClubInfo', 'club_id');
    }

    public function clubservicefee_membershiptype() {
        return $this->belongsTo('App\Model\MasterData\MembershipType', 'membership_type_id');
    }

    public function clubservicefee_agreementrenewaltype() {
        return $this->belongsTo('App\Model\MasterData\AgreementRenewalType', 'agreement_renewal_type_id');
    }
}
