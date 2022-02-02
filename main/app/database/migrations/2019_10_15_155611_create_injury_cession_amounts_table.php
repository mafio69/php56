<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInjuryCessionAmountsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('injury_cession_amounts', function(Blueprint $table)
        {
            $table->increments('id');
            $table->unsignedInteger('injury_id')->nullable();
            $table->decimal('paid_amount', 10, 2)->nullable();
            $table->tinyInteger('net_gross')->nullable();
            $table->decimal('fv_amount', 10, 2)->nullable();
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
        Schema::drop('injury_cession_amounts');
    }

}
