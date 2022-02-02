<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddIsRootToRolesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('roles', function (Blueprint $table) {
            $table->boolean('is_root')->default(0)->after('name');
        });

        Schema::table('permissions', function (Blueprint $table) {
            $table->boolean('is_root')->default(0)->after('display_name');
        });
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('roles', function(Blueprint $table)
		{
			
		});
	}

}
