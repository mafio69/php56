<?php
namespace Idea\Reports\InjuriesReports;

use Config;
use Excel;
use Idea\Reports\BaseReport;
use Idea\Reports\ReportsCsvInterface;
use Idea\Reports\ReportsInterface;
use Injury;
use InjuryFiles;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OriginalParametersReport extends BaseReport implements ReportsInterface, ReportsCsvInterface{

    private $params;
    private $filename;

    function __construct($filename, $params = array())
    {
        $this->params = $params;
        $this->filename = $filename;
    }

    public function generateReport()
    {
        set_time_limit(5000);
        \DB::disableQueryLog();

        return new StreamedResponse(function() {
            // Open output stream
            $handle = fopen('php://output', 'w');

            fputcsv($handle, $this->generateTheads());

            $date_from = $this->parseDate($this->params['date_from'], '0');
            $date_to = $this->parseDate($this->params['date_to'], '+1');

            Injury::where('active', '=', '0')
                ->where('step' , '!=' , '-10')
                ->whereBetween('created_at', array($date_from, $date_to))
                ->chunk(100, function($injuries) use($handle) {
                    $injuries->load( 'vehicle' , 'repairInformation');

                    foreach ($injuries as $k => $injury) {
                        $il_repair_info = $this->ilRepairInfo($injury);

                        $row = array(
                            $injury->vehicle->registration,
                            ($injury->vehicle_type == 'Vehicles') ? $injury->vehicle->VIN : $injury->vehicle->vin,
                            $injury->case_nr,
                            $injury->injury_nr,
                            ($injury->reported_ic != 1) ? 'NIE' : 'TAK',
                            ($injury->if_il_repair == 1) ? 'TAK' : ( ($injury->if_il_repair == 0) ?  'NIE' : 'NIE USTALONO' ),
                            $il_repair_info
                        );

                        fputcsv($handle, $row);
                    }
                });

            // Close the output stream
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$this->filename.'.csv"',
        ]);
    }

    public function generateTheads()
    {
       return array(
            'Numer Rejestracyjny',
            'VIN',
            'Numer Szkody wewnętrzny',
            'Numer Szkody Towarzystwa',
            'Zgłoszona do TU',
            'Naprawa w sieci IL',
            'Przyczyna naprawy poza siecią IL',
        );
    }



    private function ilRepairInfo($injury){
     $il_repair_info=$injury->repairInformation;
      if($il_repair_info){
        $info= $il_repair_info->name;
        if($injury->il_repair_info_description)
          $info.= '- '.$injury->il_repair_info_description;
        return $info;
      }
      return '---';
    }
}
