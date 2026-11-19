<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDigitalProductInventories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('digital_product_inventories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('club_id');
            $table->integer('digital_product_id');
            $table->float('selling_price')->default(0);
            $table->integer('qty')->default(0);
            $table->boolean('status')->default(false);
            $table->timestamps();

            $table->foreign('digital_product_id')->references('id')->on('digital_products')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('digital_product_inventories');
    }
}
