<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductRefundInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_refund_inventories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id')->unsigned();
            $table->integer('quantity');
            $table->integer('product_refund_id')->unsigned();
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('mst_products')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('product_refund_id')->references('id')->on('product_refunds')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_refund_inventories');
    }
}
