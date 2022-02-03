<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInjuryNotesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('injury_notes', function(Blueprint $table){
		    $table->increments('id');
		    $table->unsignedInteger('injury_id')->nullable();
		    $table->unsignedInteger('user_id')->nullable();
            $table->string("roknotatki", 4)->nullable();
            $table->string( "nrnotatki", 10)->nullable();
            $table->string( "obiekt", 1)->nullable();
            $table->string( "temat", 80)->nullable();
            $table->date( "data", 8)->nullable();
            $table->time( "uzeit", 6)->nullable();
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
