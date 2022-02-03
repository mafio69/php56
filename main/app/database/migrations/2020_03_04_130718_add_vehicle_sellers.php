<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVehicleSellers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('vehicle_sellers', function(Blueprint $table){
		    $table->increments('id');
            $table->string("nip")->nullable();
            $table->string("name")->nullable();
			$table->timestamps();
			$table->index(['nip', 'name']);
        });

		Schema::table('vehicles', function(Blueprint $table){
			$table->integer('seller_id')->nullable()->after('parent_id')->default(null);
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
