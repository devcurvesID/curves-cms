<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVoucherTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voucher_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('voucher_type')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::table('vouchers', function (Blueprint $table) {
            $table->foreign('type_id')->references('id')->on('voucher_types');
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
            $table->dropForeign('vouchers_type_id_foreign');
        });

        Schema::dropIfExists('voucher_types');
    }
}
