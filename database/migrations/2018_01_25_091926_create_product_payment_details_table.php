<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductPaymentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_payment_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_payment_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->integer('price')->default('0');
            $table->integer('qty');
            $table->float('discount_percent', 6, 3)->default('0');
            $table->integer('discount_value')->default('0');
            $table->timestamps();

            $table->foreign('product_payment_id')->references('id')->on('product_payments');
            $table->foreign('product_id')->references('id')->on('mst_products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_payment_details');
    }
}
