<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class FixMultiAttachesVehicleToInjury extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'system:fix-multi-attaches-vehicle';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Wyprostowaniu gdzie jeden pojazd na wielu szkodach';

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
		$vehicles = Injury::selectRaw('count(*) as ct, vehicle_id, vehicle_type')->groupBy('vehicle_type')->groupBy('vehicle_id')->having('ct', '>', 1)->get()->toArray();

		foreach ($vehicles as $vehicle_data)
        {
            $injuries = Injury::where('vehicle_id', $vehicle_data['vehicle_id'])
                   ->where('vehicle_type', $vehicle_data['vehicle_type'])
                   ->orderBy('id')->get();

            $this->info($vehicle_data['vehicle_id'].' '.$vehicle_data['vehicle_type']);
            foreach($injuries as $k => $injury)
            {
                if($k > 0)
                {
                    if($injury->vehicle_type == 'Vehicles') {
                        $source_vehicle = $injury->vehicle->toArray();
                        $source_vehicle['parent_id'] = $source_vehicle['id'];
                        $vehicle = Vehicles::create($source_vehicle);
                        $injury->update(['vehicle_id' => $vehicle->id]);
                        $this->info($vehicle->id.' '.$injury->id);
                    }elseif($injury->vehicle_type == 'VmanageVehicle'){
                        $source_vehicle = $injury->vehicle;

                        $new_vehicle = VmanageVehicle::create($source_vehicle->toArray());

                        $source_vehicle->outdated = 1;
                        $source_vehicle->save();

                        $existing_history = VmanageVehicleHistory::where('vmanage_vehicle_id', $source_vehicle->id)->orWhere('previous_vmanage_vehicle_id', $source_vehicle->id)->first();

                        if($existing_history)
                        {
                            VmanageVehicleHistory::create([
                                'history_id' => $existing_history->history_id,
                                'vmanage_vehicle_id'    =>  $new_vehicle->id,
                                'previous_vmanage_vehicle_id'   => $source_vehicle->id
                            ]);
                        }else{
                            $highest_history = VmanageVehicleHistory::orderBy('history_id', 'desc')->first();
                            if($highest_history)
                            {
                                $history_id = $highest_history->history_id + 1;
                            }else{
                                $history_id = 1;
                            }

                            VmanageVehicleHistory::create([
                                'history_id' => $history_id,
                                'vmanage_vehicle_id'    =>  $new_vehicle->id,
                                'previous_vmanage_vehicle_id'   => $source_vehicle->id
                            ]);
                        }

                        $injury->update(['vehicle_id' => $new_vehicle->id]);
                        $this->info($new_vehicle->id.' '.$injury->id);
                    }
                }
            }
            $this->info('-----------');
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
