<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class SapSzkodaPobierz extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'sap:pobierz';

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
        $sap_id = $this->argument('sap_id');

        $sap = new \Idea\SapService\Sap();

        dump($sap->szkodaPobierz($sap_id));
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
            array('sap_id', InputArgument::OPTIONAL, 'SAP ID', 'H')
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
