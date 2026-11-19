<?php

namespace App\Model\Survey;

use Illuminate\Database\Eloquent\Model;

class QuestionChoice extends Model
{
    protected $table = 'questions_choices';

    protected $fillable = [
        'question_id',
        'choice',
    ];

    // belongsTo
    public function qchoice_question() {
        return $this->belongsTo('App\Model\Survey\Question', 'question_id');
    }
}
