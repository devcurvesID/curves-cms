<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPaymentNumberColumnToGuestMemberPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('guest_member_payments', function (Blueprint $table) {
            $table->string('payment_number')->unique();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('guest_member_payments', function (Blueprint $table) {
            $table->dropColumn('payment_number');
        });
    }
}
