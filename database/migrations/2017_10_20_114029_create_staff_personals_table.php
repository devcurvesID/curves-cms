<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffPersonalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_personals', function (Blueprint $table) {
            $table->integer('user_id')->unsigned()->primary('user_id');
            /*$table->date('birth');
            $table->enum('sex', ['M', 'F'])->default('F');
            $table->string('address', 255);
            $table->string('postal', 6)->nullable();
            $table->string('phone', 15);
            $table->string('cellphone', 15)->nullable();*/
            $table->string('email_personal', 50)->nullable();
            /*$table->string('nik')->nullable();*/
            $table->string('npwp')->nullable();
            /*$table->string('photo')->nullable();
            $table->string('key_tag_id')->unique();
            $table->timestamp('joined');*/
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('staff_personals');
    }
}
