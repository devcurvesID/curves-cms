<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE ezypay_membership_purchases ALTER COLUMN lead_id DROP NOT NULL');
        Schema::table('ezypay_membership_purchases', function (Blueprint $table) {
            $table->string('renewal_key')->nullable()->unique();
            $table->unsignedBigInteger('renewal_user_id')->nullable()->index();
            $table->unsignedBigInteger('continue_membership_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('ezypay_membership_purchases', function (Blueprint $table) {
            $table->dropUnique(['renewal_key']);
            $table->dropIndex(['renewal_user_id']);
            $table->dropColumn(['renewal_key', 'renewal_user_id', 'continue_membership_id']);
        });
    }
};
