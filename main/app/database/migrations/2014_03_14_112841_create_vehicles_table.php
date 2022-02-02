<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVehiclesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('owners', function($table)
        {
            $table->increments('id')->unsigned();
            $table->string('name')->unique();
            $table->string('post');
            $table->string('city');
            $table->string('street');
            $table->timestamps();
        });

        Schema::create('vehicles', function($table)
        {
        	$table->increments('id')->unsigned();
        	$table->integer('owners_id')->unsigned();
        	$table->integer('parent_id')->unsigned()->default(0);
        	$table->string('registration');
        	$table->string('VIN');
        	$table->string('brand');
        	$table->string('model');
        	$table->string('engine');
        	$table->integer('year_production');
        	$table->date('first_registration');
        	$table->integer('mileage');
        	$table->timestamps();
        	$table->foreign('owners_id')->references('id')->on('owners'); 
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
