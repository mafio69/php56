<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInjurySapResponsesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('injury_sap_responses', function(Blueprint $table){
		    $table->increments('id');
		    $table->unsignedInteger('injury_sap_entity_id')->nullable()->index('isr_injury_sap_entity_id_index');
		    $table->string('typ')->nullable();
		    $table->string('kod')->nullable();
		    $table->text('message')->nullable();
		    $table->nullableTimestamps();
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
