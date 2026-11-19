<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMstPointsTypeChoice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            DB::statement('ALTER TABLE mst_points DROP CONSTRAINT mst_points_type_check;');
            DB::statement('ALTER TABLE mst_points ADD CONSTRAINT mst_points_type_check CHECK (type::TEXT = ANY (ARRAY[
              \'Membership\'::CHARACTER VARYING, 
              \'APO\'::CHARACTER VARYING, 
              \'BR\'::CHARACTER VARYING, 
              \'NN\'::CHARACTER VARYING, 
              \'Sales\'::CHARACTER VARYING,
              \'Flag\'::CHARACTER VARYING,
              \'FWO\'::CHARACTER VARYING,
              \'FL\'::CHARACTER VARYING,
              \'Payment\'::CHARACTER VARYING,
              \'Product\'::CHARACTER VARYING,
              \'Attendance\'::CHARACTER VARYING,
              \'WM\'::CHARACTER VARYING,
              \'WO\'::CHARACTER VARYING,
              \'TM\'::CHARACTER VARYING,
              \'Target\'::CHARACTER VARYING,
              \'Locker\'::CHARACTER VARYING,
              \'TOG\'::CHARACTER VARYING
              ]::TEXT[]))');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
