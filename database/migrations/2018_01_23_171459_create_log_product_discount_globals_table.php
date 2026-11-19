<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogProductDiscountGlobalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_product_discount_globals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id')->unsigned();
            $table->enum('discount_base', ['percent', 'value']);
            $table->float('discount_percent', 6, 3)->nullable();
            $table->integer('discount_value')->nullable();
            $table->enum('discount_category', ['Retail', 'Member']);
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
        Schema::dropIfExists('log_product_discount_globals');
    }
}
