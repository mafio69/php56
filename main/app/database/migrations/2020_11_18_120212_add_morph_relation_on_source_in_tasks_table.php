<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMorphRelationOnSourceInTasksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tasks', function (Blueprint $table){
		    $table->unsignedInteger('source_id')->nullable()->index()->after('task_source_id');
            $table->string('source_type')->nullable()->index()->after('source_id');
		    $table->dropColumn('task_mailbox_id');
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
