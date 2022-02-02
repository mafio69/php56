<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
	    Schema::create('api_modules', function (Blueprint $table){
	        $table->increments('id');
	        $table->string('name');
        });

	    Schema::create('api_module_keys', function(Blueprint $table){
	        $table->increments('id');
	        $table->unsignedInteger('api_module_id')->index();
	        $table->string('api_key');
	        $table->softDeletes();
	        $table->timestamps();
        });

		Schema::create('api_users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name')->nullable();
			$table->string('login');
			$table->string('password');
			$table->softDeletes();
			$table->timestamps();
		});

		Schema::create('api_user_api_module', function(Blueprint $table){
		    $table->increments('id');
		    $table->unsignedInteger('api_module_id')->nullable()->index();
            $table->unsignedInteger('api_user_id')->nullable()->index();
            $table->timestamps();
        });

		Schema::create('api_histories', function (Blueprint $table){
		    $table->increments('id');
		    $table->unsignedInteger('api_module_id')->nullable()->index();
		    $table->unsignedInteger('api_user_id')->nullable()->index();
		    $table->text('request');
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
		Schema::drop('api_users');
	}

}
