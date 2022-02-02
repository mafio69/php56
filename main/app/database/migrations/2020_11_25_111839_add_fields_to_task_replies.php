<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToTaskReplies extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('task_replies', function (Blueprint  $table){
            $table->unsignedInteger('task_file_id')->nullable()->index()->after('task_id');
            $table->dropColumn('filename');
            $table->boolean('sendable')->default(1)->nullable()->after('task_file_id');
            $table->text('receivers')->nullable()->after('task_file_id');
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
