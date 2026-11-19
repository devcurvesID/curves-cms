<?php

namespace App\Model\Curves\CurvesLearning;

use Illuminate\Database\Eloquent\Model;

class ClMaterialQuestion extends Model
{
    protected $table = 'cl_material_questions';

    protected $fillable = [
        'question',
        'material_content_id',
    ];

    public function clmaterialquestion_clcoachanswer(){
        return $this->hasMany('App\Model\Curves\CurvesLearning\ClCoachAnswer');
    }

    public function clmaterialquestion_clmaterialcontent(){
        return $this->belongsTo('App\Model\Curves\CurvesLearning\ClMaterialContent', 'material_content_id');
    }
}
