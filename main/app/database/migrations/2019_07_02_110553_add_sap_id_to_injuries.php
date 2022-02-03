<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSapIdToInjuries extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('injury', function(Blueprint $table)
		{
			$table->string('sap_id', 20)->nullable()->after('is_cas_case');
			$table->dateTime('sap_date')->nullable()->after('sap_id');
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
			//
		});
	}

}
