<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class UpdateGapData extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'command:update-gap-data';

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
		$injuries = Injury::whereHas('vehicleFromVehicle', function($q){
			$q->whereIn('gap', [1,2]);
		})->whereHas('injuryPolicy', function($q){
			$q->where('gap', 0);
		})->get();
		foreach($injuries as $injury){
			$injury->injuryPolicy->update(['gap' => $injury->vehicleFromVehicle->gap]);
		}

		$injuries = Injury::whereHas('vehicleFromVehicle', function($q){
			$q->whereIn('legal_protection', [1,2]);
		})->whereHas('injuryPolicy', function($q){
			$q->where('legal_protection', 0);
		})->get();
		foreach($injuries as $injury){
			$injury->injuryPolicy->update(['legal_protection' => $injury->vehicleFromVehicle->legal_protection]);
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
