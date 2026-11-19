<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductRefundDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_refund_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_refund_id')->unsigned();
            $table->integer('product_payment_detail_id')->unsigned();
            $table->integer('refund_quantity');
            $table->integer('price');
            $table->timestamps();

            $table->foreign('product_refund_id')->references('id')->on('product_refunds')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('product_payment_detail_id')->references('id')->on('product_payment_details')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_refund_details');
    }
}
