<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveClientFromPlans extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
	    Schema::create('syjon_programs', function (Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('syjon_program_id')->nullable();
            $table->string('name')->nullable();
            $table->string('name_key')->nullable();
            $table->timestamps();
        });

        Schema::create('dls_programs', function (Blueprint $table){
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('name_key')->nullable();
            $table->timestamps();
        });

		Schema::table('plans', function(Blueprint $table)
		{
			$table->dropColumn('owner_id');
			$table->string('sales_program')->nullable()->after('name');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('plans', function(Blueprint $table)
		{
			//
		});
	}

}
