<?php

namespace Idea\Vmanage\Imports;


use Carbon\Carbon;
use Config;
use Excel;
use VmanageVehicleHistory;

class ImportIdeaBank extends BaseImporter
{
    public function __construct($filename)
    {
        $path = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/imports/vmanage/';
        $this->file = $path.$filename;

        $this->vmanage_company = \VmanageCompany::where('name', 'Idea Bank Spółka Akcyjna')->first();
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
        if(isset($row['M']))
            $registration  = $this->checkRegistration($row['M']);
        else
            $registration = null;

        $vehicle['owner_id'] = $this->vmanage_company->owner_id;
        $vehicle['client_id'] = $this->vmanage_company->client_id;
        $vehicle['vmanage_company_id'] = $this->vmanage_company->id;

        $vehicleInfo = $this->parseVehicleInfo($row);
        $vehicle['brand_id'] = $vehicleInfo['brand_id'];
        $vehicle['model_id'] = $vehicleInfo['model_id'];
        $vehicle['year_production'] = $row['P'];
        $vehicle['registration'] = ($registration) ? $registration['registration'] : $registration;
        $vehicle['vin'] = $row['L'];
        $vehicle['insurance_expire_date'] = Carbon::createFromFormat('Y-m-d H:i:s', $row['G'])->format('Y-m-d');
        $vehicle['insurance'] = (isset($row['S'])) ? $row['S'] : null;
        $vehicle['insurance_amount'] = (isset($row['N'])) ? $row['N'] : null;
        $vehicle['assistance'] = (isset($row['U'])) ? $row['U'] : null;
        $vehicle['nr_contract'] = (isset($row['Q'])) ? $row['Q'] : '';
        $vehicle['cfm'] = 0;
        $vehicle['insurance_company_id'] = $this->parseInsuranceCompany($row);

        $new_vehicle = \VmanageVehicle::create($vehicle);

        if($registration && $registration['history_id']) {
            VmanageVehicleHistory::create([
                'history_id' => $registration['history_id'],
                'vmanage_vehicle_id' => $new_vehicle->id,
                'previous_vmanage_vehicle_id' => $registration['previous_vmanage_vehicle_id']
            ]);
        }
    }

    private function parseVehicleInfo($info)
    {
        $vehicle_type = $info['I'];
        if($vehicle_type == 'osobowy')
            $vehicle_type = 1;
        else
            $vehicle_type = 2;

        $vehicleInfo['brand_id'] = $this->findBrand($info['J'], $vehicle_type);
        $vehicleInfo['model_id'] = $this->findModel($vehicleInfo['brand_id'], $info['K'], $vehicle_type);

        return $vehicleInfo;
    }

    private function findBrand($brand_name, $vehicle_type)
    {
        $brand = \Brands::where('typ', $vehicle_type)->where('name', 'like', trim($brand_name))->first();
        if( !$brand ) {
            $brand = \Brands::create(['typ' => $vehicle_type, 'name' => trim($brand_name)]);
        }

        return $brand->id;
    }

    private function findModel($brand_id, $model_name, $vehicle_type)
    {
        $model = \Brands_model::where('brand_id', $brand_id)->where('typ', $vehicle_type)->where('name', 'like', trim($model_name) )->first();

        if(! $model) {
            $model = \Brands_model::create(['typ' => $vehicle_type, 'brand_id' => $brand_id, 'name' => trim($model_name) ]);
        }

        return $model->id;
    }

    private function checkRegistration($registration)
    {
        $vehicle = \Vehicles::where('registration', $registration)->latest('id')->first();
        if($vehicle)
        {
            $vehicle->active = 9;
            $vehicle->save();
        }

        $vehicle = \VmanageVehicle::where('registration', $registration)->latest('id')->first();
        if(!is_null($vehicle))
        {
            $vehicle->outdated = 1;
            $vehicle->save();

            $existing_history = VmanageVehicleHistory::where('vmanage_vehicle_id', $vehicle->id)->orWhere('previous_vmanage_vehicle_id', $vehicle->id)->first();

            if($existing_history)
            {
                $history_id = $existing_history->history_id;
            }else{
                $highest_history = VmanageVehicleHistory::orderBy('history_id', 'desc')->first();
                if($highest_history)
                {
                    $history_id = $highest_history->history_id + 1;
                }else{
                    $history_id = 1;
                }
            }
            $previous_vmanage_vehicle_id = $vehicle->id;
        }else{
            $history_id = null;
            $previous_vmanage_vehicle_id = null;
        }

        return ['registration' => $registration, 'history_id' => $history_id, 'previous_vmanage_vehicle_id' => $previous_vmanage_vehicle_id];
    }

    private function parseInsuranceCompany($row)
    {
        if(isset($row['AD']))
        {
            $insurance_company = \Insurance_companies::where('name', 'like', $row['AD'])->first();
            if($insurance_company)
                return $insurance_company->id;

            else{
                $insurance_company = \Insurance_companies::create(['name' => $row['AD']]);
                return $insurance_company->id;
            }
        }
        return null;
    }
}