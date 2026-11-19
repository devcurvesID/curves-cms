<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_points', function (Blueprint $table) {
            $table->integer('user_id')->unsigned()->primary('user_id');
            $table->integer('current_total_point');
            $table->integer('last_months_point');
            $table->integer('last_two_months_point');
            $table->date('last_months_date');
            $table->date('last_two_months_date');
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
        Schema::dropIfExists('staff_points');
    }
}
