<?php

namespace App\Model\Curves\CurvesLearning;

use Illuminate\Database\Eloquent\Model;

class ClCoachPas extends Model
{
    protected $table = 'cl_coach_pass';

    protected $fillable = [
        'material_content_id',
        'user_id',
    ];

    public function clcoachpas_clmaterialcontent(){
        return $this->belongsTo('App\Model\Curves\CurvesLearning\ClMaterialContent', 'material_content_id');
    }

    public function clcoachpas_user(){
        return $this->belongsTo('App\User', 'user_id');
    }
}
