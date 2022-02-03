<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEaCaseNumberToInjuriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('injury', function(Blueprint $table)
		{
			$table->string('ea_case_number')->nullable()->after('case_nr');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('injuries', function(Blueprint $table)
		{
			//
		});
	}

}
