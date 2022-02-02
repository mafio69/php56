<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInjuryNoteIdToInjuryInvoicesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('injury_invoices', function(Blueprint $table)
		{
			$table->unsignedInteger('injury_note_id')->after('id')->index()->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('injury_invoices', function(Blueprint $table)
		{
			//
		});
	}

}
