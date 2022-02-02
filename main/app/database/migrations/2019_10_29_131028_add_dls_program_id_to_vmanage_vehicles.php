<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDlsProgramIdToVmanageVehicles extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('vmanage_vehicles', function(Blueprint $table)
		{
			$table->unsignedInteger('dls_program_id')->nullable()->index()->after('legal_protection');

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
