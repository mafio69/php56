<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEaInjuriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ea_injuries', function (Blueprint $table){
		    $table->increments('id');
            $table->unsignedInteger('injury_id')->nullable()->index();

            $table->unsignedInteger('vehicle_id')->nullable()->index();
            $table->unsignedInteger('vehicle_type')->nullable()->index();
            $table->unsignedInteger('workshop_id')->nullable()->index();

            $table->text('vehicle_vin')->nullable();
            $table->text('vehicle_registration')->nullable();
            $table->text('vehicle_brand')->nullable();
            $table->text('vehicle_model')->nullable();
            $table->text('vehicle_engine_capacity')->nullable();
            $table->text('vehicle_year_production')->nullable();
            $table->text('vehicle_first_registration')->nullable();
            $table->text('vehicle_mileage')->nullable();

            $table->text('owner_name')->nullable();
            $table->text('client_name')->nullable();

            $table->text('contract_number')->nullable();
            $table->text('contract_end_leasing')->nullable();
            $table->text('contract_status')->nullable();

            $table->text('insurance_company_name')->nullable();
            $table->text('insurance_expire_date')->nullable();
            $table->text('insurance_policy_number')->nullable();
            $table->text('insurance_amount')->nullable();
            $table->text('insurance_own_contribution')->nullable();
            $table->text('insurance_net_gross')->nullable();
            $table->text('insurance_assistance')->nullable();
            $table->text('insurance_assistance_name')->nullable();

            $table->text('driver_name')->nullable();
            $table->text('driver_surname')->nullable();
            $table->text('driver_phone')->nullable();
            $table->text('driver_email')->nullable();
            $table->text('driver_city')->nullable();

            $table->text('claimant_name')->nullable();
            $table->text('claimant_surname')->nullable();
            $table->text('claimant_phone')->nullable();
            $table->text('claimant_email')->nullable();
            $table->text('claimant_city')->nullable();

            $table->text('injury_event_date')->nullable();
            $table->text('injury_event_time')->nullable();
            $table->text('injury_event_city')->nullable();
            $table->text('injury_event_street')->nullable();
            $table->unsignedInteger('injury_type_incident_id')->nullable();
            $table->text('injury_event_description')->nullable();
            $table->text('injury_damage_description')->nullable();
            $table->text('injury_current_location')->nullable();
            $table->boolean('injury_reported_insurance_company')->nullable();
            $table->text('injury_type')->nullable();
            $table->text('injury_number')->nullable();
            $table->text('injury_insurance_company')->nullable();
            $table->tinyInteger('injury_police_notified')->nullable();
            $table->text('injury_police_number')->nullable();
            $table->text('injury_police_unit')->nullable();
            $table->text('injury_police_contact')->nullable();
            $table->boolean('injury_statement')->nullable();
            $table->boolean('injury_taken_registration')->nullable();
            $table->boolean('injury_towing')->nullable();
            $table->boolean('injury_replacement_vehicle')->nullable();
            $table->tinyInteger('injury_vehicle_in_service')->nullable();

            $table->text('case_number')->nullable();

            $table->softDeletes();
            $table->nullableTimestamps();
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ea_injuries');
	}

}
