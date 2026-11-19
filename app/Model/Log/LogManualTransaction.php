<?php

namespace App\Model\Log;

use Illuminate\Database\Eloquent\Model;

class LogManualTransaction extends Model
{
    protected $table = 'log_manual_transactions';

    protected $fillable = [
        'acc_journal_debit_id',
        'acc_journal_credit_id',
        'club_id',
        'coa_id',
        'debit',
        'credit',
        'information',
        'coa_via_payment_id',
        'account_id',
    ];

    public function logmanualtransaction_accountingaccount(){
        return $this->belongsTo('App\Model\Accounting\AccountingAccount', 'account_id');
    }

    public function logmanualtransaction_accountingjournaldebit(){
        return $this->belongsTo('App\Model\Accounting\AccountingJournal', 'acc_journal_debit_id');
    }

    public function logmanualtransaction_accountingjournalcredit(){
        return $this->belongsTo('App\Model\Accounting\AccountingJournal', 'acc_journal_credit_id');
    }

    public function logmanualtransaction_clubinfo(){
        return $this->belongsTo('App\Model\Club\ClubInfo', 'club_id');
    }

    public function logmanualtransaction_chartofaccountpayment(){
        return $this->belongsTo('App\Model\Accounting\ChartOfAccount', 'coa_id');
    }

    public function logmanualtransaction_chartofaccountviapayment(){
        return $this->belongsTo('App\Model\Accounting\ChartOfAccount', 'coa_via_payment_id');
    }
}
