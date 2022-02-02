<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Branchestogarages extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		 Schema::create('branches_typegarages', function($table)
        {
            $table->increments('id')->unsigned();
            $table->integer('branch_id')->unsigned();
            $table->integer('typegarages_id')->unsigned();
            $table->foreign('branch_id')->references('id')->on('branches'); // assumes a branches table
            $table->foreign('typegarages_id')->references('id')->on('typegarages'); // assumes a brands table
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
