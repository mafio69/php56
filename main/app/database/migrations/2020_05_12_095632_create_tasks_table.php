<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTasksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
	    Schema::create('task_sources', function(Blueprint $table){
	        $table->increments('id');
	        $table->string('name')->nullable();
	        $table->timestamps();
        });

	    Schema::create('task_groups', function (Blueprint $table){
	        $table->increments('id');
	        $table->string('name');
	        $table->timestamps();
        });

	    Schema::create('task_subgroups', function (Blueprint $table){
	        $table->increments('id');
	        $table->unsignedInteger('task_group_id')->nullable();
	        $table->string('name')->nullable();
	        $table->timestamps();

        });

	    Schema::create('task_types', function (Blueprint $table){
	        $table->increments('id');
	        $table->unsignedInteger('task_group_id')->nullable();
	        $table->unsignedInteger('task_subgroup_id')->nullable();
	        $table->string('name');
	        $table->timestamps();

        });

		Schema::create('tasks', function(Blueprint $table)
		{
			$table->increments('id');
			$table->unsignedInteger('task_source_id')->nullable();
			$table->unsignedInteger('task_type_id')->nullable();

			$table->string('subject')->nullable();
			$table->text('content')->nullable();

			$table->softDeletes();
			$table->timestamps();
		});

		Schema::create('task_files', function(Blueprint $table){
		    $table->increments('id');
		    $table->unsignedInteger('task_id')->nullable();
		    $table->string('filename')->nullable();
		    $table->string('original_filename')->nullable();
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
		Schema::drop('tasks');
	}

}
