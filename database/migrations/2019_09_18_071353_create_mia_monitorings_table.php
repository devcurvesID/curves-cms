<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMiaMonitoringsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mia_monitorings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('caller_id')->unsigned();
            $table->integer('member_id')->unsigned();
            $table->dateTime('calling_date');
            $table->string('reason');
            $table->timestamps();

            $table->foreign('caller_id')->references('id')->on('users');
            $table->foreign('member_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mia_monitorings');
    }
}
