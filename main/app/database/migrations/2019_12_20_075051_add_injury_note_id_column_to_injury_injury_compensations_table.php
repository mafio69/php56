<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInjuryNoteIdColumnToInjuryInjuryCompensationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('injury_compensations', function(Blueprint $table)
		{
			$table->unsignedInteger('injury_note_id')->nullable()->index()->after('id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('injury_compensations', function(Blueprint $table)
		{
			//
		});
	}

}
