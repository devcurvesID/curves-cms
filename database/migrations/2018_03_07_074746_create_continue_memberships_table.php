<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContinueMembershipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('continue_memberships', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('membership_type_id')->unsigned();
            $table->date('from_date');
            $table->date('thru_date');
            $table->enum('member_vip_status', ['General', 'VIP'])->default('General');
            $table->enum('status', ['Pending', 'Active', 'Done'])->default('Pending');
            $table->enum('payment_status', ['NotSet', 'Set'])->default('NotSet');
            $table->integer('staff_id')->unsigned();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('membership_type_id')->references('id')->on('membership_types');
            $table->foreign('staff_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('continue_memberships');
    }
}
