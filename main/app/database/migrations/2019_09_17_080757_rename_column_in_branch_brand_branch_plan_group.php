<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameColumnInBranchBrandBranchPlanGroup extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('branch_brand_branch_plan_group', function(Blueprint $table){
		    $table->dropColumn('branch_program_group_id');
		    $table->unsignedInteger('branch_plan_group_id')->index()->after('branch_brand_id');
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
