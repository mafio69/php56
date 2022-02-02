<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class TrimNipInBranches extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'system:trim-nip-branches';

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
		Branch::whereNotNull('nip')->where('nip', '!=', '')->chunk(500, function($branches){
		    foreach ($branches as $branch){
		        $branch->update(['nip' => preg_replace("/[^0-9]/", "", $branch->nip )]);
            }
        });

        Company::whereNotNull('nip')->where('nip', '!=', '')->chunk(500, function($companies){
            foreach ($companies as $company){
                $company->update(['nip' => preg_replace("/[^0-9]/", "", $company->nip )]);
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
