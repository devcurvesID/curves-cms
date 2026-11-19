<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductShipmentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_shipment_details', function (Blueprint $table) {
            $table->integer('product_request_detail_id')->unsigned()->primary('product_request_detail_id');
            $table->integer('send_amount');
            $table->timestamps();

            $table->foreign('product_request_detail_id')->references('id')->on('product_request_details')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_shipment_details');
    }
}
