<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWeighMeasureGoalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('weigh_measure_goals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->timestamp('set_goal_date');
            $table->float('chest', 5, 3);
            $table->float('waist', 5, 3);
            $table->float('abdomen', 5, 3);
            $table->float('hip', 5, 3);
            $table->float('thigh', 5, 3);
            $table->float('arm', 5, 3);
            /*$table->float('left_thigh', 5, 3);
            $table->float('right_thigh', 5, 3);
            $table->float('left_arm', 5, 3);
            $table->float('right_arm', 5, 3);*/
            $table->float('weight', 5, 3);
            $table->float('body_fat', 5, 3);
            /*$table->float('body_fat_left_arm', 5, 3);
            $table->float('body_fat_right_arm', 5, 3);
            $table->float('body_fat_left_leg', 5, 3);
            $table->float('body_fat_right_leg', 5, 3);*/
            $table->float('muscle_mass', 5, 3);
            $table->float('bone_mass', 5, 3);
            $table->float('bmi', 5, 3);
            $table->float('dci', 5, 3);
            $table->float('metabolic', 5, 3);
            $table->float('body_water', 5, 3);
            $table->float('visceral', 5, 3);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('weigh_measure_goals');
    }
}
