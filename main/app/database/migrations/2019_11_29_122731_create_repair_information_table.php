<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRepairInformationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('repair_information', function (Blueprint $table){
		    $table->increments('id');
		    $table->string('name');
        });

		DB::table('repair_information')->insert([
            ['name'=>'brak serwisu, serwis za daleko'],
            ['name'=>'brak serwisu danej marki'],
            ['name'=>'zła jakość serwisu'],
            ['name'=>'nie jestem zainteresowany, mam własny serwis'],
            ['name'=>'inne'],
            ['name' => 'Klient sam wskazał'],
            ['name' => 'Brak dedykowanego serwisu'],
            ['name' => 'Pojazd znajduje się w serwisie']
        ]);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//il_repair_info
	}

}
