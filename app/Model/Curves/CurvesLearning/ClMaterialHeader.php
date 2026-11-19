<?php

namespace App\Model\Curves\CurvesLearning;

use Illuminate\Database\Eloquent\Model;

class ClMaterialHeader extends Model
{
    protected $table = 'cl_material_headers';

    protected $fillable = [
        'name',
        'category_id',
        'sequence',
    ];

    public function clmaterialheader_clmaterialcontent(){
        return $this->hasMany('App\Model\Curves\CurvesLearning\ClMaterialContent', 'material_id');
    }

    public function clmaterialheader_clcategory(){
        return $this->belongsTo('App\Model\Curves\CurvesLearning\ClCategory', 'category_id');
    }
}
