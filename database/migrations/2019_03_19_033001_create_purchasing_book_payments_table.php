<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchasingBookPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchasing_book_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('purchasing_book_id')->unsigned();
            $table->integer('coa_id')->unsigned();
            $table->date('payment_date');
            $table->integer('amount_paid');
            $table->timestamps();

            $table->foreign('purchasing_book_id')->references('id')->on('purchasing_books');
            $table->foreign('coa_id')->references('id')->on('chart_of_accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchasing_book_payments');
    }
}
