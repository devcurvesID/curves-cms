<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('payment_receipts', 'details_ezypay')) {
            Schema::table('payment_receipts', function (Blueprint $table) {
                $table->text('details_ezypay')->nullable();
            });
        }
    }

    public function down()
    {
        // Preserve payment responses, including a column that already existed.
    }
};
