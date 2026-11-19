<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ezypay_membership_purchases', function (Blueprint $table) {
            $table->unsignedBigInteger('cashier_id')->nullable()->index();
            $table->unsignedBigInteger('member_payment_id')->nullable();
            $table->decimal('installment_remaining', 18, 2)->default(0);
            $table->boolean('installments_completed')->default(true);
        });
    }

    public function down()
    {
        Schema::table('ezypay_membership_purchases', function (Blueprint $table) {
            $table->dropIndex(['cashier_id']);
            $table->dropColumn(['cashier_id', 'member_payment_id', 'installment_remaining', 'installments_completed']);
        });
    }
};
