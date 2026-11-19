<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductShipmentArrivalDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_shipment_arrival_details', function (Blueprint $table) {
            $table->integer('product_request_detail_id')->unsigned()->primary('product_request_detail_id');
            $table->integer('received_amount');
            $table->text('receiver_notes')->nullable();
            $table->integer('deficit')->default('0');
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
        Schema::dropIfExists('product_shipment_arrival_details');
    }
}
