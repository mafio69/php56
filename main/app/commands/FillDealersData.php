<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class FillDealersData extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'system:fill-dealers-data';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Fill Db with dealers data';

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
		$ids = Vehicles::whereNotNull('syjon_contract_id')->whereNull('seller_id')->lists('id');
		$this->info(count($ids));
		$counter = 0;
        Vehicles::whereIn('id', $ids)->chunk(200, function($vehicles) use ($syjonService, & $counter) {
            foreach($vehicles as $vehicle)
            {
				try{
					$syjon_vehicle = json_decode( $syjonService->loadVehicle($vehicle->syjon_vehicle_id, $vehicle->syjon_contract_id))->data;
					if($syjon_vehicle){
						if(!is_null($syjon_vehicle->nip_dost) && !is_null($syjon_vehicle->name_dost)) {
							$this->info(++$counter.' '.$syjon_vehicle->name_dost.' '.$syjon_vehicle->nip_dost);
							$seller = VehicleSellers::where('nip', $syjon_vehicle->nip_dost)->where('name', $syjon_vehicle->name_dost)->first();
							if(is_null($seller)) {
								$seller = VehicleSellers::create(array('nip' => $syjon_vehicle->nip_dost, 'name' => $syjon_vehicle->name_dost));
							}
							$vehicle->seller_id = $seller->id;
							} else {
								++$counter;
								$vehicle->seller_id = null;
							}
						$vehicle->save();
					} else {
						$this->info(++$counter);
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
