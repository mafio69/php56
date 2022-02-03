<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractorGroupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('contractor_groups', function (Blueprint $table){
		    $table->increments('id');
		    $table->string('name');
		    $table->timestamps();
        });

		Schema::table('companies', function (Blueprint $table){
		    $table->unsignedInteger('contractor_group_id')->nullable()->index()->after('billing_cycle_id');
		    $table->dropColumn('_type');
		    $table->dropColumn('_company_group_id');
        });

		Schema::table('plans', function (Blueprint $table){
		    $table->dropColumn('client_id');
		    $table->unsignedInteger('owner_id')->nullable()->index()->after('name');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}

}
