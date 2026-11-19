<?php

namespace App\Model\MasterData;

use Illuminate\Database\Eloquent\Model;

class AgreementRenewalType extends Model
{
    protected $table = 'mst_agreement_renewal_types';

    protected $fillable = [
        'agreement_renewal_type'
    ];

    public function agreementrenewaltype_clubservicefee() {
        return $this->hasMany('App\Model\Club\ClubServiceFee', 'agreement_renewal_type_id');
    }
}
