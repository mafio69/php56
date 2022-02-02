<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class FillTotalStatusSourceInInjury extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'system:fill_injury_total_status_source';

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
		//tylko jeśli dane w kolumnie wynullowane
		$counter = 0;
		$injury_thefts = InjuryTheft::groupBy('injury_id')->chunk(1000, function ($injury_thefts) use (& $counter){
			foreach($injury_thefts as $injury_theft) {
				$injury_theft->injury->total_status_source = 1;
				$injury_theft->injury->save();
				$counter++;
			}
		});
		$this->info($counter);
		$counter = 0;
		$injury_wrecks = InjuryWreck::groupBy('injury_id')->chunk(1000, function ($injury_wrecks) use (& $counter){
			foreach($injury_wrecks as $injury_wreck) {
				if($injury_wreck->injury->total_status_source == 1) $injury_wreck->injury->total_status_source = null;
				else $injury_wreck->injury->total_status_source = 0;
				$injury_wreck->injury->save();
				$counter++;
			}
		});
		$this->info($counter);
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
