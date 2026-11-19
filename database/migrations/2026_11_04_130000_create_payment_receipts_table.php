<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('payment_receipts')) {
            Schema::create('payment_receipts', function (Blueprint $table) {
                $table->id();
                $table->string('transId')->index();
                $table->text('details');
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down()
    {
        // Preserve receipt history, including a table that existed before this migration.
    }
};
