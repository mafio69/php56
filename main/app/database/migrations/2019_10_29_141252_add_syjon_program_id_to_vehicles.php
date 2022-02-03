<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSyjonProgramIdToVehicles extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('vehicles', function(Blueprint $table)
		{
			$table->unsignedInteger('syjon_program_id')->nullable()->index()->after('legal_protection');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('vehicles', function(Blueprint $table)
		{
			//
		});
	}

}
