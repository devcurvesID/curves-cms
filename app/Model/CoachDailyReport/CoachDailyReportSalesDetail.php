<?php

namespace App\Model\CoachDailyReport;

use Illuminate\Database\Eloquent\Model;

class CoachDailyReportSalesDetail extends Model
{
    protected $table = 'coach_daily_report_sales_details';

    protected $fillable = [
        'coach_daily_report_header_id',
        'category_id',
        'type',
        'total'
    ];

    // belongsTo
    public function cdrsalesdetail_cdrheader() {
        return $this->belongsTo('App\Model\CoachDailyReport\CoachDailyReportHeader', 'coach_daily_report_header_id');
    }

    public function cdrsalesdetail_cdrcategory() {
        return $this->belongsTo('App\Model\MasterData\CoachDailyReportCategory', 'category_id');
    }
}
