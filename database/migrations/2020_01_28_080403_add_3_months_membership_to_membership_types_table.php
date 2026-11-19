<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Add3MonthsMembershipToMembershipTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            DB::statement('ALTER TABLE membership_types DROP CONSTRAINT membership_types_duration_check;');
            DB::statement('ALTER TABLE membership_types ADD CONSTRAINT membership_types_duration_check CHECK (duration::TEXT = ANY (ARRAY[\'1\'::CHARACTER VARYING, \'3\'::CHARACTER VARYING, \'6\'::CHARACTER VARYING, \'12\'::CHARACTER VARYING, \'18\'::CHARACTER VARYING, \'24\'::CHARACTER VARYING]::TEXT[]))');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('membership_types', function (Blueprint $table) {
            //
        });
    }
}
