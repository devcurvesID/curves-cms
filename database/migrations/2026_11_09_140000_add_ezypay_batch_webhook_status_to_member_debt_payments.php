<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('member_debt_payments', function (Blueprint $table) {
            $table->string('ezypay_batch_status', 32)->nullable();
            $table->string('ezypay_batch_item_status', 32)->nullable();
            $table->text('ezypay_batch_failure_reason')->nullable();
        });
    }

    public function down()
    {
        Schema::table('member_debt_payments', function (Blueprint $table) {
            $table->dropColumn(['ezypay_batch_status', 'ezypay_batch_item_status', 'ezypay_batch_failure_reason']);
        });
    }
};
