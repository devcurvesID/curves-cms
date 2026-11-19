<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommunityPartnerMessageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('community_partner_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cp_id')->unsigned();
            $table->text('message');
            $table->date('from_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->date('thru_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamps();

            $table->foreign('cp_id')->references('id')->on('community_partners')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('community_partner_messages');
    }
}
