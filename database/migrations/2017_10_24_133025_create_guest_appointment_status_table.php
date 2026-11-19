<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGuestAppointmentStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guest_appointment_status', function (Blueprint $table) {
            $table->integer('ga_id')->unsigned()->primary('ga_id');
            $table->integer('status_id')->unsigned();
            $table->timestamp('c_date')->nullable();
            $table->timestamp('s_date')->nullable();
            $table->timestamp('sa_date')->nullable();
            $table->timestamps();

            $table->foreign('ga_id')->references('id')->on('guest_appointments')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('guest_appointment_status');
    }
}
