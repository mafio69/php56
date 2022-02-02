<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class UpdateMantisVehicles extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'mantis:update';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Aktualizacja pojazdów mantis.';

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
        $filename = $this->option('filename');

        $importer = new \Idea\Vmanage\Imports\ImportGetin($filename);
        $rows = $importer->loadTsv();
        $this->info('file loaded');
        foreach($rows as $k => $row) {
            $importer->parseRow($row);
            $this->info('row '.(++$k).' parsed');
        }
        $this->info('file parsed');

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
		return array(
			array('filename', null, InputOption::VALUE_OPTIONAL, 'Nazwa pliku'),
		);
	}

}
