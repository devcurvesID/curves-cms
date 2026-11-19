<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('member_payments', 'ezypay_checkout_id')) {
            Schema::table('member_payments', function (Blueprint $table) {
                $table->string('ezypay_checkout_id')->nullable()->index();
            });
        }
    }

    public function down()
    {
        // Preserve checkout references, including a column that already existed.
    }
};
