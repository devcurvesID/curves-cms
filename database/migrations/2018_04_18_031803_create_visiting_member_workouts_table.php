<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVisitingMemberWorkoutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visiting_member_workouts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('visiting_member_id')->unsigned();
            $table->timestamp('workout_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->integer('club_id')->unsigned();
            $table->timestamps();

            $table->foreign('visiting_member_id')->references('id')->on('visiting_members')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('club_id')->references('id')->on('club_infos')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('visiting_member_workouts');
    }
}
