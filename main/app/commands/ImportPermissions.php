<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ImportPermissions extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'debug:import-permissions';

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
        $filepath = storage_path('imports/syjon_uprawnienia.xlsx');

        \Excel::load($filepath, function($reader) {
            foreach ($reader->getWorksheetIterator() as $worksheet) {
                $sheet = $worksheet->toArray();

                $module_id = null;
                foreach ($sheet as $k => $row) {
                    if($k > 0) {
                        $module_id = $this->createModule($row[0]);
                        $this->createPermission($row, $module_id);
                    }
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

    private function createModule($module_name)
    {
        $module  = Module::where('name', 'like', $module_name)->first();
        if($module) return $module->id;

        $module = Module::create(['name' => $module_name]);

        return $module->id;
    }

    private function createPermission($row, $module_id)
    {
        $path  = $row[1];
        $path = explode('/', $path);
        $path = trim(implode('/', $path));

        Permission::create([
            'name' => $row[2],
            'short_name' => $this->generateShortName($path, $row[2]),
            'path' => $path,
            'module_id' => $module_id
        ]);
    }

    private function generateShortName($path, $name)
    {
        $short_name = $path.'#'.$name;

        $short_name = trim(mb_strtolower($short_name));
        $short_name = str_replace([' / ', '/'], '#', $short_name);
        $short_name = str_replace(' ', '_', $short_name);
        $short_name = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $short_name);
        $short_name = preg_replace('/_+/', '_', $short_name);
        $short_name = str_replace('\'', '', $short_name);

        return $short_name;
    }

}
