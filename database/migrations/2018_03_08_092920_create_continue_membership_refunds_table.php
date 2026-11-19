<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContinueMembershipRefundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('continue_membership_refunds', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('continue_id')->unsigned();
            $table->integer('payment_id')->unsigned();
            $table->enum('refund_status', ['Pending', 'Paid', 'Cancelled'])->default('Pending');
            $table->timestamp('refund_process_date')->nullable();
            $table->integer('submit_by')->unsigned();
            $table->integer('cancellation_reason_id')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();

            $table->foreign('continue_id')->references('id')->on('continue_memberships');
            $table->foreign('payment_id')->references('id')->on('member_payments');
            $table->foreign('submit_by')->references('id')->on('users');
            $table->foreign('cancellation_reason_id')->references('id')->on('mst_no_sale_reasons');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('continue_membership_refunds');
    }
}
