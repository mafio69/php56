<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class TestSynchronization extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'system:synchronize-test';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Testowa synchronizacja środowiska aplikacji ze środowiskiem do komunikacji z aplikacją mobilną.';

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
        $synchronizer->test();
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
