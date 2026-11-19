<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogDigitalProductInventories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_digital_product_inventories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('digital_product_inventory_id');
            $table->integer('user_id');
            $table->integer('qty_before')->default(0);
            $table->integer('qty_after')->default(0);
            $table->timestamp('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_digital_product_inventories');
    }
}
