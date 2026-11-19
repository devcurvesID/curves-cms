<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogStaffPointAcquisitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_staff_point_acquisitions', function (Blueprint $table) {
            $table->increments('id');
            $table->date('acquire_date');
            $table->integer('amount');
            $table->integer('user_id')->unsigned();
            $table->integer('point_id')->unsigned();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('point_id')->references('id')->on('mst_points');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_staff_point_acquisitions');
    }
}
