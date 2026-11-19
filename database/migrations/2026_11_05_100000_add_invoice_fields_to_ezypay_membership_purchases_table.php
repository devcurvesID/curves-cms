<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ezypay_membership_purchases', function (Blueprint $table) {
            $table->string('payment_type', 20)->default('subscription');
            $table->string('external_payment_id_ezypay')->nullable();
            $table->string('payment_number')->nullable();
        });
    }

    public function down()
    {
        Schema::table('ezypay_membership_purchases', function (Blueprint $table) {
            $table->dropColumn(['payment_type', 'external_payment_id_ezypay', 'payment_number']);
        });
    }
};
