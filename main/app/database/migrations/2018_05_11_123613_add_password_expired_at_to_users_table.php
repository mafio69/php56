<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddPasswordExpiredAtToUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('users', function(Blueprint $table)
		{
            $table->timestamp('locked_at')->nullable()->after('active');
            $table->integer('failed_attempts')->nullable()->after('active');
            $table->date('password_expired_at')->nullable()->after('active');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('users', function(Blueprint $table)
		{
			$table->dropColumn('password_expired_at');
            $table->dropColumn('failed_attempts');
            $table->dropColumn('locked_at');
		});
	}

}
