<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberAppointmentNotes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_appointment_notes', function (Blueprint $table) {
            $table->integer('ma_id')->unsigned()->primary();
            $table->text('notes');
            $table->timestamps();

            $table->foreign('ma_id')->references('id')->on('member_appointments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('member_appointment_notes');
    }
}
