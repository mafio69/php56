<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSapPremiumsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('injury_sap_premiums', function(Blueprint $table)
        {
            $table->increments('id');
            $table->unsignedInteger('injury_id')->nullable()->index();
            $table->unsignedInteger('injury_compensation_id')->nullable()->index();
            $table->string('nrRaty')->nullable()->index();
            $table->date('dataDpl')->nullable();
            $table->decimal('kwDpl', 10, 2)->nullable();
            $table->string('unameRej')->nullable();
            $table->date('dataRej')->nullable();
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
