<?php

namespace Idea\Vmanage\Imports;


use Config;
use Queue;

class QueueImportGetin
{
    protected $rows_per_parsing = 1000;

    public function fire($job, $data)
    {
        $filename = $data['filename'];
        $import_id = $data['import_id'];

        $path = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/imports/vmanage/';
        $file = $path.$filename;
        if(file_exists($file)) {
            $rows = array();
            $header = NULL;
            if (($handle = fopen($file, 'r')) !== FALSE)
            {
                fgetcsv($handle, 0,chr(9));

                \VmanageVehicle::where('if_truck', 1)->whereIn('vmanage_company_id', [9, 12])->where('outdated', 0)->update(['deleting_file' => $filename]);
                \VmanageVehicle::where('if_truck', 1)->whereIn('vmanage_company_id', [9, 12])->where('outdated', 0)->delete();

                $lp = 0;
                while(($row = fgetcsv($handle,0,chr(9)))!==FALSE){
                    $lp ++;
                    $rows[] = $this->explodeTsvRow($row);

                    if($lp == $this->rows_per_parsing)
                    {
                        Queue::push('Idea\Vmanage\Imports\ImportGetinPartial', array('rows' => $rows, 'import_id' => null));
                        $lp = 0;
                        $rows = [];
                    }
                }
                fclose($handle);

                Queue::push('Idea\Vmanage\Imports\ImportGetinPartial', array('rows' => $rows, 'import_id' => $import_id));
            }
        }

        $job->delete();
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
            'data_zawarcia_UL'  =>  $row[16],
            'NIP_dostawcy'  =>  $row[17],
            'nr_umowy' => $row[18],
            'nr_polisy' => $row[19],
//            'stan_um' => $row[20],
//            'data_wydania' => $row[21],
//            'prze_data' => $row[22]
        ];

        $explodedRow = array_map('trim' , $explodedRow);
        foreach ($explodedRow as $k => $item)
        {
            $explodedRow[$k] = iconv('WINDOWS-1250','utf-8', $item);
        }

        return $explodedRow;
    }
}
