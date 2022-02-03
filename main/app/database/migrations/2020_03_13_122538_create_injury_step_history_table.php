<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInjuryStepHistoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('injury_step_history', function(Blueprint $table){
		    $table->increments('id');
		    $table->unsignedInteger('user_id')->nullable();
		    $table->unsignedInteger('injury_id')->nullable();
		    $table->unsignedInteger('prev_step_id')->nullable();
		    $table->unsignedInteger('next_step_id')->nullable();
		    $table->unsignedInteger('injury_step_stage_id')->nullable();
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
		//
	}

}
