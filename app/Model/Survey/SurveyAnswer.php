<?php

namespace App\Model\Survey;

use Illuminate\Database\Eloquent\Model;

class SurveyAnswer extends Model
{
    protected $table = 'survey_answers';

    protected $fillable = [
        'workout_id',
        'question_id',
        'answer',
    ];

    // belongsTo
    public function surveyanswer_workout() {
        return $this->belongsTo('App\Model\Workout\Workout', 'workout_id');
    }

    public function surveyanswer_question() {
        return $this->belongsTo('App\Model\Survey\Question', 'question_id');
    }
}
