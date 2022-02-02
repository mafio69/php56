<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Create extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('branch_brands', function (Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('branch_id')->index();
            $table->unsignedInteger('brand_id')->index();
            $table->boolean('authorization')->default(0);
            $table->timestamps();
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
