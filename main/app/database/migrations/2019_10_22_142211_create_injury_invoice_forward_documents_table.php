<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInjuryInvoiceForwardDocumentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('injury_invoice_forward_document_types', function (Blueprint $table){
            $table->increments('id');
            $table->string('name');
        });

		Schema::create('injury_invoice_forward_documents', function (Blueprint $table){
		    $table->increments('id');
		    $table->unsignedInteger('injury_invoice_id')->nullable();
		    $table->unsignedInteger('injury_invoice_forward_document_type_id')->nullable();
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
		Schema::drop('injury_invoice_forward_documents');
	}

}
