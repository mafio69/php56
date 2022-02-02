<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePermissionHistoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('permission_histories', function(Blueprint $table)
		{
			$table->increments('id');
			$table->unsignedInteger('triggerer_user_id')->nullable();
			$table->unsignedInteger('permission_id')->nullable();
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
		Schema::drop('permission_histories');
	}

}
