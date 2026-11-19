<?php

namespace App\Model\Curves\CurvesLearning;

use Illuminate\Database\Eloquent\Model;

class ClMaterialContent extends Model
{
    protected $table = 'cl_material_contents';

    protected $fillable = [
        'title',
        'material_id',
        'sequence',
        'content',
        'status',
        'total_question',
    ];

    public function clmaterialcontent_clmaterialsuppot(){
        return $this->hasMany('App\Model\Curves\CurvesLearning\ClMaterialSupport', 'material_content_id');
    }

    public function clmaterialcontent_clmaterialquestion(){
        return $this->hasMany('App\Model\Curves\CurvesLearning\ClMaterialQuestion', 'material_content_id');
    }

    public function clmaterialcontent_clcoachpass(){
        return $this->hasMany('App\Model\Curves\CurvesLearning\ClCoachPas', 'material_content_id');
    }

    public function clmaterialcontent_clmaterialheader(){
        return $this->belongsTo('App\Model\Curves\CurvesLearning\ClMaterialHeader', 'material_id');
    }
}
