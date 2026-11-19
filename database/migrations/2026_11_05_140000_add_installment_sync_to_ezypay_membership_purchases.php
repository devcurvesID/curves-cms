<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up() {
        Schema::table('ezypay_membership_purchases', function (Blueprint $table) {
            $table->string('installment_sync_status', 30)->nullable();
            $table->text('installment_memo')->nullable();
            $table->text('installment_sync_response')->nullable();
        });
    }
    public function down() {
        Schema::table('ezypay_membership_purchases', function (Blueprint $table) {
            $table->dropColumn(['installment_sync_status', 'installment_memo', 'installment_sync_response']);
        });
    }
};
