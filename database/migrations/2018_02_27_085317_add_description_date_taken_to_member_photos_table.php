<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDescriptionDateTakenToMemberPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('member_photos',function(Blueprint $table){
            $table->text('description')->nullable();
            $table->date('date_taken')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('member_photos',function(Blueprint $table){
            $table->dropColumn('description');
            $table->dropColumn('date_taken');
        });
    }
}
