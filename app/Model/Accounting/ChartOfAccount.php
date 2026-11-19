<?php

namespace App\Model\Accounting;

use Illuminate\Database\Eloquent\Model;

class ChartOfAccount extends Model
{
    protected $table = 'chart_of_accounts';

    protected $fillable = [
    	'coa_number',
    	'coa_name',
    	'coa_abv_name',
    	'main_account_id',
    	'user_id',
        'jubilant_id',
    ];

    //hasOne

    //hasMany
    public function chartofaccount_accountingjournal(){
        return $this->hasMany('App\Model\Accounting\AccountingJournal','coa_id');
    }

    public function chartofaccount_bank(){
        return $this->hasMany('App\Model\MasterData\Bank', 'coa_id');
    }

    public function chartofaccount_costrevenue(){
        return $this->hasMany('App\Model\MasterData\CostRevenue', 'coa_id');
    }

    public function chartofaccount_memorialjournal(){
        return $this->hasMany('App\Model\Accounting\MemorialJournal','coa_id');
    }

    public function chartofaccount_logmanualtransactionpayment(){
        return $this->hasMany('App\Model\Log\LogManualTransaction','coa_id');
    }

    public function chartofaccount_logmanualtransactionviapayment(){
        return $this->hasMany('App\Model\Log\LogManualTransaction','coa_via_payment_id');
    }

    public function chartofaccount_purchasingbook() {
        return $this->hasMany('App\Model\Accounting\PurchasingBook', 'coa_id');
    }

    public function chartofaccount_purchasingbookpayment() {
        return $this->hasMany('App\Model\Accounting\PurchasingBookPayment', 'coa_id');
    }

    public function coa_hqcredit() {
        return $this->hasMany('App\Model\HQ\HQCredit', 'coa_id');
    }

    public function coa_tempjournal() {
        return $this->hasMany('App\Model\Accounting\TempJournal', 'coa_id');
    }

    public function coa_accjournalv2() {
        return $this->hasMany('App\Model\Accounting\AccountingJournalV2', 'coa_id');
    }

    public function coa_tempamorjournal() {
        return $this->hasMany('App\Model\Accounting\AmortizationDetail', 'coa_id');
    }

    public function coa_meb() {
        return $this->hasMany('App\Model\Accounting\MonthlyEndingBalance', 'coa_id');
    }

    //belongsToOne
    public function chartofaccount_mainaccount(){
    	return $this->belongsTo('App\Model\Accounting\MainAccount','main_account_id');
    }

    public function chartofaccount_user(){
    	return $this->belongsTo('App\User','user_id');
    }

    public function coa_jubilant() {
        return $this->belongsTo('App\Model\Jubilant\Jubilant', 'jubilant_id');
    }

    //belongsToMany
}
