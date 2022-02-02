<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSapRodzszkToInjuryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('injury', function(Blueprint $table)
		{
			$table->string('sap_rodzszk', 20)->nullable()->after('is_cas_case');
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
