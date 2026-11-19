<?php

namespace App\Model\MasterData;

use Illuminate\Database\Eloquent\Model;

class TargetCategory extends Model
{
    protected $table = "target_categories";

    protected $fillable = [
        'category_name',
        'status',
    ];

    public function targetcategory_targecategorydetail(){
        return $this->hasMany('App\Model\MasterData\TargetCategoryDetail', 'target_category_id');
    }
}
