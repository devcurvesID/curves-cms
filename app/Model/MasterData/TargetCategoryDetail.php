<?php

namespace App\Model\MasterData;

use Illuminate\Database\Eloquent\Model;

class TargetCategoryDetail extends Model
{
    protected $table = "target_category_details";

    protected $fillable = [
        'target_category_id',
        'target_interval',
        'target',
        'type',
        'status',
    ];

    public function targetcategorydetail_targetcategory(){
        return $this->belongsTo('App\Model\MasterData\TargetCategory', 'target_category_id');
    }

    public function targetcategorydetail_coachtarget(){
        return $this->hasMany('App\Model\Staff\CoachTarget', 'target_category_detail_id');
    }

    public function targetcategorydetail_logcoachtarget() {
        return $this->hasMany('App\Model\Log\LogCoachTarget', 'target_category_detail_id');
    }
}
