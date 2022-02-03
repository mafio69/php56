<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLeasingAgreementInsuranceGroupRowIdToLeasingAgreementInsurancesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
	    Schema::table('leasing_agreement_insurances', function (Blueprint $table){
	        $table->unsignedInteger('leasing_agreement_insurance_group_row_id')->nullable()->after('leasing_agreement_id')->index('lai_insrance_group_row_id_index');
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
