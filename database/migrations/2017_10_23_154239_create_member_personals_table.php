<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberPersonalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_personals', function (Blueprint $table) {
            $table->integer('user_id')->unsigned()->primary('user_id');
            $table->date('birth')->nullable();
            $table->enum('sex', ['M', 'F'])->default('F');
            $table->string('address', 255)->nullable();
            $table->string('postal', 6)->nullable();
            $table->string('phone', 15)->nullable();
            $table->string('cellphone', 15)->nullable();
            $table->string('office', 15)->nullable();
            $table->string('nik')->nullable();
            $table->string('photo')->nullable();
            $table->string('key_tag_id')->unique();
            $table->integer('sales_person_id')->unsigned();
            $table->integer('guest_id')->unsigned()->nullable();
            $table->timestamp('joined');
            $table->integer('created_by')->unsigned();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('sales_person_id')->references('id')->on('users');
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('member_personals');
    }
}
