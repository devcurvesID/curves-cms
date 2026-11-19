<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDigitalProductPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('digital_product_payments', function (Blueprint $table) {
            $table->integer('member_payment_id')->unsigned()->primary('member_payment_id');
            $table->integer('digital_product_inventory_id')->unsigned();
            $table->float('price');
            $table->integer('qty');
            $table->timestamps();

            $table->foreign('member_payment_id')->references('id')->on('member_payments')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('digital_product_payments');
    }
}
