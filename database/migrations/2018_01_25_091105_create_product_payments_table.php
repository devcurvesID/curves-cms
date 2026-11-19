<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('club_id')->unsigned();
            $table->integer('cashier_id')->unsigned();
            $table->integer('payment_method_id')->unsigned();
            $table->string('payment_number')/*->unique()*/;
            $table->integer('subtotal');
            $table->integer('tax_type_id')->unsigned();
            $table->integer('tax_value')->default('0');
            $table->string('voucher_code')->nullable();
            $table->integer('voucher_value')->default('0');
            $table->integer('total');
            $table->integer('pay');
            $table->integer('change')->default('0');
            $table->timestamp('payment_date');
            $table->timestamps();

            $table->foreign('club_id')->references('id')->on('club_infos');
            $table->foreign('cashier_id')->references('id')->on('users');
            $table->foreign('payment_method_id')->references('id')->on('mst_payment_methods');
            $table->foreign('tax_type_id')->references('id')->on('tax_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_payments');
    }
}
