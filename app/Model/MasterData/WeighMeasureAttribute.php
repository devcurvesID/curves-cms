<?php

namespace App\Model\MasterData;

use Illuminate\Database\Eloquent\Model;

class WeighMeasureAttribute extends Model
{
    protected $table = 'mst_weigh_measure_attributes';

    protected $fillable = [
        'attribute_name',
        'description',
    ];

    public function weighmeasureattribute_weighmeasure() {
        return $this->belongsToMany('App\Model\WM\WeighMeasure', 'weigh_measure_attributes', 'wm_attribute_id', 'wm_id')->withTimestamps();
    }
}
