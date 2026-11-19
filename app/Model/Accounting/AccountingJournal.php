<?php

namespace App\Model\Accounting;

use Illuminate\Database\Eloquent\Model;

class AccountingJournal extends Model
{
    protected $table = 'accounting_journals';

    protected $fillable = [
    	'coa_id',
    	'trans_date',
    	'debit',
    	'credit',
    	'information',
    	'club_id',
    ];

    //hasOne

    //hasMany
    public function accountingjournaldebit_logmanualtransaction(){
        return $this->hasMany('App\Model\Log\LogManualTransaction', 'acc_journal_debit_id');
    }

    public function accountingjournalcredit_logmanualtransaction(){
        return $this->hasMany('App\Model\Log\LogManualTransaction', 'acc_journal_credit_id');
    }

    //belongsToOne
    public function accountingjournal_chartofaccount(){
    	return $this->belongsTo('App\Model\Accounting\ChartOfAccount','coa_id');
    }

    public function accountingjournal_club(){
    	return $this->belongsTo('App\Model\Club\ClubInfo','club_id');
    }

    //belongsToMany
}
