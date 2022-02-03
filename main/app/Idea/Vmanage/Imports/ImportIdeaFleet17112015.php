<?php

namespace Idea\Vmanage\Imports;

use Config;
use Excel;
use Idea\VoivodeshipMatcher\SingleMatching;
use VmanageUser;

class ImportIdeaFleet17112015 extends BaseImporter {
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
            $this->parseVehicle($row);
        }
    }

    private function parseVehicle($row)
    {
        $vehicle = [];
        if(isset($row['B']))
            $registration  = $this->checkRegistration($row['B']);
        else
            $registration = '';

        $vehicle['owner_id'] = $this->vmanage_company->owner_id;
        $vehicle['client_id'] = $this->parseClient($row);

        $vehicle['vmanage_company_id'] = $this->vmanage_company->id;

        $vehicleInfo = $this->parseVehicleInfo($row['M']);
        $vehicle['brand_id'] = $vehicleInfo['brand_id'];
        $vehicle['model_id'] = $vehicleInfo['model_id'];
        $vehicle['version'] = $vehicleInfo['version'];
        $vehicle['year_production'] = $row['N'];
        $vehicle['first_registration'] = $row['J'];
        $vehicle['registration'] = $registration;
        $vehicle['contract_status'] = (isset($row['I'])) ? $row['I'] : '';
        $vehicle['nr_contract'] = (isset($row['A'])) ? $row['A'] : '';
        $vehicle['cfm'] = 1;
        $vehicle['contribution'] = $row['K'];

        \VmanageVehicle::create($vehicle);
    }

    private function parseVehicleInfo($info)
    {
        $info = preg_replace('/\s+/', ' ',$info);
        $info = str_replace('Samochód osobowy ', '', $info);
        $info = str_replace('Samochód ciężarowy ', '', $info);

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

    private function parseClient($row)
    {
        $name = trim($row['F']);
        $street = trim($row['G']);
        $city = explode(' ',trim($row['H']));
        $post_code = $city[0];
        unset($city[0]);
        $city = implode(' ', $city);

        $client = \Clients::where('name', $name)->first();
        if($client)
        {
            return $client->id;
        }

        if(strlen($post_code) == 6)
        {
            $matcher = new SingleMatching();
            $voivodeship_id = $matcher->match($post_code);
            $registry_voivodeship_id = $voivodeship_id;
        }else
            $registry_voivodeship_id = null;

        $client = \Clients::create(['name' => $name, 'registry_post' => $post_code, 'registry_street' => $street, 'registry_city' => $city, 'registry_voivodeship_id' => $registry_voivodeship_id]);
        return $client->id;
    }

    private function checkRegistration($registration)
    {
        $vehicle = \Vehicles::where('registration', $registration)->latest()->first();
        if($vehicle)
        {
            $vehicle->active = 9;
            $vehicle->save();
        }

        $vehicle = \VmanageVehicle::where('registration', $registration)->where('outdated', 0)->first();
        if(!is_null($vehicle))
        {
            $vehicle->delete();
        }

        return $registration;
    }

}