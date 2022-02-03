<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShortNameToInjuryCompensationDecisionTypes extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('injury_compensation_decision_types', function(Blueprint $table)
		{
			$table->string('short_name')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('injury_compensation_decision_types', function(Blueprint $table)
		{
			//
		});
	}

}
