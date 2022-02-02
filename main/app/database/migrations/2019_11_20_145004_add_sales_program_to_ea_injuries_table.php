<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSalesProgramToEaInjuriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('ea_injuries', function(Blueprint $table)
		{
			$table->string('sales_program')->nullable()->after('case_number');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('ea_injuries', function(Blueprint $table)
		{
			//
		});
	}

}
