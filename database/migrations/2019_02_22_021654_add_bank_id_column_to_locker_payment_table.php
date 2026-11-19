<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBankIdColumnToLockerPaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('locker_payments', function (Blueprint $table) {
            $table->integer('bank_id')->unsigned()->default(1);

            $table->foreign('bank_id')->references('id')->on('mst_banks')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('locker_payments', function (Blueprint $table) {
            $table->dropColumn('bank_id');
        });
    }
}
