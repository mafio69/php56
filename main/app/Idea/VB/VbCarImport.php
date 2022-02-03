<?php
/**
 * Created by PhpStorm.
 * User: przemek
 * Date: 29.12.14
 * Time: 11:36
 */

namespace Idea\VB;


use Clients;
use Config;
use Excel;
use Insurance_companies;
use Owners;
use Vehicles;

class VbCarImport {

    public $rows = 0;
    private $file;
    private $worksheet;
    public $msg = '';
    private $vehicleData;

    function __construct($filename)
    {
        $this->file = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/vb/'.$filename;
    }

    function replaceSign($string)
    {
        return str_replace('?????', '', $string);
    }

    public function loadCSV()
    {
        if(file_exists($this->file)) {
            if ($reader = Excel::load($this->file, 'windows-1250')) {
                $this->worksheet = $reader->getSheet(0);
                $highest_row = $this->worksheet->getHighestRow();
                $highest_column = $this->worksheet->getHighestColumn();

                if($highest_column != "AF" && $highest_column != "AI"){
                    $this->msg = "Niepoprawna struktura pliku .csv, zbyt duża liczba przesłanych kolumn.";
                    return false;
                }
                if($highest_row == 0){
                    $this->msg = "Niepoprawna struktura pliku .csv, zerowa liczba przesłanych wierszy.";
                    return false;
                }
                if($highest_row > 1){
                    $this->msg = "Niepoprawna struktura pliku .csv, zbyt duża liczba przesłanych wierszy. Proszę wybrać wiersz do zaimportowania.";
                    $this->rows = $highest_row;
                    return false;
                }

                return true;
            }
        }

        $this->msg = "Błąd odczytu pliku .csv, skontaktuj się z administratorem.";
        return false;
    }

    /**
     * @param int $row
     * @return mixed
     */
    public function parseWorksheet($row = 1)
    {
        $rowData = $this->worksheet->rangeToArray('A'.$row.':AI'.$row,
            NULL,
            TRUE,
            FALSE);
        $vehicleData = $rowData[0];
        $vehicleData = array_map("trim", $vehicleData);

        $vehicleData = array_map(array($this, "replaceSign"), $vehicleData);

        $this->mapVehicleData($vehicleData);

        if(!$this->checkOwner())
        {
            $this->msg = 'Wykryto błąd w zawartości lub strukturze pliku. <br/>';
            $this->msg .= 'Skontaktuj się z administratorem: <a href="mailto:biuro@ebusters.pl">biuro@ebusters.pl</a>';
            return false;
        }

        return $this->getVehicle($this->vehicleData);
    }

    private function mapVehicleData($vehicleData)
    {
        $this->vehicleData['nr_vb'] = mb_strtoupper($vehicleData[0], 'UTF-8');;
        $this->vehicleData['registration'] = mb_strtoupper($vehicleData[1], 'UTF-8');;
        $this->vehicleData['nr_contract'] = mb_strtoupper($vehicleData[2], 'UTF-8');;
        $this->vehicleData['VIN'] = mb_strtoupper($vehicleData[3], 'UTF-8');;
        $this->vehicleData['brand'] = mb_strtoupper($vehicleData[4], 'UTF-8');;
        $this->vehicleData['model'] = mb_strtoupper($vehicleData[5], 'UTF-8');;
        $this->vehicleData['owner'] = $vehicleData[6];
        $this->vehicleData['contract_status'] = mb_strtoupper($vehicleData[11], 'UTF-8');;
        $this->vehicleData['insurance_company_name'] = mb_strtoupper($vehicleData[12], 'UTF-8');;
        $this->vehicleData['expire'] = ($vehicleData[13] == '')? null :$vehicleData[13];
        $this->vehicleData['end_leasing'] = $vehicleData[10];
        $this->vehicleData['nr_policy'] = $vehicleData[20];
        $this->vehicleData['engine'] = mb_strtoupper($vehicleData[22], 'UTF-8');;
        $this->vehicleData['year_production'] = $vehicleData[23];
        $this->vehicleData['contribution'] = is_numeric($vehicleData[24]) ? $vehicleData[24] : null;
        $this->vehicleData['netto_brutto'] = ($vehicleData[25] == 'brutto') ? '2' : '1';
        $this->vehicleData['assistance'] = ($vehicleData[26] == 'tak') ? '1' : '0';
        $this->vehicleData['gap'] = (strtolower($vehicleData[27]) == 'tak') ? '1' : '0';
        $this->vehicleData['cfm'] = 0;

        $first_registration_date = date_parse($vehicleData[29]);
        if($first_registration_date["error_count"] == 0 && checkdate($first_registration_date["month"], $first_registration_date["day"], $first_registration_date["year"]))
            $this->vehicleData['first_registration'] = $vehicleData[29];
        else
            $this->vehicleData['first_registration'] = '0000-00-00';

        $this->vehicleData['mileage'] = is_numeric($vehicleData[30]) ? $vehicleData[24] : null;
        $this->vehicleData['assistance_name'] = $vehicleData[31];

        if(isset($vehicleData[32]) ){
            $phone = explode(';', $vehicleData[32]);
            $phone = implode(',', $phone);
        }else{
            $phone = '';
        }

        if(isset($vehicleData[33]) ){
            $email = explode(';', $vehicleData[33]);
            $email = implode(',', $email);
        }else{
            $email = '';
        }

        $this->vehicleData['client'] = array(
            'group' => $vehicleData[21],
            'saldo' => (strtolower($vehicleData[28]) == 'tak')?'1':'0',
            'name' => $vehicleData[7],
            'NIP' => $vehicleData[8],
            'REGON' => $vehicleData[9],
            'registry_post' => $vehicleData[14],
            'registry_city' => $vehicleData[15],
            'registry_street' => $vehicleData[16],
            'correspond_post' => $vehicleData[17],
            'correspond_city' => $vehicleData[18],
            'correspond_street' => $vehicleData[19],
            'phone' => $phone,
            'email' => $email,
            'firmID' => isset($vehicleData[34]) ? $vehicleData[34] : ''
        );

        $this->vehicleData['client']['id'] = $this->getVehicleClient($this->vehicleData['client']);
    }

    /**
     * @param $vehicleData
     * @return mixed
     */
    private function getVehicle($vehicleData)
    {
        $vehicle = Vehicles::where('nr_contract', '=', $vehicleData['nr_contract'])->orderBy('parent_id', 'desc')->get();
        if (count($vehicle) == 0) {
            return ['existing' => 0, 'vehicle_id' => $this->createVehicle($this->vehicleData)];
        }

        return ['existing' => 1, 'vehicle_id' => $vehicle->first()->id];
    }

    /**
     * @param $vehicleData
     * @return mixed
     */
    private function createVehicle($vehicleData)
    {
        $vehicleData['client_id'] = $this->getVehicleClient($vehicleData['client']);
        $vehicleData['owner_id'] = $this->getOwner($vehicleData['owner']);
        $vehicleData['insurance_company_id'] = $this->getInsuranceCompany($vehicleData['insurance_company_name']);
        $vehicleData['policy_insurance_company_id'] =  $vehicleData['insurance_company_id'];
        $vehicleData['register_as'] = 0;

        $vehicle = Vehicles::create($vehicleData);

        return $vehicle->id;
    }

    /**
     * @param $clientData
     * @return mixed
     */
    private function getVehicleClient($clientData)
    {
        $clientData['NIP'] = trim(str_replace('-', '', $clientData['NIP']));
        $client = Clients::where(function($query) use ($clientData)
        {
            if( $clientData['NIP'] != '')
                $query->where('NIP', '=', $clientData['NIP']);

            if( $clientData['REGON'] != '')
                $query->where('REGON', '=', $clientData['REGON']);

            if( $clientData['NIP'] == '' && $clientData['REGON'] == '')
                $query->where('name', '=', $clientData['name']);

        })->orderBy('parent_id', 'desc')->get();

        if (count($client) == 0) {
            $client = Clients::create($clientData);
            return $client->id;
        } else {
            $old_client = $client->first();
            $old_client->update(['active' => 1]);

            $clientData['parent_id'] = $old_client->id;
            $client = Clients::create($clientData);

            return $client->id;
        }
    }

    /**
     * @param $ownerData
     * @return mixed
     */
    private function getOwner($ownerData)
    {
        if($ownerData == 'VB Leasing Polska S.A.' || $ownerData == 'Idea Leasing S.A.') {
            return 1;
        }
            //$ownerData = 'Idea Fleet S.A.';

        $owner = Owners::where(function($query) use($ownerData){
            $query->where(function ($query) use($ownerData){
                $query->whereNotNull('old_name')->where('old_name', '=',trim($ownerData));
            })->orWhere(function($query) use($ownerData) {
                $query->whereNull('old_name')->where('name', '=',trim($ownerData));
            });
        })->get();

        if (count($owner) == 0) {
            return false;
        } else {
            return $owner->first()->id;
        }
    }

    /**
     * @param $insurance_company_name
     * @return int|mixed
     */
    private function getInsuranceCompany($insurance_company_name)
    {
        if($insurance_company_name != ''){
            $insurance_company = Insurance_companies::where('name', 'like', $insurance_company_name)->get();

            if (count($insurance_company) == 0) {
                $insurance_company = Insurance_companies::create(array(
                    'name' => $insurance_company_name
                ));
                return $insurance_company->id;
            } else {
                return $insurance_company->first()->id;
            }
        }
        return 0;
    }

    /**
     * @return mixed
     */
    public function getVehicleData()
    {
        return $this->vehicleData;
    }

    private function checkOwner()
    {
        $vehicleData = $this->vehicleData;
        return $this->getOwner($vehicleData['owner']);
    }

}
