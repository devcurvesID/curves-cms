<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_shipments', function (Blueprint $table) {
            $table->integer('product_request_id')->unsigned()->primary('product_request_id');
            $table->integer('staff_id')->unsigned();
            $table->integer('club_id')->unsigned();
            $table->date('accept_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->date('send_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->text('notes')->nullable();
            $table->enum('shipment_status',['Accepted','Canceled', 'Sending', 'Finished','Failed']);
            $table->timestamps();

            $table->foreign('product_request_id')->references('id')->on('product_requests')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('staff_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('club_id')->references('id')->on('club_infos')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_shipments');
    }
}
