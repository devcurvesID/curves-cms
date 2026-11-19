<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConsignmentOrderBillDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consignment_order_bill_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('consignment_order_bill_id')->unsigned();
            $table->integer('product_payment_id')->unsigned();
            $table->timestamps();

            $table->foreign('consignment_order_bill_id')->references('id')->on('consignment_order_bills');
            $table->foreign('product_payment_id')->references('id')->on('product_payments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('consignment_order_bill_details');
    }
}
