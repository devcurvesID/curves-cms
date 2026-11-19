<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductShipmentArrivalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_shipment_arrivals', function (Blueprint $table) {
            $table->integer('product_request_id')->unsigned()->primary('product_request_id');
            $table->date('arrival_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamps();

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
        Schema::dropIfExists('product_shipment_arrivals');
    }
}
