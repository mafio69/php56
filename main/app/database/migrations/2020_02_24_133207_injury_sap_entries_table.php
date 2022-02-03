<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InjurySapEntriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('injury_sap_entities', function (Blueprint $table){
		    $table->increments('id');
		    $table->unsignedInteger('injury_id');
            $table->string('szkodaId', 10)->nullable()->index();
            $table->string('rokum', 4)->nullable();
            $table->string('nrum', 7)->nullable();
            $table->string('symbol', 20)->nullable();
            $table->string('nrrej', 10)->nullable();
            $table->string('nrpolisy', 20)->nullable();
            $table->string('nrpolisyZew', 20)->nullable();
            $table->date('dataszkody')->nullable();
            $table->string('rodzub', 3)->nullable();
            $table->string('rodzszk', 3)->nullable();
            $table->string('stanszk', 1)->nullable();
            $table->string('towub', 5)->nullable();
            $table->decimal('kwota', 12,2)->nullable();
            $table->decimal('kwotaOdsz', 12 ,2)->nullable();
            $table->decimal('kwotawypl', 12 ,2)->nullable();
            $table->date('datawypl')->nullable();
            $table->string('odmowa', 1)->nullable();
            $table->string('uwagi', 50)->nullable();
            $table->string('odbWarsz', 1)->nullable();
            $table->string('odbLb', 1)->nullable();
            $table->string('odbGl', 1)->nullable();
            $table->string('odbInny', 1)->nullable();
            $table->string('inne', 150)->nullable();
            $table->decimal('kosztH', 12, 2)->nullable();
            $table->decimal('kosztP', 12, 2)->nullable();
            $table->decimal('kosztI', 12, 2)->nullable();
            $table->decimal('kwPotrRat',12 ,2)->nullable();
            $table->decimal('kwPotrInn', 12, 2)->nullable();
            $table->decimal('kwPozost', 12, 2)->nullable();
            $table->string('mPostoju')->nullable();
            $table->date('datWazSprzWra')->nullable();
            $table->string('towlikw', 6)->nullable();
            $table->string('idWarsz', 10)->nullable();
            $table->string('field1', 20)->nullable();
            $table->string('field2', 40)->nullable();
            $table->string('field3', 40)->nullable();
            $table->string('field4', 100)->nullable();
            $table->timestamps();
        });

		$injury_ids = Injury::whereNotNull('sap_id')->lists('id');

		Injury::whereIn('id', $injury_ids)->chunk(100, function ($injuries){
            foreach($injuries as $injury)
            {
                $sap = InjurySapEntity::create([
                    'injury_id' => $injury->id,
                    'szkodaId' => $injury->sap_id
                ]);
                $sap->created_at = $injury->sap_date;
                $sap->save(['timestamps' => false]);
            }
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
