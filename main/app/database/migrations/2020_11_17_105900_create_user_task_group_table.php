<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTaskGroupTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::dropIfExists('user_task_type_histories');
		Schema::dropIfExists('user_task_type');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('without_restrictions_task_type');
        });

        Schema::create('user_task_group', function (Blueprint $table){
            $table->unsignedInteger('user_id')->index();
            $table->unsignedInteger('task_group_id')->index();
        });

        Schema::create('user_task_group_histories', function(Blueprint $table)
        {
            $table->increments('id');
            $table->unsignedInteger('triggerer_user_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('task_group_id')->index()->nullable();
            $table->string('mode', 20)->index()->nullable();
            $table->timestamps();

            $table->foreign('triggerer_user_id')->references('id')->on('users');
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('without_restrictions_task_group')->default(0)->nullable()->after('without_restrictions');
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
