<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskCommentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('task_comments', function (Blueprint $table){
		    $table->increments('id');
		    $table->unsignedInteger('task_id')->nullable()->index();
		    $table->unsignedInteger('user_id')->nullable()->index();
		    $table->text('content');
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
		//
	}

}
