<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberDepositsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_deposits', function (Blueprint $table) {
            $table->increments('id');
            $table->string('payment_number');
            $table->integer('user_id')->unsigned();
            $table->integer('debet')->default('0');
            $table->integer('kredit')->default('0');
            $table->integer('saldo');
            $table->enum('status', ['Pending', 'Done']);
            $table->enum('payment_type', ['Payment', 'Refund']);
            $table->timestamps();

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
        Schema::dropIfExists('member_deposits');
    }
}
