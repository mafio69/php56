<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddTestReportIdToCommissionReportsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('commission_reports', function(Blueprint $table)
		{
			$table->unsignedInteger('test_report_id')->nullable()->after('id');
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
			$table->dropColumn('test_report_id');
		});
	}

}
