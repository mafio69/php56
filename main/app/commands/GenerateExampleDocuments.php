<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class GenerateExampleDocuments extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'debug:generate-example-documents';

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
	    $documentTypes = InjuryDocumentType::where('active', 0)->get();

	    $ownerGroups = OwnersGroup::whereIn('id', Owners::lists('owners_group_id'))->get();

	    foreach($ownerGroups as $ownerGroup) {
	        $injury = Injury::vehicleOwnerColumn('owners_group_id', $ownerGroup->id)->where('branch_id', '>', 0)->where('client_id', '>', 0)->where('active', 0)->first();
	        if(!$injury){
               $injury = Injury::vehicleOwnerColumn('owners_group_id', $ownerGroup->id)->where('active', 0)->where('client_id', '>', 0)->first();
            }

	        if(! $injury) dd( $ownerGroup);
            foreach ($documentTypes as $documentType) {
                $doc = new Idea\DocGenerator\DocGenerator($injury->id, 'Injury', $documentType->id);
                $doc->generatePreview($documentType->name);
            }
        }
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
