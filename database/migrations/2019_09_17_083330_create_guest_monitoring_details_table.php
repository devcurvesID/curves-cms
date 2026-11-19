<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGuestMonitoringDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guest_monitoring_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('guest_monitoring_id')->unsigned();
            $table->integer('staff_id')->unsigned();
            $table->dateTime('start_call');
            $table->dateTime('end_call')->nullable();
            $table->integer('duration')->default(0);
            $table->string('status')->nullable();
            $table->timestamps();

            $table->foreign('guest_monitoring_id')->references('id')->on('buddy_referral_leads');
            $table->foreign('staff_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('guest_monitoring_details');
    }
}
