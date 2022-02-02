<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRefercableColumnToInjuryNotesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('injury_notes', function(Blueprint $table)
		{
			$table->unsignedInteger('referenceable_id')->nullable()->after('id')->index();
			$table->string('referenceable_type')->nullable()->after('referenceable_id')->index();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('injury_notes', function(Blueprint $table)
		{
			//
		});
	}

}
