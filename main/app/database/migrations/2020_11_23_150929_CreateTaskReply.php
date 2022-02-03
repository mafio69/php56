<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskReply extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('task_replies', function (Blueprint  $table){
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable()->index();
            $table->unsignedInteger('task_id')->nullable()->index();
            $table->string('filename')->nullable();
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
		Schema::dropIfExists('task_replies');
	}

}
