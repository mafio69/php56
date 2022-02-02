<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInsuranceCompanyParentIdColumn extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('insurance_companies', function(Blueprint $table){
			$table->integer('parent_id')->nullable()->after('sap_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('insurance_companies', function(Blueprint $table){
			$table->dropColumn('parent_id');
		});
	}

}
