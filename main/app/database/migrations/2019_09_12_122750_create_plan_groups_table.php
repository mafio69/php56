<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlanGroupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('plan_groups', function (Blueprint $table){
		    $table->increments('id');
		    $table->unsignedInteger('plan_id')->nullable()->index();
		    $table->string('name')->nullable();
		    $table->unsignedInteger('ordering')->nullable();
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
		Schema::drop('plan_groups');
	}

}
