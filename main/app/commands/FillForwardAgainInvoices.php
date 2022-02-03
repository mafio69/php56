<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class FillForwardAgainInvoices extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'system:fill-forward-again-invoices';

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
		$invoices = InjuryInvoices::where('injury_invoice_status_id',3)->get();
		foreach ($invoices as $invoice) {
			$history = InjuryHistory::where('injury_id', $invoice->injury_id)->where('history_type_id', 208)->first();
			if($history) {
				$invoice->forward_again_date = $invoice->forward_date;
				$invoice->forward_date = $history->created_at;
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
