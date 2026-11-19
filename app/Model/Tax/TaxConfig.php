<?php

namespace App\Model\Tax;

use Illuminate\Database\Eloquent\Model;

class TaxConfig extends Model
{
    protected $table = 'tax_configs';

    protected $fillable = [
        'club_id',
        'tax_type_id',
        'rate',
        'from_date',
        'thru_date',
    ];

    public function taxconfig_taxtype() {
        return $this->belongsTo('App\Model\Tax\Taxtype', 'tax_type_id');
    }

    public function taxconfig_clubinfo() {
        return $this->belongsTo('App\Model\Club\ClubInfo', 'club_id');
    }
}
