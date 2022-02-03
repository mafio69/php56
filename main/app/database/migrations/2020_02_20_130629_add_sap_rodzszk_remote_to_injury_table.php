<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSapRodzszkRemoteToInjuryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('injury', function (Blueprint $table){
		    $table->string('sap_rodzszk_remote')->nullable()->after('sap_rodzszk');
		    $table->string('sap_stanszk')->nullable()->after('sap_id');
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
