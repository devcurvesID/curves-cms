<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogContactedNewLeadsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_contacted_new_leads', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('guest_id')->unsigned();
            $table->integer('staff_id')->unsigned();
            $table->enum('status', ['Answered', 'NotAnswered'])->default('NotAnswered');
            $table->timestamps();

            $table->foreign('guest_id')->references('id')->on('buddy_referral_leads');
            $table->foreign('staff_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_contacted_new_leads');
    }
}
