<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTaskTypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_task_type', function (Blueprint $table){
            $table->unsignedInteger('user_id')->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedInteger('task_type_id')->index();
            $table->foreign('task_type_id')->references('id')->on('task_types')->onDelete('cascade');
        });

        Schema::create('user_task_type_histories', function(Blueprint $table)
        {
            $table->increments('id');
            $table->unsignedInteger('triggerer_user_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('task_type_id')->nullable();
            $table->string('mode', 20)->nullable();
            $table->timestamps();

            $table->foreign('triggerer_user_id')->references('id')->on('users');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('task_type_id')->references('id')->on('task_types');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('without_restrictions_task_type')->default(0)->nullable()->after('without_restrictions');
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
