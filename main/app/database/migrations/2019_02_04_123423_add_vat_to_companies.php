<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddVatToCompanies extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
	    Schema::create('company_vat_checks', function (Blueprint $table){
	        $table->increments('id');
	        $table->unsignedInteger('company_id')->nullable();
	        $table->string('status_code')->nullable();
	        $table->string('status')->nullable();
	        $table->timestamps();

        });

		Schema::table('companies', function(Blueprint $table)
		{
            $table->unsignedInteger('company_vat_check_id')->nullable()->after('id');
            $table->boolean('is_active_vat')->default(0)->after('id');


		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('companies', function(Blueprint $table)
		{

		});
	}

}
