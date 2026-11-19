<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReferresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referrers', function (Blueprint $table) {
            $table->integer('referral_id')->unsigned()->primary('referral_id');
            $table->integer('user_id')->unsigned();
            $table->enum('status', ['Pending', 'Joined', 'Failed'])->default('Pending');
            $table->timestamps();

            $table->foreign('referral_id')->references('id')->on('buddy_referral_leads')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('referrers');
    }
}
