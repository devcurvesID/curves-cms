<?php

namespace App\Model\Jubilant;

use Illuminate\Database\Eloquent\Model;

class Jubilant extends Model
{
    protected $table = 'mst_jubilants';

    protected $fillable = [
        'jubilant_name',
        'type',
    ];

    //belongsToMany
    public function jubilant_accounting(){
        return $this->belongsToMany('App\Model\Accounting\AccountingAccount','accounting_jubilants','jubilant_id','accounting_id')->withTimestamps();
    }

    // hasMany
    public function jubilant_club() {
        return $this->hasMany('App\Model\Club\ClubInfo', 'jubilant_id');
    }

    public function jubilant_coa() {
        return $this->hasMany('App\Model\Accounting\ChartOfAccount', 'jubilant_id');
    }

    public function jubilant_hqcredit() {
        return $this->hasMany('App\Model\HQ\HQCredit', 'jubilant_id');
    }

    public function jubilant_tempjournal() {
        return $this->hasMany('App\Model\Accounting\TempJournal', 'jubilant_id');
    }

    public function jubilant_cobill() {
        return $this->hasMany('App\Model\HQ\ConsignmentOrderBill', 'jubilant_id');
    }

    public function jubilant_accjournalv2() {
        return $this->hasMany('App\Model\Accounting\AccountingJournalV2', 'jubilant_id');
    }

    public function jubilant_tempamorjournal() {
        return $this->hasMany('App\Model\Accounting\TempAmortizationJournal', 'jubilant_id');
    }

    public function jubilant_meb() {
        return $this->hasMany('App\Model\Accounting\MonthlyEndingBalance', 'jubilant_id');
    }
}
