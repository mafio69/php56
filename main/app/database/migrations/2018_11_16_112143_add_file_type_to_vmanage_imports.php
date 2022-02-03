<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddFileTypeToVmanageImports extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('vmanage_imports', function(Blueprint $table)
		{
			$table->tinyInteger('file_type')->nullable()->after('filename');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('vmanage_imports', function(Blueprint $table)
		{

		});
	}

}
