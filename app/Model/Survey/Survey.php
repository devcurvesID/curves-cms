<?php

namespace App\Model\Survey;

use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    protected $table = 'surveys';

    protected $primaryKey = 'workout_id';

    protected $fillable = [
        'workout_id',
        'answered',
    ];

    // belongsTo
    public function survey_workout() {
        return $this->belongsTo('App\Model\Workout\Workout', 'workout_id');
    }
}
