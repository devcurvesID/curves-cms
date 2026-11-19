<?php

namespace App\Model\CoachDailyReport;

use Illuminate\Database\Eloquent\Model;

class CoachDailyReportArDetail extends Model
{
    protected $table = 'coach_daily_report_ar_details';

    protected $fillable = [
        'coach_daily_report_header_id',
        'source_id',
        'total_calling',
        'answer',
        'apo',
        'reschedule',
        'reject',
        'wrong_number',
        'invalid',
        'na',
        'leads'
    ];

    // belongsTo
    public function cdrardetail_cdrheader() {
        return $this->belongsTo('App\Model\CoachDailyReport\CoachDailyReportHeader', 'coach_daily_report_header_id');
    }

    public function cdrardetail_membersource() {
        return $this->belongsTo('App\Model\MasterData\MemberSource', 'source_id');
    }
}
