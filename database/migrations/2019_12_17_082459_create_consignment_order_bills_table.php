<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConsignmentOrderBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consignment_order_bills', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('club_dest_id')->unsigned();
            $table->integer('hq_from_id')->unsigned();
            $table->integer('staff_id')->unsigned();
            $table->string('co_bill_number')->unique();
            $table->date('co_bill_date');
            $table->date('co_bill_due_date');
            $table->float('total');
            $table->float('amount_paid')->default(0);
            $table->float('rest_of_bill');
            $table->enum('status', ['Unpaid', 'Halfpaid', 'Paid'])->default('Unpaid');
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
        Schema::dropIfExists('consignment_order_bills');
    }
}
