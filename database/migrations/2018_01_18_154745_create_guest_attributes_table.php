<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGuestAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guest_attributes', function (Blueprint $table) {
            $table->integer('guest_id')->unsigned()->index();
            $table->integer('attribute_id')->unsigned()->index();
            $table->timestamps();

            $table->primary(['guest_id', 'attribute_id']);

            $table->foreign('guest_id')->references('id')->on('buddy_referral_leads')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('guest_attributes');
    }
}
