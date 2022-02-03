<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskMaileboxesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('task_mailboxes', function (Blueprint  $table){
		    $table->increments('id');
		    $table->string('name')->nullable();
		    $table->string('server')->nullable();
		    $table->string('login')->nullable();
		    $table->string('password')->nullable();
		    $table->unsignedInteger('task_source_id')->nullable()->index();
		    $table->softDeletes();
		    $table->nullableTimestamps();
        });

		Schema::table('tasks', function (Blueprint  $table){
		   $table->unsignedInteger('task_mailbox_id')->nullable()->after('task_source_id')->index();
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
