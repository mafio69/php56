<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CopyInsurancesOnVmanageVehicles extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'system:copy-insurance-on-vmanage-vehicles';

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
        $progress = $this->getHelperSet()->get('progress');
        $progress->start($this->getOutput(), Injury::where('vehicle_type', 'VmanageVehicle')->count());

		Injury::where('vehicle_type', 'VmanageVehicle')->chunk(100, function ($injuries) use($progress){
		    $injuries->load('vehicleFromVmanageVehicle', 'injuryPolicy');
		    foreach($injuries as $injury)
            {
                $vehicle = $injury->vehicleFromVmanageVehicle;
                $policy = $injury->injuryPolicy;

                if($policy) {
                    $vehicle->update([
                        'policy_insurance_company_id' => $policy->insurance_company_id,
                        'insurance_company_id' => $injury->insurance_company_id,
                        'expire' => $policy->expire,
                        'nr_policy' => $policy->nr_policy,
                        'insurance' => $policy->insurance,
                        'contribution' => $policy->contribution,
                        'netto_brutto' => $policy->netto_brutto,
                        'assistance' => $policy->assistance,
                        'assistance_name' => $policy->assistance_name,
                        'risks' => $policy->risks,
                        'gap' => $policy->gap,
                        'legal_protection' => $policy->legal_protection
                    ]);
                }else{
                    $vehicle->update([
                        'insurance_company_id' => $injury->insurance_company_id
                    ]);
                }

                $progress->advance();
            }
        });

        $progress->finish();

        $progress = $this->getHelperSet()->get('progress');
        $progress->start($this->getOutput(), Injury::where('vehicle_type', 'Vehicles')->count());

        Injury::where('vehicle_type', 'Vehicles')->chunk(100, function ($injuries) use($progress){
            $injuries->load('vehicleFromVehicle', 'injuryPolicy');
            foreach($injuries as $injury)
            {
                $vehicle = $injury->vehicleFromVehicle;
                $policy = $injury->injuryPolicy;

                if($policy) {
                    $vehicle->update([
                        'policy_insurance_company_id' => $policy->insurance_company_id,
                        'insurance_company_id' => $injury->insurance_company_id,
                        'expire' => $policy->expire,
                        'nr_policy' => $policy->nr_policy,
                        'insurance' => $policy->insurance,
                        'contribution' => $policy->contribution,
                        'netto_brutto' => $policy->netto_brutto,
                        'assistance' => $policy->assistance,
                        'assistance_name' => $policy->assistance_name,
                        'risks' => $policy->risks,
                        'gap' => $policy->gap,
                        'legal_protection' => $policy->legal_protection
                    ]);
                }else{
                    if($vehicle) {
                        $vehicle->update([
                            'insurance_company_id' => $injury->insurance_company_id
                        ]);
                    }else{
                        $this->info($injury->id);
                    }
                }

                $progress->advance();
            }
        });

        $progress->finish();
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
