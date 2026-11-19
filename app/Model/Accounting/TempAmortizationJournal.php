<?php

namespace App\Model\Accounting;

use Illuminate\Database\Eloquent\Model;

class TempAmortizationJournal extends Model
{
    protected $table = 'temp_amortization_journals';

    protected $fillable = [
        'jubilant_id',
        'club_id',
        'coa_id',
        'trans_date',
        'bank_date',
        'debit',
        'credit',
        'information',
        'approved',
        'd_or_c',
        'pair',
    ];


    // belongsTo
    public function tempamorjournal_jubilant() {
        return $this->belongsTo('App\Model\Jubilant\Jubilant', 'jubilant_id');
    }

    public function tempamorjournal_club() {
        return $this->belongsTo('App\Model\Club\ClubInfo', 'club_id');
    }

    public function tempamorjournal_coa() {
        return $this->belongsTo('App\Model\Accounting\ChartOfAccount', 'coa_id');
    }
}
