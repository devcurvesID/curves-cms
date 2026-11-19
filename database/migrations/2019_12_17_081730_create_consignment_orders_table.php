<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConsignmentOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consignment_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('club_dest_id')->unsigned();
            $table->integer('hq_from_id')->unsigned();
            $table->integer('staff_id')->unsigned();
            $table->string('order_number')->unique();
            $table->date('order_date');
            $table->float('subtotal');
            $table->float('discount')->default(0);
            $table->float('total');
            $table->boolean('delivery_order')->default(false);
            $table->timestamps();

            $table->foreign('club_dest_id')->references('id')->on('club_infos');
            $table->foreign('hq_from_id')->references('id')->on('club_infos');
            $table->foreign('staff_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('consignment_orders');
    }
}
