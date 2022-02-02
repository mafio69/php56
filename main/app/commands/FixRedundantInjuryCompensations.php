<?php

use Carbon\Carbon;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Helper\ProgressBar;

class FixRedundantInjuryCompensations extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'command:fix-injury-compensations';

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
		// $progress = $this->getHelperSet()->get('progress');
		// $progress->start($this->getOutput(), 5);
		// $i = 0;
		// while($i++ < 5){
		// 	sleep(1);
		// 	$progress->advance();
		// }
		// $progress->finish();

		Injury::chunk(1000, function($injuries){
			$injuries->load('documents', 'compensations');
			foreach($injuries as $injury){
				$noDelete = 0;
				foreach($injury->documents as $document){
					if($document->category == '6' || $document->category == '37'){
						$noDelete = 1;
						break;
					}
				}
				if(!$noDelete){
					foreach($injury->compensations as $compensation){
						$compensation->delete();
					}
				}
			}
		});

		$injuryCompensations = InjuryCompensation::whereHas('injury_file', function($q){
			$q->where('active', 9);
		})->get();
		foreach($injuryCompensations as $compensation){
			$compensation->delete();
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
