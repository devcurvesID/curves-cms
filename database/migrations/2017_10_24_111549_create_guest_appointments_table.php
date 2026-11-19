<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGuestAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guest_appointments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('guest_id')->unsigned();
            $table->integer('staff_id')->unsigned();
            $table->enum('fa_status', ['FA', 'NonFA'])->default('FA');
            $table->date('fa_date');
            $table->timeTz('fa_hour');
            $table->enum('guest_status_after', ['Lead', 'Member'])->default('Lead');
            $table->integer('club_id')->unsigned();
            $table->timestamps();

            $table->foreign('staff_id')->references('id')->on('users');
            $table->foreign('club_id')->references('id')->on('club_infos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('guest_appointments');
    }
}
