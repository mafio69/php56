<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddFilenameColumnToCommissionReportsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('commission_reports', function(Blueprint $table)
		{
			$table->string('filename')->nullable()->after('user_id');
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
			$table->dropColumn('filename');
		});
	}

}
