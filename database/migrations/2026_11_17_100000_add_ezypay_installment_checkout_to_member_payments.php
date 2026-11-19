<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('member_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('ezypay_checkout_amount')->nullable();
            $table->text('ezypay_checkout_url')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('member_payments', function (Blueprint $table) {
            $table->dropColumn(['ezypay_checkout_amount', 'ezypay_checkout_url']);
        });
    }
};
