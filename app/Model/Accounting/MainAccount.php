<?php

namespace App\Model\Accounting;

use Illuminate\Database\Eloquent\Model;

class MainAccount extends Model
{
    protected $table = 'main_accounts';

    protected $fillable = [
    	'account_number',
    	'account_name',
    	'account_abv_name',
    	'account_category',
    	'account_type',
    	'account_subtotal',
    	'account_total_calculation',
    	'user_id',
    ];

    //hasOne

    //hasMany
    public function mainaccount_chartofaccount(){
    	return $this->hasMany('App\Model\Accounting\ChartOfAccount','main_account_id');
    }

    public function mainaccount_coadefault(){
        return $this->hasMany('App\Model\MasterData\CoaDefault','main_account_id');
    }

    //belongsToOne
    public function mainaccount_user(){
    	return $this->belongsTo('App\User','user_id');
    }

    //belongsToMany
}
