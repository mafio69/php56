<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCommisionColLeasingAgreementInsurances extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('leasing_agreement_insurances', function(Blueprint $table){
            $table->decimal('contribution_commission', 10, 2)->nullable()->after('contribution');
            $table->decimal('commission', 10, 2)->nullable()->after('contribution');
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
