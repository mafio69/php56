<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class FixMultipleVmanageVehicle extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'vmanage:fix-multiple';

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
		$entities = VmanageVehicle::select(DB::raw('count(*) as ct'), 'vin')->where('outdated', 0)->groupBy('vin')->having('ct', '>', 1)->whereNotNull('vin')->where('vin', '!=', '')->get();

		$this->info($entities->count());

		foreach($entities as $entity)
        {
            $vmanage_vehicles = VmanageVehicle::where('vin', $entity->vin)->where('outdated', 0)->orderBy('id', 'desc')->get();
            foreach ($vmanage_vehicles as $k => $vehicle)
            {
                if($k > 0)
                {
                    $vehicle->update(['outdated' => 1]);
                }
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
