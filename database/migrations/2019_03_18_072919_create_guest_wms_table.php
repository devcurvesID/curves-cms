<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGuestWmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guest_wms', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('guest_id')->unsigned();
            $table->date('wm_date');
            $table->integer('age')->default(0);
            $table->float('height', 5, 3)->default(0);
            $table->integer('bp_high')->default(0);
            $table->integer('bp_low')->default(0);
            $table->integer('rhr')->default(0);
            $table->float('chest', 5, 3)->default(0);
            $table->float('waist', 5, 3)->default(0);
            $table->float('abdomen', 5, 3)->default(0);
            $table->float('hip', 5, 3)->default(0);
            $table->float('thigh', 5, 3)->default(0);
            $table->float('arm', 5, 3)->default(0);
            $table->float('weight', 5, 3)->default(0);
            $table->float('body_fat', 5, 3)->default(0);
            $table->float('muscle_mass', 5, 3)->default(0);
            $table->float('bone_mass', 5, 3)->default(0);
            $table->float('bmi', 5, 3)->default(0);
            $table->float('dci', 5, 3)->default(0);
            $table->float('metabolic', 5, 3)->default(0);
            $table->float('body_water', 5, 3)->default(0);
            $table->float('visceral', 5, 3)->default(0);
            $table->enum('three_times_a_week', ['Yes', 'No'])->default('No');
            $table->integer('total_wo_per_month')->default(0);
            $table->float('total_measurement', 5, 3);
            $table->float('total_body_fat', 5, 3);
            $table->float('total_hydration', 5, 3);
            $table->integer('measured_by')->unsigned();
            $table->enum('pro_workout', ['Yes', 'No'])->default('No');
            $table->timestamps();

            $table->foreign('guest_id')->references('id')->on('buddy_referral_leads');
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
        Schema::dropIfExists('guest_wms');
    }
}
