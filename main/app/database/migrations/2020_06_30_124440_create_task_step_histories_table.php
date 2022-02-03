<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskStepHistoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('task_step_histories', function (Blueprint $table){
		    $table->increments('id');
		    $table->unsignedInteger('task_instance_id')->nullable()->index();
		    $table->unsignedInteger('task_step_id')->nullable()->index();
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
		//
	}

}
