<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeInjuryFileNullable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        DB::statement('ALTER TABLE injury_files MODIFY nr varchar(100) NULL DEFAULT NULL;');
        DB::statement('ALTER TABLE injury_files MODIFY value decimal(10,2) NULL DEFAULT NULL;');

        DB::statement('ALTER TABLE injury_invoices MODIFY invoice_nr varchar(100) NULL DEFAULT NULL;');
        DB::statement('ALTER TABLE injury_invoices MODIFY invoice_date date NULL DEFAULT NULL;');
        DB::statement('ALTER TABLE injury_invoices MODIFY payment_date date NULL DEFAULT NULL;');
        DB::statement('ALTER TABLE injury_invoices MODIFY netto decimal(10,2) NULL DEFAULT 0;');
        DB::statement('ALTER TABLE injury_invoices MODIFY vat decimal(10,2) NULL DEFAULT 0;');
        DB::statement('ALTER TABLE injury_invoices MODIFY commission integer NULL DEFAULT 0;');
        DB::statement('ALTER TABLE injury_invoices MODIFY base_netto decimal(10,2) NULL DEFAULT 0;');

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
