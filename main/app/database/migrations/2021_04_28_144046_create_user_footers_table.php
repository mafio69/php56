<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserFootersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('users', function (Blueprint $table){
           $table->dropColumn('email_footer');
        });

		Schema::create('user_footers', function(Blueprint $table)
		{
			$table->increments('id');
			$table->unsignedInteger('user_id')->nullable()->index();
			$table->text('footer')->nullable();
			$table->softDeletes();
			$table->nullableTimestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_footers');
	}

}
