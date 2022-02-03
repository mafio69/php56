<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDocInjuryTotalTheftDate extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('dos_other_injury', function(Blueprint $table){

			if (Schema::hasColumn('dos_other_injury', 'date_end_theft')) $table->dropColumn('date_end_theft');
			if (Schema::hasColumn('dos_other_injury', 'date_end_total')) $table->dropColumn('date_end_total');
			if (Schema::hasColumn('dos_other_injury', 'date_end_normal')) $table->dropColumn('date_end_normal');

		});
		
		Schema::table('dos_other_injury', function(Blueprint $table){
			$table->datetime('date_end_theft')->after('date_end')->nullable()->default(null);
			$table->datetime('date_end_total')->after('date_end')->nullable()->default(null);
			$table->datetime('date_end_normal')->after('date_end')->nullable()->default(null);
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
