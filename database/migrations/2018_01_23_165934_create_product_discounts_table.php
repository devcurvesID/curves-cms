<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_discounts', function (Blueprint $table) {
            $table->integer('product_inventory_id')->unsigned()->primary('product_inventory_id');
            $table->float('discount_percent', 6, 3);
            $table->integer('discount_value');
            $table->enum('discount_category', ['Retail', 'Member']);
            $table->enum('discount_type', ['Club', 'Global']);
            $table->date('from_date');
            $table->date('thru_date');
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
        Schema::dropIfExists('product_discounts');
    }
}
