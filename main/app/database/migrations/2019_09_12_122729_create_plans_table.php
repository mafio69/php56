<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlansTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('plans', function (Blueprint $table){
		    $table->increments('id');
		    $table->boolean('brands')->default(0);
		    $table->string('name')->nullable();
		    $table->unsignedInteger('client_id')->nullable()->index();
		    $table->timestamps();
		    $table->softDeletes();
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('plans');
	}

}
