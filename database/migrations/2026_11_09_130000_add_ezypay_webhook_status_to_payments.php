<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('member_payments', function (Blueprint $table) {
            $table->string('ezypay_invoice_status', 32)->nullable();
            $table->string('ezypay_subscription_status', 32)->nullable();
            $table->timestamp('ezypay_subscription_event_at', 6)->nullable();
        });

        Schema::table('member_debt_payments', function (Blueprint $table) {
            $table->string('ezypay_invoice_id')->nullable()->index();
            $table->string('ezypay_invoice_status', 32)->nullable();
        });
    }

    public function down()
    {
        Schema::table('member_debt_payments', function (Blueprint $table) {
            $table->dropIndex(['ezypay_invoice_id']);
            $table->dropColumn(['ezypay_invoice_id', 'ezypay_invoice_status']);
        });

        Schema::table('member_payments', function (Blueprint $table) {
            $table->dropColumn(['ezypay_invoice_status', 'ezypay_subscription_status', 'ezypay_subscription_event_at']);
        });
    }
};
