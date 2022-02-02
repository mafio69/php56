<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class SystemUpdateStatuses extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'system:update-statuses';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Aktualizacja spraw pod nowe statusy.';

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
        // z 'zakończona' na 'zakończona wypłatą'

        // z 'obsługa' na 'zakończona - wystawiono upoważnienie'

        // z 'odmowa zakładu ubezpieczeń' na 'zakończona odmową TU'
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
