<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInjuryNoteIdToInjuryFiles extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('injury_files', function(Blueprint $table)
		{
			$table->unsignedInteger('injury_note_id')->after('injury_id')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('injury_files', function(Blueprint $table)
		{
			//
		});
	}

}
