<?php

namespace App\Model\Curves\CurvesLearning;

use Illuminate\Database\Eloquent\Model;

class ClCoachAnswer extends Model
{
    protected $table = 'cl_coach_answers';

    protected $fillable = [
        'user_id',
        'material_question_id',
        'answer',
    ];

    public function clcoachanswer_user(){
        return $this->belongsTo('App\User', 'user_id');
    }

    public function clcoachanswer_clmaterialquestion(){
        return $this->belongsTo('App\Model\Curves\CurvesLearning\ClMaterialQuestion', 'material_question_id');
    }
}
