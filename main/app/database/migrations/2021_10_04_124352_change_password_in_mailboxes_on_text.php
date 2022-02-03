<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePasswordInMailboxesOnText extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('task_mailboxes', function (Blueprint $table){
		    $table->renameColumn('password','opassword');
        });

        Schema::table('task_mailboxes', function (Blueprint $table){
            $table->text('password')->nullable()->after('login');
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
