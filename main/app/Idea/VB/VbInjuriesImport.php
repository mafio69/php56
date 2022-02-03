<?php
/**
 * Created by PhpStorm.
 * User: przemek
 * Date: 09.01.15
 * Time: 21:35
 */

namespace Idea\VB;


use Clients;
use Config;
use DateTime;
use Excel;
use Injury;
use InjuryChat;
use InjuryChatMessages;
use InjuryCompensation;
use InjuryStatusesHistory;
use Insurance_companies;
use PHPExcel_Shared_Date;
use Text_contents;
use Vehicles;

class VbInjuriesImport {

    private $file;
    private $worksheet;
    public $highestRow;
    public $highestColumn;
    public $parsedWorksheet;

    /**
     * @param filename - nazwa pliku do importu na serwerze w katalogu /uploads(-dev)/vb
     */
    function __construct($filename)
    {
        $this->file  = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/vb/'.$filename;
    }

    /**
     * Wczytuje arkusz do zmiennej $worksheet,
     * maksymalny nr kolumny do zmiennej $highestColumn,
     * a maksymalny numer wiersza do $highestColumn
     * @return bool
     */
    public function loadXLS()
    {
        if(file_exists($this->file)) {
            if ($reader = Excel::load($this->file)) {
                $this->worksheet = $sheet = $reader->getSheet(0);
                $this->highestRow = $sheet->getHighestRow();
                $this->highestColumn = $sheet->getHighestColumn();
                return true;
            }
            return false;
        }
        return false;
    }

    /**
     * @param $startingRow - wiersz od którego ma nastąpić parsowanie wcześniej zaczytanego arkusza (liczone od 1)
     * @param null $parsingHighestRow - wiersz na którym ma zakończyć parsowanie arkusza
     */
    public function parseWorksheet($startingRow, $parsingHighestRow = null)
    {
        $highestRow = (is_null($parsingHighestRow))?$this->highestRow:$parsingHighestRow;
        $actualRow = $startingRow;
        $step = 50;
        $upToRows = 0;
        $allLoops = 0;

        while($actualRow < $highestRow) {

            if( ($upToRows+$step) < $highestRow)
                $upToRows = $highestRow;
            else
                $upToRows+=$step;

            for ($actualRow ; $actualRow <= $upToRows; $actualRow++) {

                $rowData = $this->worksheet->rangeToArray('A' . $actualRow . ':' . $this->highestColumn . $actualRow,
                    NULL,
                    TRUE,
                    FALSE);
                $rowData = $rowData[0];
                $result[$allLoops++] = array(
                    'vehicle_data' => array(
                        'cfm' => $this->parseCfm($rowData[0]),
                        'nr_contract' => $this->unifyNr_contract($rowData[0], $rowData[1]),
                        'registration' => $this->parseRegistration($rowData[1]),
                        'nr_policy' => $rowData[14],
                        'insurance_company_name' => $rowData[15],
                        'vehicle_type' => $rowData[17], //unused
                    ),
                    'injury_data' => array(
                        'date_event' => $this->parseDate($rowData[2]),
                        'created_at' => $this->parseDate($rowData[3]),
                        'injuries_type_id' => $this->parseInjuries_type($rowData[5]), //todo
                        'injury_type' => $rowData[6],
                        'step' => $rowData[7],
                        'date_end' => $this->parseDate($rowData[8]),
                        'way_of_finished' => $rowData[9], //unused
                        'remarks' => $rowData[12],
                        'injury_nr' => $rowData[16]
                    ),
                    'chat' => array(
                        'description_liquidation' => $rowData[11],
                        'case_nr' => $rowData[13],
                        'archive' => $rowData[18],
                        'regres' => $rowData[19]
                    ),
                    'client' => $rowData[4],
                    'amount_of_compensation' => $this->parseNumber($rowData[10])
                );
            }
        }

        //wynik parsowania wpisany do zmiennej $parsedWorksheet
        $this->parsedWorksheet = $result;
    }

    /**
     * do testów pobranie wybranego sparsowanego wcześniej wiersza
     * @param $id
     * @return mixed
     */
    public function getRow($id)
    {
        return $this->parsedWorksheet[$id];
    }

    /**
     * przeprowadzenie importu wcześniej sparsowanych wybranych wierszy arkusza (znajdujących się w $parsedWorksheet)
     */
    public function import()
    {
        foreach($this->parsedWorksheet as $injury_row)
        {
            $vehicle = $this->parseVehicle($injury_row['vehicle_data'], $injury_row['client']);
            $vehicle_id = $vehicle->id;
            $client_id = $vehicle->client_id;

            $injury_id = $this->parseInjury($injury_row['injury_data'], $vehicle_id, $client_id);

            $this->parseChat($injury_row['chat'], $injury_id);

            $this->parseCompensation($injury_row['amount_of_compensation'], $injury_id);
        }

    }

    /**
     * poprawia nr umowy leasingowej z SŁUŻBOWY na SŁUŻBOWY/nr_rejestracyjny
     * @param $nr_contract
     * @param $registration
     * @return string - nr_contract po poprawieniu
     */
    private function unifyNr_contract($nr_contract, $registration){
        if($nr_contract == 'SŁUŻBOWY'){
            return $nr_contract.'/'.$this->parseRegistration($registration);
        }

        return $nr_contract;
    }

    /**
     * usuwa spacje z rejestracji
     * @param $registration
     * @return mixed
     */
    private function parseRegistration($registration){
        return str_replace(' ', '', $registration);
    }

    /**
     * Zamienia excelowską datę na datę formatu Y-m-d
     * @param $date
     * @return bool|string
     */
    private function parseDate($date)
    {
        $myDateTime = PHPExcel_Shared_Date::ExcelToPHP($date); // 1007596800 (Unix time)
        $myDateTime = date('Y-m-d', $myDateTime); // 2001-11-30 (formatted date)
        return $myDateTime;
    }

    /**
     * Sprawdza czy ustawić wskaźnik CFM dla samochodu
     * @param $nr_contract
     * @return int
     */
    private function parseCfm($nr_contract)
    {
        $tail = substr($nr_contract, -2);
        if( $tail == '/N' || $tail == '/T')
            return 1;

        $tail = substr($nr_contract, -3);
        if($tail == '/LL' || $tail == '/NK')
            return 1;

        if(substr($nr_contract, -4) == '/Lai')
            return 1;

        if($nr_contract == 'SŁUŻBOWY')
            return 1;

        return 0;
    }

    //todo
    /**
     * Sprawdza jakiego typu jest szkoda
     * @param $type
     * @return int - id typu z tabeli injuries_type
     */
    private function parseInjuries_type($type)
    {
        switch($type){
            case 'AC':
                return 3;
            case 'OC':
                return 6;
            case 'AC obce':
                return 1;
            case 'OC obce':
                return 2;
        }
        return 1;
    }

    /**
     * Zwraca kwotę w formacie x.x
     * @param $number
     * @return mixed
     */
    private function parseNumber($number)
    {
        return str_replace(',' , '.', $number);
    }

    /**
     * Sprawdza czy istnieje klient o danej nazwie w systemie, jeśli nie tworzy go i zwraca jego id
     * @param $client
     * @return client_id
     */
    private function parseClient($client)
    {
        $findClient = Clients::whereName($client)->get();
        if(!$findClient->isEmpty())
            return $findClient->first()->id;

        $createdClient = Clients::create(array(
            'name' => $client
        ));

        return $createdClient->id;
    }

    /**
     * Sprawdza czy istnieje dany samochód w bazie z takim samym nr_contract i registration,
     * jeśli nie tworzy go, Przy okazji sprawdza niezbędnie czy istnieje dane TU i Klient
     * @param $vehicle_data
     * @param $client
     * @return \Illuminate\Database\Eloquent\Model|mixed|null|static $vehicle - objekt utworzonego pojazdu
     */
    private function parseVehicle($vehicle_data, $client)
    {
        $findVehicle = Vehicles::where('nr_contract', '=', $vehicle_data['nr_contract'])->where('registration', '=', $vehicle_data['registration'])->orderBy('id', 'desc')->get();
        if(!$findVehicle->isEmpty())
            return $findVehicle->first();

        $client_id = $this->parseClient($client);

        $insurance_company = Insurance_companies::where('name', '=', $vehicle_data['insurance_company_name'])->get();
        if(!$insurance_company->isEmpty())
            $insurance_company =  $insurance_company->first();
        else {
            $insurance_company = Insurance_companies::create(array(
                'name' => $vehicle_data['insurance_company_name']
            ));
        }

        $vehicle = Vehicles::create(array(
            'registration' => $vehicle_data['registration'],
            'nr_contract' => $vehicle_data['nr_contract'],
            'nr_policy' => $vehicle_data['nr_policy'],
            'insurance_company_name' => $vehicle_data['insurance_company_name'],
            'insurance_company_id' => $insurance_company->id,
            'policy_insurance_company_id' => $insurance_company->id,
            'cfm' => $vehicle_data['cfm'],
            'client_id' => $client_id
        ));

        return $vehicle;
    }

    /**
     * Tworzy daną szkodę
     * @param $injury_data
     * @param $vehicle_id
     * @param $client_id
     * @return mixed $injury_id
     */
    private function parseInjury($injury_data, $vehicle_id, $client_id)
    {
        if( !is_null($injury_data['remarks']) && $injury_data['remarks'] != ''){
            $insert = Text_contents::create(array(
                'content' => $injury_data['remarks']
            ));

            $remarks_id = $insert->id;
        }else{
            $remarks_id = '0';
        }

        $last_injury = Injury::orderBy('id', 'desc')->limit('1')->get();
	    if( isCasActive() ) {
		    if (!$last_injury->isEmpty()) {
			    $case_nr = $last_injury->first()->case_nr;
			    if (strpos($case_nr, 'C') !== false) {
				    $case_nr = substr($case_nr, 0, -2);
			    }

			    if (substr($case_nr, -4) == date('Y')) {
				    $case_nr = intval(substr($case_nr, 0, -5));
				    $case_nr++;
				    $case_nr .= '/' . date('Y').'/C';
			    } else {
				    $case_nr = '1/' . date('Y').'/C';
			    }
		    } else {
			    $case_nr = '1/' . date('Y').'/C';
		    }
	    }else{
		    if (!$last_injury->isEmpty()) {
			    $case_nr = $last_injury->first()->case_nr;
			    if (substr($case_nr, -4) == date('Y')) {
				    $case_nr = intval(substr($case_nr, 0, -5));
				    $case_nr++;
				    $case_nr .= '/' . date('Y');
			    } else {
				    $case_nr = '1/' . date('Y');
			    }
		    } else {
			    $case_nr = '1/' . date('Y');
		    }
	    }

        $task_authorization = 0;
        $total_status_id = 0;
        switch($injury_data['step']){
            case 'Nowa':
                $step = 0;
                break;
            case 'Zgłoszona':
                $step = 10;
                $task_authorization = 1;
                break;
            case 'Zliwkidowana - zapłacona':
                $step = 17;
                break;
            case 'Zlikwidowana - odmówiona':
                $step = 20;
                break;
            default:
                $step = 0;
                break;
        }
        switch($injury_data['injury_type']){
            case 'Całkowita':
                // TODO DO USTALENIA STATUS ETAPU PRZY IMPORCIE
                $total_status_id = 11;
                if($injury_data['step'] == 'Zliwkidowana - zapłacona'){
                    $step = 34;
                }elseif($injury_data['step'] == 'Zlikwidowana - odmówiona'){
                    $step = 35;
                }else{
                    $step = 30;
                }
                break;
            case 'Kradzieżowa':
                $step = 40;
                $vehicle = Vehicles::find($vehicle_id);
                $vehicle->contract_status = "KRADZIEŻ";
                $vehicle->save();
                if($injury_data['step'] == 'Zliwkidowana - zapłacona' || $injury_data['step'] == 'Zlikwidowana - odmówiona')
                    $step = '-7';
                break;
        }

        $injury = Injury::create(array(
            'user_id' 		=> '1',
            'vehicle_id' 	=> $vehicle_id,
            'client_id' 	=> $client_id,
            'injuries_type_id' 	=> $injury_data['injuries_type_id'],
            'remarks' 		=> $remarks_id,
            'case_nr'		=> $case_nr,
            'date_event' 	=> $injury_data['date_event'],
            'way_of'        => '1',
            'created_at'    => $injury_data['created_at'],
            'date_end'      => $injury_data['date_end'],
            'step'          => $step,
            'task_authorization' => $task_authorization,
            'if_courtesy_car' => '0',
            'injury_nr' => $injury_data['injury_nr'],
            'total_status_id' => $total_status_id,
	        'is_cas_case' => (isCasActive()) ? 1 : 0
        ));

        InjuryStatusesHistory::create([
            'injury_id' => $injury->id,
            'user_id'   => (Auth::user()) ? Auth::user()->id : null,
            'status_id' => $total_status_id,
            'status_type' => 'InjuryTotalStatuses'
        ]);

        if($total_status_id == 1) {
            InjuryWreck::create(array(
                'injury_id' => $injury->id
            ));

            $vehicle = Vehicles::find($vehicle_id);
            $vehicle->contract_status = "CAŁKOWITA";
            $vehicle->save();
        }

        return $injury->id;
    }

    /**
     * Tworzy zapisy w czacie
     * @param $chat
     * @param $injury_id
     */
    private function parseChat($chat, $injury_id)
    {
        $status = 4;

        $this->createChatConversation('opis likwidacji szkody', $injury_id, $status, $chat['description_liquidation']);

        //$this->createChatConversation('Nr szkody', $injury_id, $status, $chat['case_nr']);

        $this->createChatConversation('przekazane do archiwum', $injury_id, $status, 'przekazane do archiwum : '.$chat['archive']);

        if($chat['regres'] > 0)
        {
            $this->createChatConversation('wpłata regresu', $injury_id, $status, 'wpłata regresu : : ' . $chat['regres']);
        }
    }

    /**
     * @param $topic
     * @param $injury_id
     * @param $status
     * @param $content
     * @internal param $chat
     */
    private function createChatConversation($topic, $injury_id, $status, $content)
    {
        if(!is_null($content)) {
            $chat = InjuryChat::create(array(
                    'injury_id' => $injury_id,
                    'user_id' => 1,
                    'topic' => $topic,
                    'status' => $status
                )
            );
            InjuryChatMessages::create(array(
                    'chat_id' => $chat->id,
                    'user_id' => 1,
                    'content' => $content,
                    'status' => $status,
                )
            );
        }
    }

    /**
     * Tworzy zapis w odszkodowaniach do szkody, jeśli kwota odszkodowania > 0
     * @param $amount_of_compensation
     * @param $injury_id
     */
    private function parseCompensation($amount_of_compensation, $injury_id)
    {
        if($amount_of_compensation > 0)
        {
            InjuryCompensation::create(array(
                'injury_id' => $injury_id,
                'compensation' => $amount_of_compensation
            ));
        }
    }
}
