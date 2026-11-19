<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogProductInventoryCorrectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_product_inventory_corrections', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_inventory_id')->unsigned();
            $table->integer('staff_id')->unsigned();
            $table->integer('qty');
            $table->date('correction_date');
            $table->text('correction_reason')->nullable();
            $table->timestamps();

            $table->foreign('product_inventory_id')->references('id')->on('product_inventories')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('staff_id')->references('id')->on('users')->onUpdate('cascade')->onUpdate('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_product_inventory_corrections');
    }
}
