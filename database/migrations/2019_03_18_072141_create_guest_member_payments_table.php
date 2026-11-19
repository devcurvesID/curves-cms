<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGuestMemberPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guest_member_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('guest_membership_member_id')->unsigned();
            $table->integer('payment_type_id')->unsigned();
            $table->integer('bank_id')->unsigned();
            $table->date('payment_date');
            $table->integer('amount_due')->default(0);
            $table->integer('amount_paid')->default(0);
            $table->integer('rest_of_bill')->default(0);
            $table->enum('payment_status', ['Paid', 'Halfpaid', 'Unpaid'])->default('Unpaid');
            $table->timestamps();

            $table->foreign('guest_membership_member_id')->references('id')->on('guest_membership_members')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('payment_type_id')->references('id')->on('mst_payment_methods');
            $table->foreign('bank_id')->references('id')->on('mst_banks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('guest_member_payments');
    }
}
