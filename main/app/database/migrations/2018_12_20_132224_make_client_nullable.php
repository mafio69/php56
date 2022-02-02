<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class MakeClientNullable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('clients', function(Blueprint $table)
		{
            $table->string('registry_post')->nullable()->change();
            $table->string('registry_city')->nullable()->change();
            $table->string('registry_street')->nullable()->change();
            $table->string('correspond_post')->nullable()->change();
            $table->string('correspond_city')->nullable()->change();
            $table->string('correspond_street')->nullable()->change();
            $table->string('phone')->nullable()->change();
		});
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
