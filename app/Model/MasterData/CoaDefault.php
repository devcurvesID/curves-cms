<?php

namespace App\Model\MasterData;

use Illuminate\Database\Eloquent\Model;

class CoaDefault extends Model
{
    protected $table = 'mst_coas';

    protected $fillable = [
        'coa_number',
        'coa_name',
        'coa_abv_name',
        'main_account_id',
    ];

    // belongsToOne
    public function coadefault_mainaccount(){
        return $this->belongsTo('App\Model\Accounting\MainAccount','main_account_id');
    }
}
