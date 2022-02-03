<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInsurancePolicyInfoToVehicle extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('vehicles', function(Blueprint $table)
		{
			$table->string('risks')->nullable()->after('insurance');
		});

		Schema::table('vmanage_vehicles', function (Blueprint $table){
            $table->string('risks')->nullable()->after('insurance');
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
