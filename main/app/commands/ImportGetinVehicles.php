<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ImportGetinVehicles extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'getin:import';

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

        $import = VmanageImport::create(
            [
                'user_id'   =>  3,
                'filename'  =>  $filename
            ]);

        Queue::push('Idea\Vmanage\Imports\QueueImportGetin', array('filename' => $filename, 'import_id' => $import->id));
        $this->info('Trwa importowanie zestawnia...');
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
