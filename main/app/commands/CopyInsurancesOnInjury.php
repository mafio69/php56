<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CopyInsurancesOnInjury extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'system:copy-insurances-on-injury';

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

        $progress->start($this->getOutput(), Injury::count());


        Injury::latest()->chunk(200, function($injuries) use (&$progress)
        {
            $injuries->load('vehicle');
            foreach($injuries as $injury) {
                $this->process($injury);
                $progress->advance(1);
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

    private function process($injury)
    {
        if(!$injury->injury_policy_id && $injury->vehicle_id > 0) {
            $vehicle = $injury->vehicle;
            $injury->insurance_company_id = $vehicle->insurance_company_id;
            $injuryPolicy = InjuryPolicy::create([
                'insurance_company_id' => $vehicle->policy_insurance_company_id,
                'expire' => $vehicle->expire,
                'nr_policy' => $vehicle->nr_policy,
                'insurance' => $vehicle->insurance,
                'contribution' => $vehicle->contribution,
                'netto_brutto' => $vehicle->netto_brutto,
                'assistance' => $vehicle->assistance,
                'assistance_name' => $vehicle->assistance_name,
                'risks' => $vehicle->risks,
                'gap' => $vehicle->gap,
                'legal_protection' => $vehicle->legal_protection
            ]);
            $injury->injury_policy_id = $injuryPolicy->id;

            $injury->save();
        }
    }

}
