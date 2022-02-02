<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddIsUptodateColumnToCommissionReportsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('commission_reports', function(Blueprint $table)
		{
			$table->boolean('is_uptodate')->default(1)->after('is_individual');
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
			$table->dropColumn('is_uptodate');
		});
	}

}
