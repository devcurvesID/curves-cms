<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMainAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('main_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('account_number')->unique();
            $table->string('account_name')->unique();
            $table->string('account_abv_name')->unique();
            $table->enum('account_category',['Header','Main','Total'])->default('Header');
            $table->enum('account_type',['Aktiva','Passiva','Pendapatan','Biaya']);
            $table->integer('user_id')->unsigned();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('main_accounts');
    }
}
