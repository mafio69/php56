<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInjuryProcessingTypes extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('injury_processing_types', function (Blueprint $table){
		    $table->increments('id');
		    $table->string('name')->nullable();
        });

		Schema::table('history_type', function (Blueprint $table){
		    $table->unsignedInteger('injury_processing_type_id')->nullable();
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
