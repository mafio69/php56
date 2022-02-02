<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBranchAuthorizationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('branch_authorizations', function($table)
        {
            $table->increments('id')->unsigned();
            $table->integer('branch_id')->unsigned()->nullable()->index();
            $table->integer('brand_id')->unsigned()->nullable()->index();
//            $table->foreign('branch_id')->references('id')->on('branches');
//            $table->foreign('brand_id')->references('id')->on('brands');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('branch_authorizations');
	}

}
