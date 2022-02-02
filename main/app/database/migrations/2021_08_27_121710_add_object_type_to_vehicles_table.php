<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddObjectTypeToVehiclesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('vehicles', function(Blueprint $table)
		{
			$table->string('object_type')->nullable()->after('nr_policy');
		});

        Schema::table('vmanage_vehicles', function(Blueprint $table)
        {
            $table->string('object_type')->nullable()->after('nr_policy');
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
