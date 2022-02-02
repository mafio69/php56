<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDocumentTemplatesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('document_templates', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name')->nullable();
			$table->string('slug')->nullable();
			$table->timestamps();
		});

		Schema::table('owners', function(Blueprint $table){
		    $table->unsignedInteger('document_template_id')->nullable()->index()->after('owners_group_id');
            $table->unsignedInteger('conditional_document_template_id')->nullable()->index()->after('document_template_id');
        });
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('document_templates');
	}

}
