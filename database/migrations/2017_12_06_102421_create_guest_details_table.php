<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGuestDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guest_details', function (Blueprint $table) {
            $table->integer('guest_id')->unsigned()->primary('guest_id');
            $table->date('birth');
            $table->string('email', 50)->nullable();
            $table->string('occupation', 30);
            $table->enum('status', ['Single', 'Married'])->default('Single');
            $table->date('fa_date');
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
        Schema::dropIfExists('guest_details');
    }
}
