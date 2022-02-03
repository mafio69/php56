<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddTrucksToVmanageVehicles extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('vmanage_vehicles', function(Blueprint $table)
		{
			$table->boolean('if_truck')->default(0)->after('id');
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

		});
	}

}
