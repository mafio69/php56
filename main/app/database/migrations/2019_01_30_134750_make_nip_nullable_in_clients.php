<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class MakeNipNullableInClients extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        DB::statement('ALTER TABLE clients MODIFY NIP varchar(255) NULL DEFAULT NULL;');
        DB::statement('ALTER TABLE clients MODIFY phone varchar(255) NULL DEFAULT NULL;');
        DB::statement('ALTER TABLE leasing_agreements MODIFY withdraw_reason TEXT NULL DEFAULT NULL;');
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
	}

}
