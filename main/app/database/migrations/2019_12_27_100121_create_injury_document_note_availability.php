<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInjuryDocumentNoteAvailability extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('injury_document_note_availabilities', function (Blueprint $table){
		    $table->increments('id');
		    $table->unsignedInteger('document_id')->nullable()->index();
		    $table->string('document_type')->nullable()->index();
		    $table->string('note')->nullable();
		    $table->unsignedInteger('receive_id')->nullable();
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
