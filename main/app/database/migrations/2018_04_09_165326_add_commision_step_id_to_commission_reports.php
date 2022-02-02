<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddCommisionStepIdToCommissionReports extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('commission_reports', function(Blueprint $table)
		{
			$table->unsignedInteger('commission_step_id')->nullable()->after('id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('commission_reports', function(Blueprint $table)
		{
			$table->dropColumn('commission_step_id');
		});
	}

}
