<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_inventories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('club_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->integer('selling_price')->default('0');
            $table->integer('qty')->default('0');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('club_id')->references('id')->on('club_infos');
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
        Schema::dropIfExists('product_inventories');
    }
}
