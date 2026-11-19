<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductInventoryLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_inventory_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('club_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->integer('prev_qty');
            $table->integer('next_qty');
            $table->date('transaction_date');
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
        Schema::dropIfExists('product_inventory_logs');
    }
}
