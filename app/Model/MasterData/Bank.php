<?php

namespace App\Model\MasterData;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $table = "mst_banks";

    protected $fillable = [
        'bank_name',
        'bank_abv',
        'coa_id',
        'bank_account_number',
    ];

    public function getPrettyBankAttribute(){
        return $this->bank_abv .' - '. $this->bank_name;
    }

    //Has One

    //Has Many
    public function bank_lockerpayment(){
        return $this->hasMany('App\Model\Locker\LockerPayment', 'bank_id');
    }

    public function bank_memberpayment(){
        return $this->hasMany('App\Model\Payment\MemberPayment', 'bank_id');
    }

    public function bank_productpayment(){
        return $this->hasMany('App\Model\Product\ProductPayment', 'bank_id');
    }

    public function bank_gmpayment() {
        return $this->hasMany('App\Model\GuestMembership\GuestMemberPayment', 'bank_id');
    }

    public function bank_memberdebtpayment() {
        return $this->hasMany('App\Model\Payment\MemberDebtPayment', 'bank_id');
    }

    public function bank_hqcreditpayment() {
        return $this->hasMany('App\Model\HQ\HQCreditPayment', 'bank_id');
    }

    public function bank_cobillpayment() {
        return $this->hasMany('App\Model\HQ\ConsignmentOrderBillPayment', 'bank_id');
    }

    //Belong To
    public function bank_chartofaccount(){
        return $this->belongsTo('App\Model\Accounting\ChartOfAccount', 'coa_id');
    }

    //Belong To Many
    public function bank_club() {
        return $this->belongsToMany('App\Model\Club\ClubInfo', 'bank_club', 'id_bank', 'club_id')->withTimestamps();
    }
}
