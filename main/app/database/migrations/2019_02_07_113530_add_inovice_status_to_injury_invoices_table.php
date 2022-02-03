<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInoviceStatusToInjuryInvoicesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('injury_invoice_statuses', function (Blueprint $table){
		    $table->increments('id');
		    $table->string('name');
        });

		Schema::table('injury_invoices', function (Blueprint $table){
		    $table->unsignedInteger('injury_invoice_status_id')->nullable()->after('id');
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
