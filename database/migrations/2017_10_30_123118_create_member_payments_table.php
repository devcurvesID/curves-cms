<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('payment_number')->nullable();
            $table->integer('user_id')->unsigned();
            $table->integer('sf_amount')->default(0);
            $table->integer('mf_amount')->default(0);
            $table->integer('dc_amount')->default(0);
            $table->integer('tax_type_id')->unsigned()->nullable();
            $table->integer('tax_amount')->default(0);
            $table->float('tax_percentage', 6, 3)->default(0);
            $table->integer('amount_due');
            $table->enum('payment_category', ['First', 'Monthly'])->default('Monthly');
            $table->timestamp('payment_date')->nullable();
            $table->integer('payment_method_id')->unsigned()->nullable();
            $table->integer('amount_paid')->default(0);
            $table->integer('rest_of_bill')->default(0);
            $table->enum('payment_status', ['Unpaid', 'Halfpaid', 'Paid'])->default('Unpaid');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('payment_method_id')->references('id')->on('mst_payment_methods');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('member_payments');
    }
}
