<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogContactedLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_contacted_leads', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('guest_appointment_id')->unsigned();
            $table->integer('staff_id')->unsigned();
            $table->integer('status_id')->unsigned();
            $table->timestamps();

            $table->foreign('guest_appointment_id')->references('id')->on('guest_appointments');
            $table->foreign('staff_id')->references('id')->on('users');
            $table->foreign('status_id')->references('id')->on('mst_appointment_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_contacted_leads');
    }
}
