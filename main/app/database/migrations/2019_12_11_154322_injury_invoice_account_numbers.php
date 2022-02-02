<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InjuryInvoiceAccountNumbers extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('injury_invoices_account_numbers', function (Blueprint $table) {
            $table->integer('injury_invoices_id')->unsigned()->index();
            $table->foreign('injury_invoices_id')->references('id')->on('injury_invoices');
            $table->integer('company_account_numbers_id')->unsigned()->index();
            $table->foreign('company_account_numbers_id', 'company_account_id')->references('id')->on('company_account_numbers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('injury_invoices_account_numbers');
    }

}
