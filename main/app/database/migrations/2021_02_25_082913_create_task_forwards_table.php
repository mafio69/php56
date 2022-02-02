<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskForwardsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('task_forwards', function(Blueprint  $table){
		    $table->increments('id');
            $table->unsignedInteger('user_id')->nullable()->index();
            $table->unsignedInteger('task_id')->nullable()->index();
            $table->unsignedInteger('task_file_id')->nullable()->index();
            $table->boolean('sendable')->default(1)->nullable();
            $table->text('receivers')->nullable();
		    $table->nullableTimestamps();
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('task_forwards');
	}

}
