<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInjuriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('clients', function($table){
			$table->increments('id')->unsigned();
			$table->integer('parent_id')->unsigned();
			$table->string('name');
			$table->string('NIP');
			$table->string('REGON');
			$table->string('registry_post');
			$table->string('registry_city');
			$table->string('registry_street');
			$table->string('correspond_post');
			$table->string('correspond_city');
			$table->string('correspond_street');
			$table->string('phone');
			$table->string('email');
			$table->integer('active')->default(0);
			$table->timestamps();
		});

		Schema::create('drivers', function($table){
			$table->increments('id')->unsigned();
			$table->integer('clients_id')->unsigned();
			$table->string('surname');
			$table->string('name');
			$table->string('phone');
			$table->string('email');
			$table->string('city');
			$table->timestamps();
			$table->foreign('clients_id')->references('id')->on('clients');
		});

		Schema::create('insurance_companies', function($table){
			$table->increments('id')->unsigned();
			$table->string('name')->unique();
			$table->integer('active')->default(0);
			$table->timestamps();			
		});



		Schema::table('vehicles', function($table)
		{
		    $table->date('end_leasing')->after('mileage');
		    $table->integer('insurance_companies_id')->unsigned()->after('mileage');
		    $table->date('expire')->after('mileage');
		    $table->integer('contribution')->after('expire');
		    $table->integer('assistance')->after('contribution');
		    $table->string('assistance_name')->after('assistance');
		    $table->integer('insurance')->after('assistance_name');
		    $table->string('nr_policy')->after('insurance');
		    $table->integer('active')->default(0)->after('nr_policy');
		    $table->foreign('insurance_companies_id')->references('id')->on('insurance_companies');
		});

		Schema::create('injuries_type', function($table)
		{
			$table->increments('id')->unsigned();
			$table->string('name');
		});

		Schema::create('text_contents', function($table){
			$table->increments('id')->unsigned();
			$table->text('content');
		});

		Schema::create('receives', function($table){
			$table->increments('id')->unsigned();
			$table->string('name');			
		});

		Schema::create('injuries', function($table)
        {
        	$table->increments('id')->unsigned();
        	$table->integer('users_id')->unsigned();
        	$table->integer('vehicles_id')->unsigned();
        	$table->integer('clients_id')->unsigned();
        	$table->integer('drivers_id')->unsigned();
        	$table->string('notifier_surname');
        	$table->string('notifier_name');
        	$table->string('notifier_phone');
        	$table->string('notifier_city');
        	$table->integer('injuries_type_id')->unsigned();
        	$table->integer('info')->unsigned()->default(0);
        	$table->integer('remarks')->unsigned()->default(0);
        	$table->integer('police')->default(0);
        	$table->date('date_event');
        	$table->string('event_post');
        	$table->string('event_city');
        	$table->string('event_street');
        	$table->integer('if_map')->default(0);
        	$table->integer('if_map_correct')->default(0);
        	$table->float('lat');
        	$table->float('lng');
        	$table->integer('receives_id')->unsigned();
        	$table->string('status');
        	$table->integer('active')->default(0);
        	$table->timestamps();
        	$table->foreign('users_id')->references('id')->on('users'); 
        	$table->foreign('vehicles_id')->references('id')->on('vehicles'); 
        	$table->foreign('clients_id')->references('id')->on('clients'); 
        	$table->foreign('drivers_id')->references('id')->on('drivers'); 
        	$table->foreign('injuries_type_id')->references('id')->on('injuries_type'); 
        	$table->foreign('info')->references('id')->on('text_contents'); 
        	$table->foreign('remarks')->references('id')->on('text_contents'); 
        	$table->foreign('receives_id')->references('id')->on('receives'); 

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
