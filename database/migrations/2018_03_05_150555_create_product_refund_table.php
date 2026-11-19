<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductRefundTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_refunds', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_payment_id')->unsigned();
            $table->integer('staff_id')->unsigned();
            $table->string('refund_number');
            $table->date('refund_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->text('refund_reason')->nullable();
            $table->integer('refund_amount');
            $table->enum('refund_type', ['cash', 'voucher', 'trade'])->default('trade');
            $table->enum('refund_status', ['pending', 'success', 'fail'])->default('pending');
            $table->timestamps();

            $table->foreign('product_payment_id')->references('id')->on('product_payments')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('staff_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_refunds');
    }
}
