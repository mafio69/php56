<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInjuryTotalSourceCol extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('injury', function (Blueprint $table) {
			$table->tinyInteger('total_status_source')->after('theft_status_id')->nullable()->default(null)->comment('0 - wreck (total), 1 - theft');
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
