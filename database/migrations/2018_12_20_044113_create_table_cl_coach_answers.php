<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableClCoachAnswers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cl_coach_answers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();
            $table->integer('material_question_id')->unsigned()->index();
            $table->text('answer');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('material_question_id')->references('id')->on('cl_material_questions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cl_coach_answers');
    }
}
