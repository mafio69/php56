<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRoleHistoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_role_histories', function(Blueprint $table)
		{
			$table->increments('id');
			$table->unsignedInteger('triggerer_user_id')->nullable();
			$table->unsignedInteger('user_id')->nullable();
			$table->unsignedInteger('module_id')->nullable();
			$table->unsignedInteger('role_id')->nullable();
            $table->string('mode', 20)->nullable();
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_role_histories');
	}

}
