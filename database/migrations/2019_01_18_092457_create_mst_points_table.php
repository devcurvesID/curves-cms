<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMstPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_points', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->enum('type',['Membership','APO','BR','NN','Sales','Flag','FWO','FL','Payment']);
            $table->string('type_detail');
            $table->enum('target_point',['Staff','Club','Member']);
            $table->integer('point');
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
        Schema::dropIfExists('mst_points');
    }
}
