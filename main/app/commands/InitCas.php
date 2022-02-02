<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Fetch\Server;

class InitCas extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'init:cas';

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
		$settings = json_decode(file_get_contents(base_path('app/config/constants.json')), true);
		$settings['cas'] =  date('Y-m-d H:i:s');
		file_put_contents(base_path('app/config/constants.json'), json_encode($settings));

		$step = InjurySteps::find(11);
		$step->update(['name' => 'w obsłudze CAS']);

		$step = InjurySteps::find(14);
		$step->update(['name' => 'do rozliczenia CAS']);

		$step = InjurySteps::find(21);
		$step->update(['name' => 'rozliczona CAS']);

		$step = InjurySteps::find(22);
		$step->update(['name' => 'odmowa ZU CAS']);
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
