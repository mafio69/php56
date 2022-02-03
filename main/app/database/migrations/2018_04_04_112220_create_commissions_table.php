<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCommissionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('commissions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->unsignedInteger('injury_invoice_id')->nullable();
			$table->unsignedInteger('commission_individual_report_id')->nullable();
			$table->unsignedInteger('commission_group_report_id')->nullable();
			$table->unsignedInteger('commission_step_id')->nullable();
			$table->date('invoice_date')->nullable();
			$table->decimal('commission')->nullable();
			$table->text('omission_reason')->nullable();
			$table->string('omission_attachment')->nullable();

			$table->timestamps();
		});

		Schema::create('commission_reports', function (Blueprint $table){
			$table->increments('id');
			$table->unsignedInteger('user_id');

			$table->string('report_number')->nullable();

			$table->boolean('is_trial')->default(0);
			$table->boolean('is_individual')->default(0);

			$table->softDeletes();
			$table->timestamps();
		});

		Schema::create('commission_steps', function (Blueprint $table){
			$table->increments('id');
			$table->string('name');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('commission_steps');
		Schema::drop('commission_reports');
		Schema::drop('commissions');
	}

}
