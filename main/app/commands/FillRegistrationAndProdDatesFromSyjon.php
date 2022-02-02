<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class FillRegistrationAndProdDatesFromSyjon extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'fill:vehicles-production-and-registration-date';

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
		$syjonService = new \Idea\SyjonService\SyjonService();
		$ids = Vehicles::whereNotNull('syjon_vehicle_id')->whereNull('object_type')->lists('id');
		$this->info(count($ids));
		$counter = 0;
        Vehicles::whereIn('id', $ids)->chunk(200, function($vehicles) use ($syjonService, & $counter) {
            foreach($vehicles as $vehicle)
            {
				try{
					$response = json_decode( $syjonService->loadVehicle($vehicle->syjon_vehicle_id, $vehicle->syjon_contract_id));
					if(!$response) {
						$this->info(++$counter);
					} else {
						$syjon_vehicle = $response->data;
						if($syjon_vehicle){
							if(property_exists($syjon_vehicle, 'object_type_origin') && !is_null($syjon_vehicle->object_type_origin) ) {
								$this->info(++$counter.' '.$syjon_vehicle->id);
								if($syjon_vehicle->object_type_origin) $vehicle->object_type = $syjon_vehicle->object_type_origin;
							    $vehicle->save();
							}
						} else {
							$this->info(++$counter);
						}
					}
				} catch (ErrorException $e) {
					$this->info($e);
				}
			}
        });
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
