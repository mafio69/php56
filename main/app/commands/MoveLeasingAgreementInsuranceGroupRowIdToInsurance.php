<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class MoveLeasingAgreementInsuranceGroupRowIdToInsurance extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'system:fill-insurance-group-row-id';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		LeasingAgreementInsurance::chunk(200, function ($insurances){
		    $insurances->load('leasingAgreement');
		    foreach($insurances as $insurance)
            {
                $insurance->leasing_agreement_insurance_group_row_id = $insurance->leasingAgreement->leasing_agreement_insurance_group_row_id;
                $insurance->save();
            }
        });
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
		);
	}

}
