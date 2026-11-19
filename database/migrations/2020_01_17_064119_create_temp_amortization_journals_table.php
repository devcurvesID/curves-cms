<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTempAmortizationJournalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_amortization_journals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('jubilant_id')->unsigned();
            $table->integer('club_id')->unsigned();
            $table->integer('coa_id')->unsigned();
            $table->date('trans_date');
            $table->date('bank_date')->nullable();
            $table->float('debit')->default(0);
            $table->float('credit')->default(0);
            $table->string('information');
            $table->boolean('approved')->default(false);
            $table->enum('d_or_c', ['Debit', 'Credit']);
            $table->integer('pair');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('temp_amortization_journals');
    }
}
