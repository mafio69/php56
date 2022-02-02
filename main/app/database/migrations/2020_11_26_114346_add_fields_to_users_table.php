<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
	    Schema::create('departments', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable()->index();
            $table->string('name');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('teams', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable()->index();
            $table->string('name');
            $table->softDeletes();
            $table->timestamps();
        });

	    Schema::table('users', function (Blueprint  $table){
            $table->unsignedInteger('department_id')->nullable()->index()->after('password');
            $table->unsignedInteger('team_id')->nullable()->index()->after('department_id');
            $table->string('phone_number')->nullable()->after('team_id');
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
