<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCostRevenueCentersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cost_revenue_centers', function (Blueprint $table) {
            $table->integer('club_id')->unsigned()->primary('club_id');
            $table->string('cr_code')->unique();
            $table->string('name');
            $table->timestamps();

            $table->foreign('club_id')->references('id')->on('club_infos')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cost_revenue_centers');
    }
}
