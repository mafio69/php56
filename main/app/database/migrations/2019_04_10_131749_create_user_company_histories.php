<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserCompanyHistories extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('user_company_histories', function(Blueprint $table)
        {
            $table->increments('id');
            $table->unsignedInteger('triggerer_user_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('vmanage_company_id')->nullable();
            $table->string('mode', 20)->nullable();
            $table->timestamps();

            $table->foreign('triggerer_user_id')->references('id')->on('users');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('vmanage_company_id')->references('id')->on('vmanage_companies');
        });
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_company_histories');
	}

}
