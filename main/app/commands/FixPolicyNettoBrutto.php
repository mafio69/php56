<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class FixPolicyNettoBrutto extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'system:fix-policy-netto-brutto';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Naprawa typu kwoty ubezpieczenia';

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
        InjuryPolicy::whereHas('injury', function($query){
            $query->where('vehicle_type', 'Vehicles')->whereHas('vehicleFromVehicle', function ($query){
                $query->whereNotNull('syjon_policy_id')->where('syjon_policy_id', '!=', 0);
            });
        })->chunk(100, function($policies){
            $policies->load('injury.vehicle');
            $syjonService = new \Idea\SyjonService\SyjonService();
            foreach($policies as $policy)
            {
                $syjonPolicy = json_decode($syjonService->loadPolicy($policy->injury->vehicle->syjon_policy_id))->data;
                if(! $syjonPolicy){
                    dd($policy->id);
                }
                $policy_type = $syjonPolicy->policy_type_price;
                if($policy_type == 'Netto') $netto_brutto = 1;
                elseif($policy_type == 'Brutto') $netto_brutto = 2;
                elseif($policy_type == 'Netto50') $netto_brutto = 3;
                else $netto_brutto = 0;

                if($netto_brutto != $policy->netto_brutto)
                {
                    $policy->update(['netto_brutto' => $netto_brutto]);
                    $this->info($policy->injury->id.' -> '.$netto_brutto);
                }
            }
        });
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
