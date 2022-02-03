<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateBranchesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('branches', function(Blueprint $table){
            $table->time('open_time')->after('suspended')->nullable();
            $table->time('close_time')->after('open_time')->nullable();
            $table->string('contact_people')->after('phone')->nullable();
            $table->string('priorities')->after('priority')->nullable();
            $table->string('tug_remarks')->after('tug24h')->nullable();
            $table->string('delivery_cars')->after('tug_remarks')->nullable();
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('branches', function (Blueprint $table){
            $table->dropColumn('open_time');
            $table->dropColumn('close_time');
            $table->dropColumn('contact_people');
            $table->dropColumn('priorities');
            $table->dropColumn('tug_remarks');
            $table->dropColumn('delivery_cars');
        });
	}

}
