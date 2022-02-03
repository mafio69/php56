<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToInjury extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('injury', function(Blueprint $table){
            $table->boolean('dsp_notification')->nullable()->default(0)->after('settlement_cost_estimate');
            $table->boolean('vindication')->nullable()->default(0)->after('dsp_notification');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('injury', function (Blueprint $table){
		    $table->dropColumn('vindication');
		    $table->dropColumn('dsp_notification');
        });
	}

}
