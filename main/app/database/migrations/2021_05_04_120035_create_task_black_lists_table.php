<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskBlackListsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('task_black_lists', function (Blueprint $table){
		    $table->increments('id');
		    $table->unsignedInteger('user_id')->nullable()->index();
		    $table->string('email')->nullable();
		    $table->string('topic')->nullable();
		    $table->nullableTimestamps();
		    $table->softDeletes();
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
