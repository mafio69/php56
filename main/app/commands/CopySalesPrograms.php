<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CopySalesPrograms extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'system:copy-sales-programs';

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
        //sales_program
        VmanageVehicle::whereNotNull('sales_program')->chunk(200, function ($vehicles){
            foreach($vehicles as $vehicle)
            {
                $dlsProgram = \DlsProgram::where('name', 'like', $vehicle->sales_program)->first();
                if(! $dlsProgram){
                    $dlsProgram = DlsProgram::create(['name' => $vehicle->sales_program]);
                }

                $vehicle->update(['dls_program_id' => $dlsProgram->id]);
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
