<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddSyjonColumns extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('clients', function(Blueprint $table)
		{
            $table->unsignedInteger('syjon_contractor_id')->nullable()->after('id');
		});

        Schema::table('owners', function(Blueprint $table)
        {
            $table->unsignedInteger('syjon_contractor_id')->nullable()->after('id');
        });

        Schema::table('vehicles', function(Blueprint $table)
        {
            $table->unsignedInteger('syjon_vehicle_id')->nullable()->after('id');
            $table->unsignedInteger('syjon_contract_id')->nullable()->after('id');
            $table->unsignedInteger('syjon_contract_internal_agreement_id')->nullable()->after('id');
            $table->unsignedInteger('syjon_policy_id')->nullable()->after('id');
        });
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('syjon_columns', function(Blueprint $table)
		{

		});
	}

}
