<?php

namespace App\Model\Curves\CurvesLearning;

use Illuminate\Database\Eloquent\Model;

class ClCategory extends Model
{
    protected $table = 'cl_categories';

    protected $fillable = [
      'name',
    ];

    public function clcategory_clmaterialheader(){
        return $this->hasMany('App\Model\Curves\CurvesLearning\ClMaterialHeader', 'category_id');
    }
}
