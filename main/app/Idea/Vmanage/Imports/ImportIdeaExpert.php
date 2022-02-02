<?php

namespace Idea\Vmanage\Imports;

use Config;
use Excel;
use VmanageUser;

class ImportIdeaExpert extends BaseImporter {
    private $vmanage_company;
    private $client;

    public function __construct($filename)
    {
        $path = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/imports/vmanage/';
        $this->file = $path.$filename;

        $this->vmanage_company = \VmanageCompany::where('name', 'Idea Expert S.A.')->first();
        $this->client = $this->vmanage_company->client;
    }

    public function load()
    {
        set_time_limit(500);

        if(file_exists($this->file)) {
            if ($reader = Excel::load($this->file, 'windows-1250')) {
                $objWorksheet = $reader->getActiveSheet();

                $maxCell = $objWorksheet->getHighestRowAndColumn();
                $data = $objWorksheet->rangeToArray('A2:' . $maxCell['column'] . $maxCell['row'],
                    NULL,
                    TRUE,
                    FALSE,
                    TRUE);
                $data = array_map('array_filter', $data);
                $this->rows = array_filter($data);
                return true;
            }
        }

        return $this->parseFailed("Błąd odczytu pliku, skontaktuj się z administratorem.");
    }

    public function parseRows()
    {
        foreach($this->rows as $k => $row)
        {
            $row['vmanage_user_id'] = $this->parseUser($row);
            $this->parseVehicle($row);
        }
    }

    private function parseUser($row)
    {
        if(!isset($row['R']) || $row['R'] == '' || $row['R'] == '#N/A')
            return null;

        $name_surname = explode(' ', $row['R']);

        $user = VmanageUser::where('name', 'like', $name_surname[0])->where('surname', 'like', $name_surname[1])->first();
        if(is_null($user)) {
            $user['vmanage_company_id'] = $this->vmanage_company->id;
            $user['name'] = $name_surname[0];
            $user['surname'] = $name_surname[1];
            $user['phone'] = (isset($row['S']) && $row['S'] != '#N/A') ? $row['S'] : '';
            $user['email'] = (isset($row['T']) && $row['T'] != '#N/A') ? str_replace(';', '', $row['T']) : '';

            $user = VmanageUser::create($user);
        }
        return $user->id;
    }

    private function parseVehicle($row)
    {
        $vehicle = [];
        if($row['M'] == 'tak') {
            $vehicle['owner_id'] = $this->parseOwner($row);
            $vehicle['client_id'] = $this->client->id;
        }else
            $vehicle['owner_id'] = $this->vmanage_company->owner_id;

        $vehicle['vmanage_company_id'] = $this->vmanage_company->id;

        $vehicleInfo = $this->parseVehicleInfo($row['E']);
        $vehicle['brand_id'] = $vehicleInfo['brand_id'];
        $vehicle['model_id'] = $vehicleInfo['model_id'];
        $vehicle['version'] = $vehicleInfo['version'];
        $vehicle['year_production'] = $row['I'];
        $vehicle['first_registration'] = ($row['J'] != 'b.d.') ? $this->parseExcelDate($row['J']) : null;
        $vehicle['doors_nb'] = $row['L'];
        $vehicle['engine_capacity'] = $row['G'];
        $vehicle['registration'] = $row['B'];
        $vehicle['vin'] = $this->checkVin($row['C']);
        $vehicle['insurance_expire_date'] = $this->parseInsuranceExpireDate($row['N']);
        $vehicle['insurance'] = $row['K'];
        $vehicle['assistance'] = $this->checkAssistance($row);
        $vehicle['place_of_usage'] = (isset($row['Q'])) ? $row['Q'] : null;
        $vehicle['vmanage_user_id'] = $row['vmanage_user_id'];

        \VmanageVehicle::create($vehicle);
    }

    private function parseOwner($row)
    {
        $owner = \Owners::where('name', 'like', $row['P'])->first();
        if(is_null($owner))
        {
            $owner['name'] = $row['P'];
            $owner['short_name'] = shortenName($row['P']);
            $owner['owners_group_id'] = 4;
            $owner = \Owners::create($owner);
        }

        return $owner->id;
    }

    private function parseVehicleInfo($info)
    {
        $info_parts = explode(' ', $info);

        $vehicleInfo['brand'] = $info_parts[0];
        unset($info_parts[0]);
        if(strpos($info_parts[1],'_') !== false){
            $model_info = explode('_', $info_parts[1]);
            $vehicleInfo['model'] = $model_info[0];
            $info_parts[1] = $model_info[1];
        }else{
            $vehicleInfo['model'] = $info_parts[1];
            unset($info_parts[1]);
        }
        ksort($info_parts);
        $vehicleInfo['version'] = implode(' ', $info_parts);

        $vehicleInfo['brand_id'] = $this->findBrand($vehicleInfo['brand']);
        $vehicleInfo['model_id'] = $this->findModel($vehicleInfo['brand_id'], $vehicleInfo['model']);

        return $vehicleInfo;
    }

    private function findBrand($brand)
    {
        $brand = \Brands::where('typ', 1)->where('name', 'like', $brand)->first();
        if(is_null($brand))
            return $this->parseFailed("Błąd podczas importu marki pojazdu.");

        return $brand->id;
    }

    private function findModel($brand_id, $model)
    {
        $model = \Brands_model::where('brand_id', $brand_id)->where('typ', 1)->where('name', 'like', $model)->first();

        if(is_null($model))
            return $this->parseFailed("Błąd podczas importu modelu pojazdu.");

        return $model->id;
    }

    private function parseExcelDate($date){
        $date = \PHPExcel_Shared_Date::ExcelToPHP($date);
        return date('Y-m-d',(int) $date);
    }

    private function checkVin($vin)
    {
        $vehicle = \VmanageVehicle::where('vin', 'like', $vin)->first();
        if(!is_null($vehicle))
            return $this->parseFailed("Istnieje już pojazd o podanym numrze VIN.");

        return $vin;
    }

    private function parseInsuranceExpireDate($date)
    {
        $date = $this->parseExcelDate($date);
        $date = \Date::createFromFormat('Y-m-d', $date);
        return $date->addMonths(12)->toDateString();
    }

    private function checkAssistance($row)
    {
        if(mb_strtoupper($row['U']) == 'TAK')
            return $row['V'].' '.$row['W'];

        return null;
    }


}