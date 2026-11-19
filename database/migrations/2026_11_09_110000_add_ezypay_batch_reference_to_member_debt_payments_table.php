<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('member_debt_payments', 'ezypay_batch_reference')) {
            Schema::table('member_debt_payments', function (Blueprint $table) {
                $table->string('ezypay_batch_reference')->nullable()->index();
            });
        }
    }

    public function down()
    {
        // Preserve batch references, including a column that already existed.
    }
};
