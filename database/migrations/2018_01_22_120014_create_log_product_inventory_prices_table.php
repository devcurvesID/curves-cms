<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogProductInventoryPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_product_inventory_prices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_inventory_id')->unsigned();
            $table->integer('price');
            $table->timestamp('from_date');
            $table->timestamp('thru_date')->nullable();
            $table->timestamps();

            $table->foreign('product_inventory_id')->references('id')->on('product_inventories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_product_inventory_prices');
    }
}
