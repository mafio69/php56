<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveStanszkRemotFromInjuryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('injury', function(Blueprint $table)
		{
			$table->dropColumn('sap_stanszk_remote');
            $table->dropColumn('sap_rodzszk_remote');
            $table->dropColumn('sap_id');
            $table->dropColumn('sap_date');
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
