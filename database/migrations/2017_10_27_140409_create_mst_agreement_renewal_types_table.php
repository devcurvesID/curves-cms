<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMstAgreementRenewalTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_agreement_renewal_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('agreement_renewal_type')->unique();
            $table->timestamps();
        });

        Schema::table('club_service_fees', function (Blueprint $table) {
            $table->foreign('agreement_renewal_type_id')->references('id')->on('mst_agreement_renewal_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('club_service_fees', function (Blueprint $table) {
            $table->dropForeign('club_service_fees_agreement_renewal_type_id_foreign');
        });

        Schema::dropIfExists('mst_agreement_renewal_types');
    }
}
