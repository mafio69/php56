<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ImportInvoiceCommission extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'commission:import';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Import brakujących prowizji z faktur.';

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
		InjuryInvoices::where('commission', 1)->with('injury.branch')->whereActive(0)->chunk(100, function($invoices){
			foreach ($invoices as $invoice)
			{
				$commission = new Commission;
				$commission->injury_invoice_id = $invoice->id;
				$commission->company_id = ($invoice->injury->branch) ? $invoice->injury->branch->company_id : null;
				$commission->commission_step_id = 1;
				$commission->invoice_date = $invoice->invoice_date;
				$commission->created_at = $invoice->created_at;
				$commission->save();
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
