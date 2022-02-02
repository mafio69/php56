<?php
namespace Idea\Gap\NewAgreement;

use Excel;
use Config;
use File;
use Idea\Gap\AgreementDocumentParser;
use Idea\Gap\AgreementDocumentParserInterface;
use Idea\Gap\BaseAgreementDocumentParser;
use Log;
use PHPExcel_Shared_Date;
use Carbon\Carbon;

class NewAgreementDocumentParser extends BaseAgreementDocumentParser implements AgreementDocumentParserInterface{

    private $slice;

    private $headers = [];

    private $patern;

    function __construct($filename)
    {
        $path = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/gap/new/';
        $this->file = $path.$filename;
        $this->filename = $filename;

        if(!File::exists($path)) {
            File::makeDirectory($path,511, true);
        }
    }

    public function getDefaultParsePatern(){
      return [
        'A'=>['code'=>'agreement_number','name'=>'Numer umowy','type'=>'varchar'],
        'C'=>['code'=>'group','name'=>'GAP','type'=>'varchar'],
        'F'=>['code'=>'pesel_regon','name'=>'REGON/PESEL','type'=>'varchar'],
        'L'=>['code'=>'object_vin','name'=>'VIN','type'=>'varchar'],
        'R'=>['code'=>'object_type','name'=>'Rodzaj mienia','type'=>'varchar'],
        'P'=>['code'=>'object_group','name'=>'Klasyfikacja','type'=>'varchar'],
        'Q'=>['code'=>'object_name','name'=>'Nazwa przedmiotu leasingu','type'=>'varchar'],
        'U'=>['code'=>'gross_net','name'=>'Netto/brutto GAP','type'=>'varchar'],
        'V'=>['code'=>'type','name'=>'Typ GAP','type'=>'varchar'],
        'W'=>['code'=>'contribution','name'=>'Składka GAP','type'=>'float'],
        'X'=>['code'=>'time','name'=>'Okres GAP','type'=>'int'],
        'Z'=>['code'=>'object_price','name'=>'Przedmioty cena jedn PLN','type'=>'float'],
        'AB'=>['code'=>'object_currency','name'=>'Przedmiot waluta','type'=>'varchar'],
        'AF'=>['code'=>'activation_date','name'=>'Data aktywacji umowy','type'=>'date'],
        'AG'=>['code'=>'accept_date','name'=>'Data akceptacji umowy','type'=>'date'],
      ];
    }

    public function setPatern($patern = null){
      if($patern)
        $this->patern = $patern;
      else
        $this->patern = $this->getDefaultParsePatern();
    }

    public function load()
    {
        set_time_limit(500);

        if(file_exists($this->file)) {
            if ($reader = Excel::load($this->file, 'windows-1250')) {
                $objWorksheet = $reader->getActiveSheet();

                //$this->preCheckFileStructure($objWorksheet);

                $maxCell = $objWorksheet->getHighestRowAndColumn();
                $data = $objWorksheet->rangeToArray('A1:' . $maxCell['column'] . $maxCell['row'],
                    NULL,
                    TRUE,
                    TRUE,
                    TRUE);
                $data = array_map('array_filter', $data);
                $this->rows = array_filter($data);
                return true;
              //  return $this->checkFileStructure();
            }
        }

        return $this->parseFailed("Błąd odczytu pliku, skontaktuj się z administratorem.");
    }

    public function parse_rows($test = false)
    {
        $new_agreement = array();
        $parsedRows = array();
        $agreementParser = new AgreementParser();
        $patern = $this->patern;
        $limit = null;
        if($test){
          $limit = 3;
        }
        foreach($this->rows as $k => $row)
        {
          if($limit&&$k>$limit){
            break;
          }
          if($test){
            foreach($row as $col_key => $item){
              if(isset($patern[$col_key])){
                $parsedRows[$col_key]['val'][$k] = $item;
                $parsedRows[$col_key]['code'] = $patern[$col_key]['code'];
                $parsedRows[$col_key]['name'] = $patern[$col_key]['name'];
                if($k==1){
                  if(strtolower($patern[$col_key]['name'])!=strtolower($item))
                    $parsedRows[$col_key]['danger'] = true;
                }
              }
              else{
                $parsedRows[$col_key]['val'][$k] = $item;
              }
            }
          }
          else{
            if($k>1){
              $parsedRow = array();
              foreach($patern as $col_key => $item){
                if(isset($row[$col_key])){
                  $value = $row[$col_key];
                  if($item['type']=='varchar'){
                    $parsedRow[$item['code']] = trim($value);
                  }
                  elseif($item['type']=='float'){
                    $parsedRow[$item['code']] = $this->floatValue($value);
                  }
                  elseif($item['type']=='date'){
                    try{
                      $value = Carbon::createFromFormat('m-d-y',$value)->format('Y-m-d');
                    }catch(\Exception $e){

                    }
                    $parsedRow[$item['code']] = $value;
                  }
                  else{
                    $parsedRow[$item['code']] = $value;
                  }

                  switch($item['code']){
                    case 'agreement_number':
                      $parsedRow['type_import'] = $agreementParser->checkAgreement($parsedRow[$item['code']]);
                      break;
                    case 'pesel_regon':
                      if(strlen($parsedRow[$item['code']])==11){
                        $parsedRow['pesel']=$parsedRow[$item['code']];
                      }
                      else{
                        $parsedRow['regon']=$parsedRow[$item['code']];
                      }
                      break;
                    case 'group':
                      $parsedRow['group_id'] = $agreementParser->checkGroup($parsedRow[$item['code']]);
                      break;
                    case 'type':
                      $parsedRow['type_id'] = $agreementParser->checkType($parsedRow[$item['code']]);
                      break;
                    case 'type':
                      $parsedRow['type_id'] = $agreementParser->checkType($parsedRow[$item['code']]);
                      break;
                    case 'object_type':
                      $parsedRow['object_type_id'] = $agreementParser->checkObjectType($parsedRow[$item['code']]);
                      break;
                    case 'object_group':
                      $parsedRow['object_group_id'] = $agreementParser->checkObjectGroup($parsedRow[$item['code']],$parsedRow['object_type_id']);
                      break;
                    case 'gross_net':
                      if(strtolower($parsedRow[$item['code']])=='netto'){
                        $parsedRow['gross_net']=1;
                      }
                      else{
                        $parsedRow['gross_net']=0;
                      }
                      break;
                    default:
                      break;
                  }
                }
              }
              $parsedRows[$parsedRow['type_import']][] = $parsedRow;
            }
          }

        }

        if($test){
          return $parsedRows;
        }
        //return $parsedRows;

        return $this->convertToHtml($parsedRows);
    }

    private function parseExcelDate($date){
        $date = PHPExcel_Shared_Date::ExcelToPHP($date);
        return date('Y-m-d',(int) $date);
    }

    private function simplifyValue($value)
    {
        $value = trim($value);
        $value = str_replace(',', '.', $value);
        $value = floatval($value);
        return $value;
    }

    private function floatValue($value)
    {
        $value = str_replace(",",".",$value);
        $value = preg_replace('/\.(?=.*\.)/', '', $value);
        return floatval($value);
    }

    private function convertToHtml($parsedRows)
    {
        $converted = array();
        if(isset($parsedRows['new']))
        {
            $converted['new'][] = '<tr><th>Lp.</th><th>'.implode('</th><th>',array_pluck($this->patern,'name')).'</th></tr>';
            foreach($parsedRows['new'] as $k => $row)
            {
                $htmlRow = '<tr>';
                $htmlRow .= '<td>'.++$k;
                $htmlRow .= $this->serializeHtmlRow($row, $k);
                $htmlRow .= '.</td>';
                foreach($this->patern as $item){
                  if($item['type']=='float')
                    $htmlRow .= '<td>'.number_format($row[$item['code']],2,"."," ").' zł</td>';
                  else
                    $htmlRow .= '<td>'.$row[$item['code']].'</td>';
                }
                $htmlRow .= '</tr>';
                $converted['new'][] = $htmlRow;
            }
        }

        if(isset($parsedRows['exist']))
        {
          $converted['exist'][] = '<tr><th>Lp.</th><th>'.implode('</th><th>',array_pluck($this->patern,'name')).'</th></tr>';
          foreach($parsedRows['exist'] as $k => $row)
          {
              $htmlRow = '<tr>';
              $htmlRow .= '<td>'.++$k;
              $htmlRow .= '.</td>';
              foreach($this->patern as $item){
                if($item['type']=='float')
                  $htmlRow .= '<td>'.number_format($row[$item['code']],2,"."," ").' zł</td>';
                else
                  $htmlRow .= '<td>'.$row[$item['code']].'</td>';
              }
              $htmlRow .= '</tr>';
              $converted['exist'][] = $htmlRow;
          }
        }

        return $converted;
    }

    private function serializeHtmlRow($row, $lp)
    {
        $serialized = '';

        foreach($row as $key => $value){
          if(strpos($key, 'object') !== false){
            $key = str_replace('object_','',$key);
            if(in_array($key, [
              'name',
              'vin',
              'group_id',
              'type_id',
              'price',
              'type_id',
              'currency',
            ]))
            $serialized.= '<input name="agreements['.$lp.'][object]['.$key.']" type="hidden" value="'.htmlspecialchars($value).'"/>';
          }
          else{
            if(in_array($key, [
          		'agreement_number',
          		'group_id',
          		'pesel',
          		'regon',
          		'gross_net',
          		'type_id',
          	  'contribution',
          		'time',
          		'activation_date',
          		'accept_date',
          		'status_id',
          	]))
              $serialized.= '<input name="agreements['.$lp.']['.$key.']" type="hidden" value="'.htmlspecialchars($value).'"/>';
          }

        }

        return $serialized;
    }


}
