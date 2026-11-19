<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_attributes', function (Blueprint $table) {
            $table->integer('user_id')->unsigned()->index();
            $table->integer('attribute_id')->unsigned()->index();
            $table->timestamps();

            $table->primary(['user_id', 'attribute_id']);

            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('attribute_id')->references('id')->on('mst_member_attributes')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('member_attributes');
    }
}
