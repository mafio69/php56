<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class RemoveLeasingAgreements extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'system:remove-leasing-agreements';

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
		$agreements = LeasingAgreement::whereBetween('created_at', ['2019-12-01 00:00:51', '2019-12-31 12:03:51'])->get();

        $this->info('removing '.count($agreements));

		foreach($agreements as $agreement)
        {
            $agreement->insurances()->delete();
            $agreement->delete();
        }

		$this->info('done');
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
