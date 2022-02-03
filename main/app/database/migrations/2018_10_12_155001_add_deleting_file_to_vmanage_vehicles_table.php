<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddDeletingFileToVmanageVehiclesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('vmanage_vehicles', function(Blueprint $table)
		{
			$table->string('deleting_file')->nullable()->after('deleted_at');
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
