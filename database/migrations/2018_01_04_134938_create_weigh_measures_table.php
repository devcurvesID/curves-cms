<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWeighMeasuresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('weigh_measures', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->timestamp('wm_date');
            $table->integer('age');
            $table->float('height', 5, 3);
            $table->integer('bp_high');
            $table->integer('bp_low');
            $table->integer('rhr');
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
            $table->enum('three_times_a_week', ['Yes', 'No'])->default('No');
            $table->integer('total_wo_per_month')->default(0);
            $table->float('total_measurement', 5, 3);
            $table->float('total_body_fat', 5, 3);
            $table->float('total_hydration', 5, 3);
            $table->integer('measured_by')->unsigned();
            $table->enum('pro_workout', ['Yes', 'No'])->default('No');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('measured_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('weigh_measures');
    }
}
