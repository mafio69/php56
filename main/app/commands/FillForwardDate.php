<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class FillForwardDate extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'debug:fill-forward-date';

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
        InjuryHistory::where('history_type_id', 208)->with('injury')->orderBy('id')->chunk(200, function($histories){
            foreach($histories as $history)
            {
                $injury = $history->injury;

                $invoice = InjuryInvoices::where('injury_id', $injury->id)
                                            ->where('injury_invoice_status_id', 1)
                                            ->whereNull('forward_date')->first();

                if($invoice){
                    $invoice->update(['forward_date' => $history->created_at]);
                }
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
