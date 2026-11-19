<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCancellationReasonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cancellation_reasons', function (Blueprint $table) {
            $table->integer('refund_submission_id')->unsigned()->primary('refund_submission_id');
            $table->integer('membership_type_id')->unsigned();
            $table->integer('cancellation_reason_id')->unsigned()->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->timestamps();

            $table->foreign('refund_submission_id')->references('id')->on('refund_submissions')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('membership_type_id')->references('id')->on('membership_types');
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
        Schema::dropIfExists('cancellation_reasons');
    }
}
