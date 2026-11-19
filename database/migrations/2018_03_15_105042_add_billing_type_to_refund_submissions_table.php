<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBillingTypeToRefundSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('refund_submissions', function (Blueprint $table) {
            $table->enum('billing_type', ['Refund', 'Bill', 'Voucher'])->default('Refund');
            $table->integer('membership_type_id')->unsigned();
            $table->date('start_membership_date');
            $table->date('end_membership_date');
            $table->string('refund_number')->nullable();

            $table->foreign('membership_type_id')->references('id')->on('membership_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('refund_submissions', function (Blueprint $table) {
            $table->dropColumn('billing_type');
            $table->dropColumn('membership_type_id');
            $table->dropColumn('start_membership_date');
            $table->dropColumn('end_membership_date');
            $table->dropColumn('refund_number');
        });
    }
}
