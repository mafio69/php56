<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInjuryStatusNoteAvailabilities extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('injury_status_note_availabilities', function (Blueprint $table){
		    $table->increments('id');
            $table->unsignedInteger('status_id')->nullable()->index();
            $table->string('status_type')->nullable()->index();
            $table->string('note')->nullable();
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
