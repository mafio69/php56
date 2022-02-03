<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddRemarksToLeasingAgreements extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('leasing_agreements', function(Blueprint $table)
		{
			$table->text('remarks')->after('filename')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('leasing_agreements', function(Blueprint $table)
		{

		});
	}

}
