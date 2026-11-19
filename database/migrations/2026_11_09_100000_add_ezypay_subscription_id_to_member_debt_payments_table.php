<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('member_debt_payments', 'ezypay_subscription_id')) {
            Schema::table('member_debt_payments', function (Blueprint $table) {
                $table->string('ezypay_subscription_id')->nullable()->index();
            });
        }
    }

    public function down()
    {
        // Preserve subscription references, including a column that already existed.
    }
};
