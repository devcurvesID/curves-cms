<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsKeyReturnedColumnToMemberLockersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('member_lockers',function(Blueprint $table){
            $table->enum('is_key_returned',['Yes','No'])->default('Yes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('member_lockers',function(Blueprint $table){
            $table->dropColumn('is_key_returned');
        });
    }
}
