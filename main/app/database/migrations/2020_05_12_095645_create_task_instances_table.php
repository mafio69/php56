<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTaskInstancesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
	    Schema::create('task_steps', function (Blueprint $table){
	        $table->increments('id');
	        $table->string('name');
	        $table->timestamps();
        });

		Schema::create('task_instances', function(Blueprint $table)
		{
			$table->increments('id');
			$table->unsignedInteger('task_id')->nullable();
			$table->unsignedInteger('user_id')->nullable();
			$table->unsignedInteger('task_step_id')->nullable();

			$table->dateTime('date_collect')->nullable();
			$table->dateTime('date_complete')->nullable();

			$table->softDeletes();
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
		Schema::drop('task_instances');
	}

}
