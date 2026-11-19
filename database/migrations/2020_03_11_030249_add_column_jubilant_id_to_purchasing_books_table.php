<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnJubilantIdToPurchasingBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchasing_books', function (Blueprint $table) {
            $table->string('invoice_number')->nullable();
            $table->integer('jubilant_id')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchasing_books', function (Blueprint $table) {
            $table->dropColumn('invoice_number');
            $table->dropColumn('jubilant_id');
        });
    }
}
