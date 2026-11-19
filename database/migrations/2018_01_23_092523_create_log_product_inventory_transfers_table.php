<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogProductInventoryTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_product_inventory_transfers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_inventory_id')->unsigned();
            $table->integer('qty');
            $table->integer('from_club')->unsigned()->nullable();
            $table->integer('to_club')->unsigned();
            $table->timestamps();

            $table->foreign('product_inventory_id')->references('id')->on('product_inventories');
            $table->foreign('from_club')->references('id')->on('club_infos');
            $table->foreign('to_club')->references('id')->on('club_infos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_product_inventory_transfers');
    }
}
