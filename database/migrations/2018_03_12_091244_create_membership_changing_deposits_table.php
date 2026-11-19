<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMembershipChangingDepositsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('membership_changing_deposits', function (Blueprint $table) {
            $table->integer('payment_id')->unsigned()->primary('payment_id');
            $table->integer('deposit_amount');
            $table->timestamps();

            $table->foreign('payment_id')->references('id')->on('member_payments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('membership_changing_deposits');
    }
}
