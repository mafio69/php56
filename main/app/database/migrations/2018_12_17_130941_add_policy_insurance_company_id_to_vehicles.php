<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPolicyInsuranceCompanyIdToVehicles extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('vehicles', function (Blueprint $table){
		    $table->unsignedInteger('policy_insurance_company_id')->nullable()->after('insurance_company_id');
        });

        Schema::table('vmanage_vehicles', function (Blueprint $table){
            $table->unsignedInteger('policy_insurance_company_id')->nullable()->after('insurance_company_id');
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
