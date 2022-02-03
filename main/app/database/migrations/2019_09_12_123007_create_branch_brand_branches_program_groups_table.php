<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBranchBrandBranchesProgramGroupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('branch_brand_branch_program_group', function (Blueprint $table){
		    $table->increments('id');
		    $table->unsignedInteger('branch_brand_id')->nullable()->index();
		    $table->unsignedInteger('branch_program_group_id')->nullable()->index();
		    $table->boolean('if_sold')->default(0);
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
