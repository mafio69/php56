<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToObjectsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('objects', function(Blueprint $table)
		{
            $table->unsignedInteger('syjon_vehicle_id')->nullable()->after('id');
            $table->unsignedInteger('syjon_contract_id')->nullable()->after('id');
            $table->unsignedInteger('syjon_contract_internal_agreement_id')->nullable()->after('id');
            $table->unsignedInteger('syjon_policy_id')->nullable()->after('id');
            $table->string('source')->after('id')->nullable()->default('baza szkód');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('objects', function(Blueprint $table)
		{
			//
		});
	}

}
