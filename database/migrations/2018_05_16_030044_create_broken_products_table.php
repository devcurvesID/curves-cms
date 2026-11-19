<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBrokenProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('broken_products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_inventory_id')->unsigned();
            $table->integer('quantity');
            $table->date('input_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->integer('staff_id')->unsigned();
            $table->timestamps();

            $table->foreign('product_inventory_id')->references('id')->on('product_inventories')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('staff_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('broken_products');
    }
}
