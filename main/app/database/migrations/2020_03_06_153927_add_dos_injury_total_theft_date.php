<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDosInjuryTotalTheftDate extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('dos_other_injury', function(Blueprint $table){
			$table->date('date_end_theft')->after('date_end')->nullable()->default(null);
			$table->date('date_end_total')->after('date_end')->nullable()->default(null);
			$table->date('date_end_normal')->after('date_end')->nullable()->default(null);
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
