<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInjuryGapsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
	    Schema::create('gap_types', function (Blueprint $table){
	        $table->increments('id');
	        $table->string('name');
        });

		Schema::create('injury_gaps', function(Blueprint $table)
		{
			$table->increments('id');
			$table->unsignedInteger('injury_id')->nullable();
			$table->unsignedInteger('insurance_company_id')->nullable();
			$table->unsignedInteger('gap_type_id')->nullable();
			$table->decimal('insurance_amount')->nullable();
			$table->tinyInteger('netto_brutto')->nullable();
			$table->decimal('forecast')->nullable();
			$table->string('injury_number')->nullable();
			$table->timestamps();

			$table->index('injury_id');
			$table->index('injury_number');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('injury_gaps');
	}

}
