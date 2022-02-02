<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInjuryBranchesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('injury_branches', function(Blueprint $table){
		    $table->increments('id');
		    $table->unsignedInteger('injury_id')->nullable()->index();
		    $table->unsignedInteger('branch_id')->nullable()->index();
		    $table->unsignedInteger('user_id')->nullable()->index();
		    $table->softDeletes();
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
