<?php

namespace App\Model\Accounting;

use Illuminate\Database\Eloquent\Model;

class AccountingAccount extends Model
{
    protected $table = 'accounting_accounts';

    protected $fillable = [
        'username',
        'password',
        'encrypted_password',
        'name',
    ];

    public function accounting_club() {
        return $this->belongsToMany('App\Model\Club\ClubInfo', 'accounting_clubs', 'accounting_id', 'club_id')->withTimestamps();
    }

    public function accounting_jubilant(){
        return $this->belongsToMany('App\Model\Jubilant\Jubilant','accounting_jubilants','accounting_id','jubilant_id')->withTimestamps();
    }

    public function accountingaccount_logmanualtransaction(){
        return $this->hasMany('App\Model\Log\LogManualTransaction', 'account_id');
    }
}
