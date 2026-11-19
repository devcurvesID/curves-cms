<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoachSequencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coach_sequences', function (Blueprint $table) {
            $table->integer('coach_id')->unsigned()->primary('coach_id');
            $table->integer('club_id')->unsigned();
            $table->integer('sequence')->unsigned();
            $table->timestamps();

            $table->foreign('coach_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('club_id')->references('id')->on('club_infos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coach_sequences');
    }
}
