<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSalesProgramToVehiclesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('vehicles', function(Blueprint $table)
		{
			$table->string('sales_program')->nullable()->after('legal_protection');
		});

		Schema::table('vmanage_vehicles', function (Blueprint $table){
            $table->string('sales_program')->nullable()->after('legal_protection');
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
