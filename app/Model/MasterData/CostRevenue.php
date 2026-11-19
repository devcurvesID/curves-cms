<?php

namespace App\Model\MasterData;

use Illuminate\Database\Eloquent\Model;

class CostRevenue extends Model
{
    protected $table = "mst_cost_revenues";

    protected $fillable = [
        'account_info',
        'default_transaction_info',
        'coa_id',
    ];

    //Has One

    //Belong To Many
    public function costrevenue_chartofaccount(){
        return $this->belongsTo('App\Model\Accounting\ChartOfAccount', 'coa_id');
    }

    //Has Many
}
