<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddInjuryTotalStepStageIdToInjury extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('injury', function(Blueprint $table)
		{
			$table->unsignedInteger('injury_total_step_stage_id')->nullable()->after('injury_step_stage_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('injury', function(Blueprint $table)
		{

		});
	}

}
