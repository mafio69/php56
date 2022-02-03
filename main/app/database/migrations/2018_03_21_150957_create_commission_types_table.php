<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCommissionTypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('commission_types', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
		});

		Schema::create('billing_cycles', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('billing_cycles');
		Schema::drop('commission_types');
	}

}
