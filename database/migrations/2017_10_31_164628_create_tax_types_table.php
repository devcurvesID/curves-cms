<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaxTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tax_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tax_type')->unique();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();
        });

        Schema::table('member_payments', function (Blueprint $table) {
            $table->foreign('tax_type_id')->references('id')->on('tax_types');
        });

        /*Schema::table('member_cancellation_fees', function (Blueprint $table) {
            $table->foreign('tax_type_id')->references('id')->on('tax_types');
        });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /*Schema::table('member_cancellation_fees', function (Blueprint $table) {
            $table->foreign('tax_type_id')->references('id')->on('tax_types');
        });*/

        Schema::table('member_payments', function (Blueprint $table) {
            $table->dropForeign('member_payments_tax_type_id_foreign');
        });
        Schema::dropIfExists('tax_types');
    }
}
