<?php

use Idea\SyjonService\SyjonService;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class SyncSyjonDictionary extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'system:sync-syjon-dictionary';

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
        $syjonService = new SyjonService();
        $syjonService->syncDictionaryTotalStatuses();
        $syjonService->syncDictionaryTheftStatuses();
        $syjonService->syncDictionaryStages();
        $syjonService->syncDictionaryStatuses();
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
