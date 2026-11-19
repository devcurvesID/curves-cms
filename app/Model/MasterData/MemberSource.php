<?php

namespace App\Model\MasterData;

use Illuminate\Database\Eloquent\Model;

class MemberSource extends Model
{
    protected $table = 'member_sources';

    protected $fillable = [
        'source',
    ];

    public function membersource_buddyreferrallead() {
        return $this->hasMany('App\Model\Guest\BuddyReferralLead', 'source_id');
    }

    public function membersource_cdrardetail() {
        return $this->hasMany('App\Model\CoachDailyReport\CoachDailyReportArDetail', 'source_id');
    }

    public function SetSourceAttribute($value) {
        $this->attributes['source'] = strtolower($value);
    }
}
