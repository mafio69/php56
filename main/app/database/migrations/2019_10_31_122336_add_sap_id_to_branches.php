<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSapIdToBranchess extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('branches', function(Blueprint $table)
		{
			$table->string('sap_id')->nullable()->after('id');
		});

		Schema::table('companies', function(Blueprint $table){
		    $table->dropColumn('sap_id');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('branches', function(Blueprint $table)
		{
			//
		});
	}

}
