<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHqCreditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hq_credits', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('staff_id')->unsigned();
            $table->integer('jubilant_id')->unsigned();
            $table->date('invoice_date');
            $table->string('invoice_number')->unique();
            $table->integer('coa_id')->unsigned();
            $table->string('information')->nullable();
            $table->float('subtotal');
            $table->float('amount_paid')->default(0);
            $table->float('rest_of_bill');
            $table->enum('credit_status', ['Unpaid', 'Halfpaid', 'Paid'])->default('Unpaid');
            $table->timestamps();

            $table->foreign('staff_id')->references('id')->on('users');
            $table->foreign('jubilant_id')->references('id')->on('mst_jubilants');
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
        Schema::dropIfExists('hq_credits');
    }
}
