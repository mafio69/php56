<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ImportTest extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'import:test';

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

    protected $rows_per_parsing = 5;
    private $header;

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
        $filename = 'AS_20181119.csv';
        $import_id = 157;
        $import = \VmanageImport::find($import_id);

        $path = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/imports/vmanage/';
        $file = $path.$filename;

        $rows = array();
        $header = NULL;
        if (($handle = fopen($file, 'r')) !== FALSE)
        {
            \VmanageVehicle::where('if_truck', 0)->whereIn('vmanage_company_id', [9, 12])->where('outdated', 0)->update(['deleting_file' => $filename]);
            \VmanageVehicle::where('if_truck', 0)->whereIn('vmanage_company_id', [9, 12])->where('outdated', 0)->delete();

            $lp = 0;
            $k = 1;
            while(($row = fgetcsv($handle,0,chr(9)))!==FALSE){
                if(isset($row[0]) && $row[0] && mb_strtoupper($row[0]) == 'LP.') {
                    $this->header = $row;
                }
                if(isset($row[0]) && $row[0] && mb_strtoupper($row[0]) != 'LP.' && $k > 1) {
                    if(!isset($row[1])) {
                        \Log::info('err '.$k, $row);
                    }else {
                        $lp++;
                        $rows[] = $this->explodeTsvRow($row);
                        if ($lp == $this->rows_per_parsing) {
                            $start = microtime(true);
                            Queue::push('Idea\Vmanage\Imports\ImportTruckPartial', array('rows' => $rows, 'import_id' => null));
                            $time_elapsed_secs = (microtime(true) - $start) ;
                            dd($time_elapsed_secs);
                            $lp = 0;
                            $rows = [];
                        }
                    }
                }
                $k++;
            }

            fclose($handle);

            Queue::push('Idea\Vmanage\Imports\ImportTruckPartial', array('rows' => $rows, 'import_id' => $import_id));
        }
	}

    private function explodeTsvRow($row)
    {
        $explodedRow = [
            'lp'    =>  $row[0],
            'registration'  =>  $row[1],
            'vin'   =>  $row[2],
            'brand' =>  $row[3],
            'model' =>  $row[4],
            'pojemnosc_silnika' =>  $row[5],
            'moc_silnika'   =>  $row[6],
            'jednostka_mocy'    =>  $row[7],
            'rok_produkcji' =>  $row[8],
            'typ_nadwozia'  =>  $row[9],
            'data_konca_polisy' =>  $row[10],
            'nazwa_TU'  =>  $row[11],
            'wlasciciel_pojazdu'    => $row[12],
            'sprzedawca'    =>  $row[13],
            'dane_sprzedawcy'   =>  $row[14],
            'dealer_forda'  =>  $row[15],

            'klient' => null,
            'klient_adres' => null,
            'assistance' => null,
            'data_rejestracji' => null,
        ];

        if($this->header[16] == '')
        {
            $explodedRow['data_zawarcia_UL']  =  $row[17];
            $explodedRow['NIP_dostawcy']  =  $row[18];
            $explodedRow['nr_umowy']  =  isset($row[19]) ? $row[19] : null;
            $explodedRow['nr_polisy'] = isset($row[20]) ? $row[20] : null;
        }else{
            $explodedRow['data_zawarcia_UL']  =  $row[16];
            $explodedRow['NIP_dostawcy']  =  $row[17];
            $explodedRow['nr_umowy']  =  isset($row[18]) ? $row[18] : null;
            $explodedRow['nr_polisy'] = isset($row[19]) ? $row[19] : null;
        }

        $explodedRow = array_map('trim' , $explodedRow);

        foreach ($explodedRow as $k => $item) {
            $explodedRow[$k] = iconv('WINDOWS-1250', 'utf-8', $item);
        }

        return $explodedRow;
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
