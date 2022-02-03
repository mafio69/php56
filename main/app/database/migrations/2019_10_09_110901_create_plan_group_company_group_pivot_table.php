<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlanGroupCompanyGroupPivotTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('plan_group_company_group', function(Blueprint $table)
		{
			$table->increments('id');
			$table->unsignedInteger('plan_group_id')->index();
            $table->unsignedInteger('company_group_id')->index();
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
		Schema::drop('plan_group_company_group');
	}

}
