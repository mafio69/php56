<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInjuryStepStageNextStageColumn extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('injury_step_stages', function (Blueprint $table) {
			$table->unsignedInteger('parent_stage_id')->nullable()->after('next_injury_step_id');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('injury_step_stages', function (Blueprint $table) {
			$table->dropColumn('parent_stage_id');
        });
	}

}
