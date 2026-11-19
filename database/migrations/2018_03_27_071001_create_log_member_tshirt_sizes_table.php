<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogMemberTshirtSizesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_member_tshirt_sizes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('member_id')->unsigned();
            $table->integer('tshirt_size')->unsigned();
            $table->date('size_date')->default(DB::raw('CURRENT_TIMESTAMP'));
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
        Schema::dropIfExists('log_member_tshirt_sizes');
    }
}
