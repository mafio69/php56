<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInjuryStatusHistories extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('injury_contract_status_histories', function(Blueprint $table)
		{
			$table->increments('id');
			$table->unsignedInteger('injury_id')->nullable();
			$table->unsignedInteger('contract_status_id')->nullable();
			$table->unsignedInteger('vehicle_id')->nullable();
			$table->string('vehicle_type')->nullable();
			$table->timestamps();
		});

		Schema::table('injury', function (Blueprint $table){
		    $table->unsignedInteger('contract_status_id')->nullable()->after('vehicle_type');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
	    Schema::table('injury', function (Blueprint $table){
	        $table->dropColumn('contract_status_id');
        });
		Schema::drop('injury_contract_status_histories');
	}

}
