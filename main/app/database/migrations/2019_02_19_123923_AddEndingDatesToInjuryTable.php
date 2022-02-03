<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEndingDatesToInjuryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('injury', function(Blueprint $table)
		{
            $table->dateTime('date_end_normal')->nullable()->after('date_end');
		    $table->dateTime('date_end_total')->nullable()->after('date_end');
            $table->dateTime('date_end_theft')->nullable()->after('date_end');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('injury', function(Blueprint $table)
		{
			//
		});
	}

}
