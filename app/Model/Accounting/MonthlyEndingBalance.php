<?php

namespace App\Model\Accounting;

use Illuminate\Database\Eloquent\Model;

class MonthlyEndingBalance extends Model
{
    protected $table = 'monthly_ending_balances';

    protected $fillable = [
        'jubilant_id',
        'club_id',
        'coa_id',
        'recap_date',
        'debit',
        'credit',
        'd_or_c',
    ];

    // belongsTo
    public function meb_jubilant() {
        return $this->belongsTo('App\Model\Jubilant\Jubilant', 'jubilant_id');
    }

    public function meb_club() {
        return $this->belongsTo('App\Model\Club\ClubInfo', 'club_id');
    }

    public function meb_coa() {
        return $this->belongsTo('App\Model\Accounting\ChartOfAccount', 'coa_id');
    }
}
