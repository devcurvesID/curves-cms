<?php

namespace App\Model\Staff;

use Illuminate\Database\Eloquent\Model;

class CoachTarget extends Model
{
    protected $table = "coach_targets";

    protected $fillable = [
        'coach_id',
        'target_category_detail_id',
        'target',
        'acquired',
        'status',
    ];

    public function coachtarget_user(){
        return $this->belongsTo('App\User', 'coach_id');
    }

    public function coachtarget_targetcategorydetail(){
        return $this->belongsTo('App\Model\MasterData\TargetCategoryDetail', 'target_category_detail_id');
    }
}
