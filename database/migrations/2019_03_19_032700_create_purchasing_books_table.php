<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchasingBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchasing_books', function (Blueprint $table) {
            $table->increments('id');
            $table->date('purchasing_date');
            $table->integer('coa_id')->unsigned();
            $table->string('purchasing_info');
            $table->integer('amount_due');
            $table->integer('amount_paid');
            $table->integer('rest_of_bill');
            $table->enum('payment_status', ['Paid', 'Halfpaid', 'Unpaid'])->default('Unpaid');
            $table->integer('club_id')->unsigned();
            $table->timestamps();

            $table->foreign('coa_id')->references('id')->on('chart_of_accounts');
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
        Schema::dropIfExists('purchasing_books');
    }
}
