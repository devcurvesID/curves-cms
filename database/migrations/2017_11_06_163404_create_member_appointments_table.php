<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_appointments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('staff_id')->unsigned();
            $table->integer('ap_type_id')->unsigned();
            $table->date('app_date');
            $table->time('app_hour');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('staff_id')->references('id')->on('users');
            $table->foreign('ap_type_id')->references('id')->on('mst_appointment_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('member_appointments');
    }
}
