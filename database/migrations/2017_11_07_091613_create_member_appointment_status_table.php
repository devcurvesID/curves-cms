<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberAppointmentStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_appointment_status', function (Blueprint $table) {
            $table->integer('ma_id')->unsigned()->primary();
            $table->integer('status_id')->unsigned();
            $table->timestamp('c_date')->nullable();
            $table->timestamp('s_date')->nullable();
            //$table->timestamp('sa_date')->nullable();
            $table->timestamps();

            $table->foreign('ma_id')->references('id')->on('member_appointments')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('member_appointment_status');
    }
}
