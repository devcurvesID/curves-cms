<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLockerPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locker_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('memberlocker_id')->unsigned();
            $table->integer('locker_id')->unsigned();
            $table->string('payment_number');
            $table->integer('staff_id')->unsigned();
            $table->integer('price');
            $table->integer('quantity');
            $table->integer('subtotal');
            $table->integer('pay');
            $table->integer('paymentmethod_id')->unsigned();
            $table->date('payment_date')->date(DB::raw('CURRENT_TIMESTAMP'));
            $table->integer('taxtype_id')->unsigned();
            $table->integer('tax_amount');
            $table->timestamps();

            $table->foreign('memberlocker_id')->references('id')->on('member_lockers')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('staff_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('paymentmethod_id')->references('id')->on('mst_payment_methods')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('taxtype_id')->references('id')->on('tax_types')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('locker_id')->references('id')->on('mst_lockers')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('locker_payments');
    }
}
