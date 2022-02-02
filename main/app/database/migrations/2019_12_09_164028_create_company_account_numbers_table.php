<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyAccountNumbersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_account_numbers', function ($table) {

            $table->increments('id')->unsigned();
            $table->integer('company_id')->unsigned();
            $table->string('account_number', 50);
            $table->foreign('company_id')->references('id')->on('companies');
            $table->timestamps();
        }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('company_account_numbers');
    }

}
