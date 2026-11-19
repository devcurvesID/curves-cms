<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NoSaleReasonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('no_sale_reasons', function (Blueprint $table) {
            $table->integer('guest_appointment_id')->unsigned()->primary('guest_appointment_id');
            $table->integer('no_sale_reason_id')->unsigned();
            $table->timestamps();

            $table->foreign('guest_appointment_id')->references('id')->on('guest_appointments')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('no_sale_reasons');
    }
}
