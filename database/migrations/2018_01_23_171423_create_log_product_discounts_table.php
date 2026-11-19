<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogProductDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_product_discounts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_inventory_id')->unsigned();
            $table->float('discount_percent', 6, 3);
            $table->integer('discount_value');
            $table->enum('discount_category', ['Retail', 'Member']);
            $table->enum('discount_type', ['Club', 'Global']);
            $table->date('from_date');
            $table->date('thru_date');
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
        Schema::dropIfExists('log_product_discounts');
    }
}
