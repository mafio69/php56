<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInsuranceCompanyIdToInjuriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
	    Schema::create('injury_policies', function(Blueprint $table){
	        $table->increments('id');
	        $table->unsignedInteger('insurance_company_id')->nullable()->index();
            $table->date('expire')->nullable();
            $table->string('nr_policy')->nullable();
            $table->unsignedInteger('insurance')->nullable();
            $table->unsignedInteger('contribution')->nullable();
            $table->smallInteger('netto_brutto')->default(1)->nullable();
            $table->smallInteger('assistance')->nullable();
            $table->string('assistance_name')->nullable();
            $table->string('risks')->nullable();
            $table->smallInteger('gap')->default(0)->nullable();
            $table->smallInteger('legal_protection')->default(0)->nullable();
            $table->timestamps();
        });

		Schema::table('injury', function(Blueprint $table)
		{
			$table->unsignedInteger('insurance_company_id')->nullable()->index()->after('original_branch_id');
            $table->unsignedInteger('injury_policy_id')->nullable()->index()->after('insurance_company_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('injury', function(Blueprint $table)
		{
			//
		});
	}
}
