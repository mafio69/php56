<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class FixInjuryInvoicesBranch extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'fix:injury-invoices-branch';

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
		$invoices = InjuryInvoices::where('branch_id', 0)->has('companyVatCheck')->with('companyVatCheck', 'companyVatCheck.company')->get();

		foreach ($invoices as $invoice) {
            $company_branches = count($invoice->companyVatCheck->company->branches);
            if($company_branches == 1) {
                $current_branch = $invoice->branch_id;
                $invoice->branch_id = $invoice->companyVatCheck->company->branches()->first()->id;
                $branches_list = implode($invoice->companyVatCheck->company->branches()->lists('id'));
                Log::info("Serwis: {$invoice->companyVatCheck->company->id}, oddział:{$branches_list}: Faktura id {$invoice->id}: Zmiana branch_id:{$current_branch} => {$invoice->branch_id}.");
                $invoice->save();
            }
		}
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
