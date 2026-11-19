<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGuestFatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guest_fats', function (Blueprint $table) {
            $table->integer('guest_id')->primary('guest_id');
            $table->float('weight', 5, 3)->nullable();
            $table->float('body_fat', 5, 3)->nullable();
            $table->float('visceral', 5, 3)->nullable();
            $table->timestamps();

            $table->foreign('guest_id')->references('id')->on('buddy_referral_leads')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('guest_fats');
    }
}
