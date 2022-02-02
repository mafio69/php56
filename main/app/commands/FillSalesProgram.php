<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class FillSalesProgram extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'system:fill-sales-program';

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
        $ids = Vehicles::whereNotNull('syjon_contract_id')->whereNull('syjon_program_id')->lists('id');
        $this->info(count($ids));
        Vehicles::whereIn('id', $ids)->chunk(200, function($vehicles){
            foreach($vehicles as $vehicle)
            {
                $syjonService = new \Idea\SyjonService\SyjonService();
                $syjon_contract = json_decode( $syjonService->loadContract($vehicle->syjon_contract_id) )->data;
                if( $syjon_contract){
                    $vehicle->update([
                        'syjon_program_id' => $syjon_contract->program_id
                    ]);
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
