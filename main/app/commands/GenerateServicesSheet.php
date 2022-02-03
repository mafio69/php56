<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class GenerateServicesSheet extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'generate:services-sheet';

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
        $handle = fopen(\Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER').'/temp/garages_report.csv', 'w');
        Branch::where('active', '=', '0')->chunk(1000, function($garages) use(&$handle){
            $garages->load('typegarages', 'voivodeship');
            foreach($garages as $garage)
            {
                if($garage->company->groups->contains(1) ||  $garage->company->groups->contains(5) ) {
                    fputcsv($handle, array(
                        $garage->id,
                        $garage->short_name,
                        $garage->city,
                        ($garage->voivodeship) ? $garage->voivodeship->name : '',
                        $garage->street,
                        $garage->code,
                        $garage->phone,
                        implode(',', $garage->brands()->where('typ', 1)->lists('name')),
                        implode(',', $garage->brands()->where('typ', 2)->lists('name')),
                        str_replace('|', ',', $garage->emails),
                        str_replace(array("\r\n", "\n\r", "\n", "\r", "|"), ',', $garage->remarks)
                    ), '|');
                }
            }
        });

        fclose($handle);
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
