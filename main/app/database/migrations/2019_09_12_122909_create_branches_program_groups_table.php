<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBranchesProgramGroupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('branch_program_group', function (Blueprint $table){
		    $table->increments('id');
		    $table->unsignedInteger('branch_id')->nullable()->index();
		    $table->unsignedInteger('plan_group_id')->nullable()->index();
		    $table->timestamps();
		    $table->softDeletes();
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
