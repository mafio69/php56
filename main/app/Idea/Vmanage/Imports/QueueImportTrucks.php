<?php

namespace Idea\Vmanage\Imports;


use Idea\SpreadsheetParser\SpreadsheetParser;
use Config;
use Queue;

class QueueImportTrucks
{
    protected $rows_per_parsing = 500;
    private $header;

    public function fire($job, $data)
    {
        $filename = $data['filename'];
        $import_id = $data['import_id'];
        $import = \VmanageImport::find($import_id);

        $path = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/imports/vmanage/';
        $file = $path.$filename;
        if(file_exists($file)) {
            switch ($import->file_type){
                case 1:
                    $this->importTcsv($file, $filename, $import_id);
                    break;
                case 2:
                    $this->importGetinCfm($file, $import_id);
                    break;
                case 3:
                    $this->impotGetinMobilny($file, $import_id);
                    break;
                case 4:
                    $this->importOpelLeasingMobilny($file, $import_id);
                    break;
                default:
                    \Log::alert('vmanage truck import error', [$import_id]);
                    break;
            }
        }

        $job->delete();
    }

    private function importTcsv($file, $filename, $import_id)
    {
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
                            Queue::push('Idea\Vmanage\Imports\ImportTruckPartial', array('rows' => $rows, 'import_id' => null));
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

    private function importGetinCfm($file, $import_id)
    {
        $workbook = SpreadsheetParser::open($file);
        $rows = array();

        foreach($workbook->getWorksheets() as $sheet) {
            $sheetIndex = $workbook->getWorksheetIndex($sheet);

            $lp = 0;
            foreach ($workbook->createRowIterator($sheetIndex) as $rowIndex => $row) {
                if ($rowIndex > 1) {
                    $lp++;
                    $rows[] = $this->explodeGetinCfmRow($row);

                    if ($lp == $this->rows_per_parsing) {
                        Queue::push('Idea\Vmanage\Imports\ImportTruckPartial', array('rows' => $rows, 'import_id' => null));
                        $lp = 0;
                        $rows = [];
                    }
                }
            }
            Queue::push('Idea\Vmanage\Imports\ImportTruckPartial', array('rows' => $rows, 'import_id' => $import_id));
            return;
        }
    }

    private function impotGetinMobilny($file, $import_id)
    {
        $workbook = SpreadsheetParser::open($file);
        $rows = array();

        foreach($workbook->getWorksheets() as $sheet) {
            $sheetIndex = $workbook->getWorksheetIndex($sheet);

            $lp = 0;
            foreach ($workbook->createRowIterator($sheetIndex) as $rowIndex => $row) {
                if ($rowIndex > 1) {
                    $lp++;
                    $rows[] = $this->explodeGetinMobilnyRow($row);

                    if ($lp == $this->rows_per_parsing) {
                        Queue::push('Idea\Vmanage\Imports\ImportTruckPartial', array('rows' => $rows, 'import_id' => null));
                        $lp = 0;
                        $rows = [];
                    }
                }
            }
            Queue::push('Idea\Vmanage\Imports\ImportTruckPartial', array('rows' => $rows, 'import_id' => $import_id));
            return;
        }
    }

    private function importOpelLeasingMobilny($file, $import_id)
    {
        $workbook = SpreadsheetParser::open($file);
        $rows = array();

        foreach($workbook->getWorksheets() as $sheet) {
            $sheetIndex = $workbook->getWorksheetIndex($sheet);

            $lp = 0;
            foreach ($workbook->createRowIterator($sheetIndex) as $rowIndex => $row) {
                if ($rowIndex > 1) {
                    $lp++;
                    $rows[] = $this->explodeOpelLeasingMobilnyRow($row);

                    if ($lp == $this->rows_per_parsing) {
                        Queue::push('Idea\Vmanage\Imports\ImportTruckPartial', array('rows' => $rows, 'import_id' => null));
                        $lp = 0;
                        $rows = [];
                    }
                }
            }
            Queue::push('Idea\Vmanage\Imports\ImportTruckPartial', array('rows' => $rows, 'import_id' => $import_id));
            return;
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
            $explodedRow[ 'stan_um'] =  isset($row[21]) ? $row[21] : null;
            $explodedRow['data_wydania'] =  isset($row[22]) ? $row[22] : null;
            $explodedRow['prze_data'] =  isset($row[23]) ? $row[23] : null;
        }else{
            $explodedRow['data_zawarcia_UL']  =  $row[16];
            $explodedRow['NIP_dostawcy']  =  $row[17];
            $explodedRow['nr_umowy']  =  isset($row[18]) ? $row[18] : null;
            $explodedRow['nr_polisy'] = isset($row[19]) ? $row[19] : null;
            $explodedRow['stan_um'] =  isset($row[20]) ? $row[20] : null;
            $explodedRow['data_wydania'] =  isset($row[21]) ? $row[21] : null;
            $explodedRow['prze_data'] =  isset($row[22]) ? $row[22] : null;
        }

        $explodedRow = array_map('trim' , $explodedRow);

        foreach ($explodedRow as $k => $item) {
            $explodedRow[$k] = iconv('WINDOWS-1250', 'utf-8', $item);
        }

        return $explodedRow;
    }

    private function explodeGetinCfmRow($row)
    {
        $explodedRow = [
            'lp'    =>  null,
            'registration'  =>  isset($row['H']) ? $row['H'] : null,
            'vin'   =>  isset($row['I']) ? $row['I'] : null,
            'brand' =>  isset($row['A']) ? $row['A'] : null,
            'model' =>  isset($row['B']) ? $row['B'] : null,
            'pojemnosc_silnika' =>  '',
            'moc_silnika'   =>  '',
            'jednostka_mocy'    =>  '',
            'rok_produkcji' =>  isset($row['M']) ? $row['M'] : null,
            'typ_nadwozia'  =>  '',
            'data_konca_polisy' =>  isset($row['G']) ? $this->date($row['G']) : null,
            'nazwa_TU'  =>  isset($row['N']) ? $row['N'] : null,
            'wlasciciel_pojazdu'    => isset($row['O']) ? $row['O'] : null,
            'sprzedawca'    =>  '',
            'dane_sprzedawcy'   =>  '',
            'dealer_forda'  =>  '',
            'data_zawarcia_UL'  =>  '',
            'NIP_dostawcy'  =>  '',

            'nr_umowy'  =>  '',
            'nr_polisy' => isset($row['D']) ? $row['D'] : null,

            'klient' => isset($row['T']) ? $row['T'] : null,
            'klient_adres' => isset($row['U']) ? $row['U'] : null,
            'assistance' => isset($row['K']) ? $row['K'] : null,
            'data_rejestracji' => isset($row['L']) ? $this->date($row['L']) : null,
            'atrybut' => isset($row['Z']) ? $row['Z'] : null,
        ];

        $explodedRow = array_map('trim' , $explodedRow);
        return $explodedRow;
    }

    private function explodeGetinMobilnyRow($row)
    {
        $explodedRow = [
            'lp'    =>  null,
            'registration'  =>  isset($row['H']) ? $row['H'] : null,
            'vin'   =>  isset($row['I']) ? $row['I'] : null,
            'brand' =>  isset($row['A']) ? $row['A'] : null,
            'model' =>  isset($row['B']) ? $row['B'] : null,
            'pojemnosc_silnika' =>  '',
            'moc_silnika'   =>  '',
            'jednostka_mocy'    =>  '',
            'rok_produkcji' =>  isset($row['M']) ? $row['M'] : null,
            'typ_nadwozia'  =>  '',
            'data_konca_polisy' =>  isset($row['G']) ? $this->date($row['G']) : null,
            'nazwa_TU'  =>  isset($row['N']) ? $row['N'] : null,
            'wlasciciel_pojazdu'    => isset($row['O']) ? $row['O'] : null,
            'sprzedawca'    =>  isset($row['U']) ? $row['U'] : null,
            'dane_sprzedawcy'   =>  '',
            'dealer_forda'  =>  '',
            'data_zawarcia_UL'  =>  '',
            'NIP_dostawcy'  =>  '',

            'nr_umowy'  =>  '',
            'nr_polisy' => isset($row['D']) ? $row['D'] : null,

            'klient' => isset($row['T']) ? $row['T'] : null,
            'klient_adres' => '',
            'assistance' => isset($row['K']) ? $row['K'] : null,
            'data_rejestracji' => isset($row['L']) ? $this->date($row['L']) : null,
            'atrybut' => isset($row['Y']) ? trim($row['Y']) : null,
        ];

        $explodedRow = array_map('trim' , $explodedRow);

        return $explodedRow;
    }

    private function explodeOpelLeasingMobilnyRow($row)
    {
        $explodedRow = [
            'lp'    =>  null,
            'registration'  =>  isset($row['H']) ? $row['H'] : null,
            'vin'   =>  isset($row['I']) ? $row['I'] : null,
            'brand' =>  isset($row['A']) ? $row['A'] : null,
            'model' =>  isset($row['B']) ? $row['B'] : null,
            'pojemnosc_silnika' =>  '',
            'moc_silnika'   =>  '',
            'jednostka_mocy'    =>  '',
            'rok_produkcji' =>  isset($row['M']) ? $row['M'] : null,
            'typ_nadwozia'  =>  '',
            'data_konca_polisy' =>  isset($row['G']) ? $this->date($row['G']) : null,
            'nazwa_TU'  =>  isset($row['N']) ? $row['N'] : null,
            'wlasciciel_pojazdu'    => isset($row['O']) ? $row['O'] : null,
            'sprzedawca'    =>  isset($row['U']) ? $row['U'] : null,
            'dane_sprzedawcy'   =>  '',
            'dealer_forda'  =>  '',
            'data_zawarcia_UL'  =>  '',
            'NIP_dostawcy'  =>  '',

            'nr_umowy'  =>  '',
            'nr_polisy' => isset($row['D']) ? $row['D'] : null,

            'klient' => isset($row['T']) ? $row['T'] : null,
            'klient_adres' => '',
            'assistance' => isset($row['K']) ? $row['K'] : null,
            'data_rejestracji' => isset($row['L']) ? $this->date($row['L']) : null,
            'atrybut' => isset($row['Y']) ? $row['Y'] : null,
        ];

        $explodedRow = array_map('trim' , $explodedRow);

        return $explodedRow;
    }

    private function date($cell)
    {
        if(! is_object($cell)){
            $cell = new \DateTime( $cell);
        }
        return $cell->format('Y-m-d');
    }
}
