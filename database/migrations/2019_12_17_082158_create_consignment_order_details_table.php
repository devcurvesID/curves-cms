<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConsignmentOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consignment_order_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('consignment_order_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->integer('qty');
            $table->float('price_per_pcs');
            $table->timestamps();

            $table->foreign('consignment_order_id')->references('id')->on('consignment_orders');
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
        Schema::dropIfExists('consignment_order_details');
    }
}
