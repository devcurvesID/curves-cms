<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDetailToVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->enum('voucher_base', ['value', 'percent'])->default('value');
            $table->integer('value')->default('0');
            $table->float('percent', 6, 3)->default('0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn('voucher_base');
            $table->dropColumn('value');
            $table->dropColumn('percent');
        });
    }
}
