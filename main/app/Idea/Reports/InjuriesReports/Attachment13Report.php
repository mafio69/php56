<?php
namespace Idea\Reports\InjuriesReports;


use Carbon\Carbon;
use Excel;
use Idea\Reports\BaseReport;
use Idea\Reports\ReportsInterface;
use Injury;
use InjuryFiles;
use PHPExcel_Style_NumberFormat;

class Attachment13Report extends BaseReport implements ReportsInterface
{
    private $params;
    private $filename;

    function __construct($filename, $params = array())
    {
        $this->params = $params;
        $this->filename = $filename;
    }

    public function generateReport()
    {

        set_time_limit(500);
        \DB::disableQueryLog();

        $template = \Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/templates/zal-nr-13-raport-szkod-czesciowych.xlsx';

        Excel::load($template, function($excel)  {

            $excel->setActiveSheetIndex(0);
            $sheet = $excel->getActiveSheet();

            $row = 2;

            $date_from = $this->parseDate($this->params['date_from'], '0');
            $date_to = $this->parseDate($this->params['date_to'], '+1');

            $injuries = Injury::where('active', '=', '0')->whereBetween('created_at', array($date_from, $date_to))
                ->whereIn('step', ['0', '10', '13', '15', '16', '17', '18', '19', '20'])
                ->with('user', 'vehicle', 'vehicle.owner', 'vehicle.insurance_company', 'client', 'receive', 'compensations', 'injuries_type', 'status', 'chat', 'chat.messages')->get();

            $filesA = InjuryFiles::whereActive(0)->whereType(3)->whereIn('injury_id', $injuries->lists('id'))->where(function($query){
                $query->where('category', 6)->orWhere('category', 49)->orWhere('category', 60);
            })->get();

            $filesInjuryA = array();
            foreach ($filesA as $file) {
                if(!isset($filesInjuryA[$file->injury_id]))
                    $filesInjuryA[$file->injury_id] = $file;
            }
	        $limit_date_01022018 = Carbon::createFromFormat('Y-m-d', '2018-02-01');

            ob_clean();
            foreach ($injuries as $injury) {

                $compensation = $this->findCompensation($injury);
                $dateCompensation = $this->findDateCompensation($injury);
                $days = $this->calcDays($injury);

                $rowsToInsert = array(
                    ($injury->client_id == 0) ? '---' : $injury->client->name,
                    $injury->vehicle->registration,
                    $injury->vehicle->nr_contract,
                    $injury->vehicle->owner->name,
	                $injury->vehicle->owner->old_nip,
                    $injury->created_at->format('Y-m-d H:i'),
                    $injury->date_event,
                    $injury->injuries_type->name,
                    ($injury->vehicle->insurance_company_id != 0) ? $injury->vehicle->insurance_company()->first()->name : '---',
                    $injury->injury_nr,
                    ($injury->receive_id == 0) ? '' : $injury->receive->name,
                    (isset($filesInjuryA[$injury->id])) ? 'tak' : 'nie',
                    $compensation,
                    $dateCompensation,
                    $this->getLastActionDate($injury),
                    $injury->date_end,
                    $days,
                    $injury->status->name,
                    $injury->user->name
                );
                $sheet->fromArray($rowsToInsert, null, 'A' . $row);
                $row++;

                unset($compensation);
                unset($dateCompensation);
                unset($days);
            }
            $sheet->getStyle('P1:P'.$row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

        })->setFileName($this->filename)->export('xls');
    }

    private function findDateCompensation($injury)
    {
        $date = '';
        foreach ($injury->compensations as $compensation) {
            $date = $compensation->date_decision;
        }

        return $date;
    }

    private function findCompensation($injury)
    {
        $sumCompensation = 0;
        foreach ($injury->compensations as $compensation) {
            if(!is_null($compensation->compensation))
                if($compensation->injury_compensation_decision_type_id == 7)
                    $compensation->compensation = abs($compensation->compensation) * -1;
            $sumCompensation+=$compensation->compensation;
        }

        return $sumCompensation;
    }

    private function calcDays($injury)
    {
        if($injury->date_end)
        {
            $date_end = Carbon::createFromFormat('Y-m-d H:i:s', $injury->date_end);
            $days = $injury->created_at->diffInDays($date_end);
            if($days == 0)
                $days = '0';
            return $days;
        }
        return '';
    }




}