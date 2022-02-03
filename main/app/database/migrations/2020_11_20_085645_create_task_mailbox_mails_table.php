<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskMailboxMailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('task_mailbox_mails', function (Blueprint $table){
		    $table->increments('id');
		    $table->unsignedInteger('task_mailbox_id')->nullable()->index();
		    $table->string('mail')->nullable();
		    $table->unsignedInteger('task_group_id')->nullable()->index();
		    $table->timestamps();;
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('task_mailbox_mails');
	}

}
