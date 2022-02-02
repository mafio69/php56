<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForwardDateToInjuryInvoicesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('injury_invoices', function (Blueprint $table){
		    $table->dateTime('forward_date')->nullable()->after('payment_date');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('injury_invoices', function (Blueprint $table){
            $table->dropColumn('forward_date');
        });
	}

}
