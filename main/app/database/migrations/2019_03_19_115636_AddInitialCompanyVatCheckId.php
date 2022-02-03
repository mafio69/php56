<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInitialCompanyVatCheckId extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('injury_invoices', function(Blueprint $table)
		{
            $table->unsignedInteger('initial_company_vat_check_id')->nullable()->after('id');
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
