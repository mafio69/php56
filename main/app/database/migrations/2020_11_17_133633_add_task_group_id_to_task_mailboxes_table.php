<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTaskGroupIdToTaskMailboxesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('task_mailboxes', function (Blueprint $table){
		    $table->dropColumn('task_type_id');
		    $table->unsignedInteger('task_group_id')->index()->after('id');
        });

		Schema::table('task_groups', function (Blueprint  $table){
		    $table->tinyInteger('ord')->nullable()->after('name');
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
