<?php

namespace App\Model\Curves\CurvesLearning;

use Illuminate\Database\Eloquent\Model;

class ClMaterialSupport extends Model
{
    protected $table = 'cl_material_supports';

    protected $fillable = [
        'file_name',
        'material_id',
    ];

    public function clmaterialsupport_clmaterialcontent(){
        return $this->belongsTo('App\Model\Curves\CurvesLearning\ClMaterialContent', 'material_content_id');
    }
}
