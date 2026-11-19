<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGuestWosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guest_wos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('guest_id')->unsigned();
            $table->date('wo_date');
            $table->integer('club_id')->unsigned();
            $table->timestamps();

            $table->foreign('guest_id')->references('id')->on('buddy_referral_leads');
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
        Schema::dropIfExists('guest_wos');
    }
}
