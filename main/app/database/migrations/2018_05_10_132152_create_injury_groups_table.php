<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInjuryGroupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('injury_groups', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
		});

		Schema::table('injury_steps', function (Blueprint $table){
		   $table->unsignedInteger('injury_group_id')->nullable();
        });
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('injury_steps', function (Blueprint $table){
            $table->dropColumn('injury_group_id');
        });
		Schema::drop('injury_groups');
	}

}
