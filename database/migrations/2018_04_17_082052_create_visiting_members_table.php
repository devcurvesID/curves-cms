<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVisitingMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visiting_members', function (Blueprint $table) {
            $table->increments('id');
            $table->string('member_name');
            $table->string('member_id');
            $table->date('membership_expiration_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('origin_club');
            $table->string('origin_club_address')->nullable();
            $table->string('origin_club_phone_number')->nullable();
            $table->string('origin_club_fax_number')->nullable();
            $table->string('origin_club_email')->nullable();
            $table->date('first_use_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->date('validity_date')->default(DB::raw('CURRENT_TIMESTAMP'));
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
        Schema::dropIfExists('visiting_members');
    }
}
