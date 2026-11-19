<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommunityPartnerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('community_partners', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cp_name');
            $table->string('pic_name')->nullable();
            $table->string('address');
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->enum('status',['active','inactive'])->default('active');
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
        Schema::dropIfExists('community_partners');
    }
}
