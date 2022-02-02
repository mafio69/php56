<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ImportTempInjuries extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'import:temp-injuries';

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
	    $file = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/imports/injuries/2018-11-22_raport_szkod_getin2.xlsx';
        $reader = Excel::load($file);
        $worksheet = $reader->getSheet(0);
        $highest_row = $worksheet->getHighestRow();
        $highest_column = $worksheet->getHighestColumn();
        $rowData = $worksheet->rangeToArray('A2:' . $highest_column.$highest_row ,
            NULL,
            TRUE,
            FALSE);
        foreach($rowData as $row)
        {
            $notifier = explode(' ', $row[15]);
            $injuries_type_id = '';
            if($row[6] == 'AUTO-CASCO'){
                $injuries_type_id = 1;
            }elseif($row[6] == 'OC SPRAWCY'){
                $injuries_type_id = 2;
            }elseif($row[6] == 'AC/REGRES'){
                $injuries_type_id = 4;
            }
            MobileInjury::create([
                'registration' => $row[3] ? $row[3] : '',
                'nr_contract' => $row[20] ? $row[20] : '',
                'notifier_surname' => isset($notifier[1]) ? $notifier[1] : '',
                'notifier_name' => isset($notifier[0]) ? $notifier[0] : '',
                'injuries_type_id' => $injuries_type_id,
                'marka' => $row[2] ? $row[2] : '',
                'model' => $row[2] ? $row[2] : '',
                'name_zu' => $row[7] ? $row[7] : '',
                'date_event' => date('Y-m-d',PHPExcel_Shared_Date::ExcelToPHP($row[1])),
                'event_city' => $row[4] ? $row[4] : '',
                'nr_injurie' => $row[9] ? $row[9] : '',
                'desc_event' => $row[5].'; '.$row[10].'; nr polisy:'.$row[17].'; nr polisy sprawcy: '.$row[18],
                'company' => $row[12].' '.$row[13],
                'active' => 0,
                'source' =>0,
                'name_zu' => '',
                'if_on_as_server' => 0,
                'created_at' => date('Y-m-d H:i:s',PHPExcel_Shared_Date::ExcelToPHP($row[1]))
            ]);
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
