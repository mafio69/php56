<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCompanyCommisionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('companies', function(Blueprint $table){
			$table->unsignedInteger('billing_cycle_id')->nullable()->after('id');
			$table->unsignedInteger('commission_type_id')->nullable()->after('id');
		});

		Schema::create('company_commissions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->unsignedInteger('company_id')->nullable();
			$table->unsignedInteger('brand_id')->nullable();
			$table->unsignedInteger('min_value')->nullable();
			$table->unsignedInteger('min_amount')->nullable();
			$table->decimal('commission')->nullable();
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
		Schema::drop('company_commissions');
		Schema::table('companies', function(Blueprint $table){
			$table->dropColumn('commission_type_id');
			$table->dropColumn('billing_cycle_id');
		});
	}

}
