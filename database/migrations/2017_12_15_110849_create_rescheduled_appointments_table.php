<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRescheduledAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rescheduled_appointments', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('appointment_type', ['Guest', 'Member']);
            $table->integer('appointment_id')->unsigned();
            $table->date('original_schedule_date');
            $table->timeTz('original_schedule_time');
            $table->date('new_schedule_date');
            $table->timeTz('new_schedule_time');
            $table->integer('last_status')->unsigned()->nullable();
            $table->integer('rescheduled_by')->unsigned();
            $table->timestamps();

            $table->foreign('last_status')->references('id')->on('mst_appointment_status');
            $table->foreign('rescheduled_by')->references('id')->on('users');
            /*$table->foreign('appointment_id')->references('id')->on('guest_appointments');*/
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rescheduled_appointments');
    }
}
