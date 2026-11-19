<?php

namespace App\Model\Survey;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $table = 'questions';

    protected $fillable = [
        'question',
        'type',
        'rate_level',
        'status',
    ];

    // hasMany
    public function question_qchoice() {
        return $this->hasMany('App\Model\Survey\QuestionChoice', 'question_id');
    }

    public function question_surveyanswer() {
        return $this->hasMany('App\Model\Survey\SurveyAnswer', 'question_id');
    }
}
