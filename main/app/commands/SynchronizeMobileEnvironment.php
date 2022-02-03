<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class SynchronizeMobileEnvironment extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'system:synchronize';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Synchronizacja środowiska aplikacji ze środowiskiem do komunikacji z aplikacją mobilną.';

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
        $synchronizer = new \Idea\Synchronization\Synchronizer();
        $mobile_injuries_imported = $synchronizer->injuries();
        $this->info('mobile injuries imported: '. $mobile_injuries_imported);
        $synchronizer->companies();
        $synchronizer->branches();
    }

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array();
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array();
	}

}
