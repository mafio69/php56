<?php

namespace Idea\Vmanage\Imports;

use Config;
use Excel;
use VmanageUser;

class ImportIdeaFleet extends BaseImporter {
    private $vmanage_company;
    private $client;

    public function __construct($filename)
    {
        $path = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/imports/vmanage/';
        $this->file = $path.$filename;

        $this->vmanage_company = \VmanageCompany::where('name', 'Idea Fleet S.A.')->first();
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
        if(!isset($row['I']) || $row['I'] == '' || $row['I'] == '#N/A')
            return null;

        if(!isset($row['J']))
            $surname = '';
        else{
            $surname = $row['J'];
        }

        $user = VmanageUser::where('name', 'like', $row['I'])->where('surname', 'like', $surname)->first();
        if(is_null($user)) {
            $user['vmanage_company_id'] = $this->vmanage_company->id;
            $user['name'] = $row['I'];
            $user['surname'] = $surname;
            $user['phone'] = (isset($row['K']) && $row['K'] != '#N/A') ? $row['K'] : '';

            $user = VmanageUser::create($user);
        }
        return $user->id;
    }

    private function parseVehicle($row)
    {
        $vehicle = [];
        if(isset($row['C']))
            $vin  = $this->checkVin($row['C']);
        else
            $vin = ['vin' => '', 'exist' => '0'];

        $vehicle['owner_id'] = $this->vmanage_company->owner_id;
        $vehicle['client_id'] = $this->parseClient($row);

        $vehicle['vmanage_company_id'] = $this->vmanage_company->id;

        $vehicleInfo = $this->parseVehicleInfo($row['B']);
        $vehicle['brand_id'] = $vehicleInfo['brand_id'];
        $vehicle['model_id'] = $vehicleInfo['model_id'];
        $vehicle['version'] = $vehicleInfo['version'];
        $vehicle['year_production'] = $row['D'];
        $vehicle['registration'] = $row['A'];
        $vehicle['vin'] = $vin['vin'];
        $vehicle['assistance'] = $this->checkAssistance($row);
        $vehicle['vmanage_user_id'] = $row['vmanage_user_id'];
        $vehicle['keeper_email'] = (isset($row['L'])) ? $row['L'] : '';
        $vehicle['min_franchise'] = (isset($row['R'])) ? $row['R'] : '';
        $vehicle['contract_status'] = (isset($row['F'])) ? $row['F'] : '';
        $vehicle['nr_contract'] = (isset($row['E'])) ? $row['E'] : '';
        $vehicle['cfm'] = 1;

        if($vin['exist'] == 1)
            $vin['vehicle']->update($vehicle);

        else
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

    private function findBrand($brand_name)
    {
        $brand = \Brands::where('typ', 1)->where('name', 'like', $brand_name)->first();
        if( !$brand ) {
            $brand = \Brands::where('typ', 2)->where('name', 'like', $brand_name)->first();

            if(! $brand)
            {
                $brand = \Brands::create(['typ' => 1, 'name' => $brand_name]);
            }
        }

        return $brand->id;
    }

    private function findModel($brand_id, $model_name)
    {
        $model = \Brands_model::where('brand_id', $brand_id)->where('typ', 1)->where('name', 'like', $model_name)->first();

        if(! $model) {
            $model = \Brands_model::where('brand_id', $brand_id)->where('typ', 2)->where('name', 'like', $model_name)->first();

            if(!$model )
                $model = \Brands_model::create(['typ' => 1, 'brand_id' => $brand_id, 'name' => $model_name]);
        }

        return $model->id;
    }

    private function parseExcelDate($date){
        $date = \PHPExcel_Shared_Date::ExcelToPHP($date);
        return date('Y-m-d',(int) $date);
    }

    private function checkVin($vin)
    {
        $vehicle = \Vehicles::where('vin', 'like', $vin)->latest()->first();
        if($vehicle)
        {
            $vehicle->active = 9;
            $vehicle->save();
        }

        $vehicle = \VmanageVehicle::where('vin', 'like', $vin)->where('outdated', 0)->first();
        if(!is_null($vehicle))
            return ['vin' => $vin, 'exist' => '1', 'vehicle' => $vehicle];

        return ['vin' => $vin, 'exist' => '0'];
    }

    private function parseInsuranceExpireDate($date)
    {
        $date = $this->parseExcelDate($date);
        $date = \Date::createFromFormat('Y-m-d', $date);
        return $date->addMonths(12)->toDateString();
    }

    private function checkAssistance($row)
    {
        if(isset($row['S']) && mb_strtoupper($row['S']) == 'TAK')
            return $row['M'].' '.$row['T'];

        return null;
    }

    private function parseClient($row)
    {
        $name = trim($row['G']);
        $client = \Clients::where('name', $name)->first();
        if($client)
        {
            return $client->id;
        }


        $client = \Clients::create(['name' => $name]);
        return $client->id;
    }


}