<?php

namespace App\Model\MasterData;

use Illuminate\Database\Eloquent\Model;

class CoachDailyReportCategory extends Model
{
    protected $table = 'mst_coach_daily_report_categories';

    protected $fillable = [
        'category',
        'sequence'
    ];

    // hasMany
    public function cdrcategory_cdrsalesdetail() {
        return $this->hasMany('App\Model\MasterData\CoachDailyReportSalesCategory', 'category_id');
    }
}
