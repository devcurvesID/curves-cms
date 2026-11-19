<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffFunctionalRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_functional_roles', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->integer('functional_role_id')->unsigned();
            $table->timestamps();

            $table->primary(['user_id', 'functional_role_id']);

            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('functional_role_id')->references('id')->on('mst_functional_roles')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('staff_functional_roles');
    }
}
