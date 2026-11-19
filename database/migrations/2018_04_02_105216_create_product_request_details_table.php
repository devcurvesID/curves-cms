<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductRequestDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_request_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id')->unsigned();
            $table->integer('product_request_id')->unsigned();
            $table->integer('amount');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('mst_products')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('product_request_id')->references('id')->on('product_requests')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_request_details');
    }
}
