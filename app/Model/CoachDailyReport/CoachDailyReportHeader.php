<?php

namespace App\Model\CoachDailyReport;

use Illuminate\Database\Eloquent\Model;

class CoachDailyReportHeader extends Model
{
    protected $table = 'coach_daily_report_headers';

    protected $fillable = [
        'coach_id',
        'date'
    ];

    // belongsTo
    public function cdrheader_user() {
        return $this->belongsTo('App\User', 'coach_id');
    }

    // hasMany
    public function cdrheader_cdrardetail() {
        return $this->hasMany('App\Model\CoachDailyReport\CoachDailyReportArDetail', 'coach_daily_report_header_id');
    }

    public function cdrheader_cdrsalesdetail() {
        return $this->hasMany('App\Model\CoachDailyReport\CoachDailyReportSalesDetail', 'coach_daily_report_header_id');
    }
}
