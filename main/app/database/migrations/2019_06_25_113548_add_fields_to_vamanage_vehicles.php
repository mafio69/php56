<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToVamanageVehicles extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('vmanage_vehicles', function(Blueprint $table)
		{
			$table->date('end_leasing')->nullable()->after('agreement_date');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('vmanage_vehicles', function(Blueprint $table)
		{
			//
		});
	}

}
