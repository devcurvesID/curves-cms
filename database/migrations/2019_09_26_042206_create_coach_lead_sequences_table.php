<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoachLeadSequencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coach_lead_sequences', function (Blueprint $table){
            $table->integer('coach_lead_id')->unsigned()->primary('coach_lead_id');
            $table->integer('club_id')->unsigned();
            $table->integer('sequence')->unsigned();
            $table->timestamps();

            $table->foreign('coach_lead_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('club_id')->references('id')->on('club_infos');
        });

        Schema::table('club_infos', function (Blueprint $table) {
            $table->integer('current_lead_sequence')->unsigned()->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coach_lead_sequences');

        Schema::table('club_infos', function (Blueprint $table) {
            $table->dropColumn('current_lead_sequence');
        });
    }
}
