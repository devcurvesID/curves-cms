<?php

namespace App\Model\Log;

use Illuminate\Database\Eloquent\Model;

class LogCoachTarget extends Model
{
    protected $table = 'log_coach_targets';

    protected $fillable = [
        'coach_id',
        'target_category_detail_id',
        'acquired_date',
        'target',
        'acquired',
    ];

    // belongsTo
    public function logcoachtarget_user() {
        return $this->belongsTo('App\User', 'coach_id');
    }

    public function logcoachtarget_targetcategorydetail() {
        return $this->belongsTo('App\Model\MasterData\TargetCategoryDetail', 'target_category_detail_id');
    }
}
