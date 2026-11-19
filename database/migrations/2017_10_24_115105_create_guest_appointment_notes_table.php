<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGuestAppointmentNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guest_appointment_notes', function (Blueprint $table) {
            $table->integer('ga_id')->unsigned()->primary('ga_id');
            $table->string('note');
            $table->timestamps();

            $table->foreign('ga_id')->references('id')->on('guest_appointments')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('guest_appointment_notes');
    }
}
