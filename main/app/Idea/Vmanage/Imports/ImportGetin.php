<?php

namespace Idea\Vmanage\Imports;


use Carbon\Carbon;
use Config;
use Idea\AddressParser\AddressParser;
use VmanageVehicle;
use VmanageVehicleHistory;


class ImportGetin extends BaseImporter
{

    private $owners;
    private $addressParser;

    public function __construct($filename = null)
    {
        $path = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/imports/vmanage/';
        $this->file = $path.$filename;
        foreach (\VmanageCompanies::get() as $item) {
            $this->owners[$item->id] = $item;
        }
        $this->addressParser = new AddressParser();
    }

    public function load()
    {
        \Debugbar::disable();
        \DB::disableQueryLog();
        set_time_limit(500);
        if(file_exists($this->file)) {
            $data = array();
            $header = NULL;
            if (($handle = fopen($this->file, 'r')) !== FALSE)
            {
                $lp = 0;
                while (($row = fgetcsv($handle, 1000, '$')) !== FALSE)
                {
                    if(!$header) {
                        $header = $row;
                    }else {
                        $lp ++;
                        $data[] = $this->explodeRow($row[0]);
                    }
                }
                fclose($handle);
            }

            $this->rows = $data;
        }

        return true;
    }

    public function loadTsv()
    {
        \Debugbar::disable();
        \DB::disableQueryLog();
        set_time_limit(500);
        if(file_exists($this->file)) {
            $data = array();
            $header = NULL;
            if (($handle = fopen($this->file, 'r')) !== FALSE)
            {
                fgetcsv($handle, 0,chr(9));
                $lp = 0;
                while(($row = fgetcsv($handle,0,chr(9)))!==FALSE){
                    $lp ++;
                    $data[] = $this->explodeTsvRow($row);
                }
                fclose($handle);
            }

            return $data;
        }

        return false;
    }

    public function parse()
    {
        foreach ($this->rows as $row)
        {
            $this->parseRow($row);
        }
    }

    private function explodeRow($row)
    {
        $explodedRow = [
            'lp'    =>  substr($row, 0, 12),
            'registration'  =>  substr($row, 11, 10),
            'vin'   =>  substr($row, 21, 20 ),
            'brand' =>  substr($row, 41, 31),
            'model' =>  substr($row, 72, 31),
            'pojemnosc_silnika' =>  substr($row, 103, 12),
            'moc_silnika'   =>  substr($row, 115, 12),
            'jednostka_mocy'    =>  substr($row, 127, 10),
            'rok_produkcji' =>  substr($row, 137, 14),
            'typ_nadwozia'  =>  substr($row, 151, 63),
            'data_konca_polisy' =>  substr($row, 214, 20),
            'nazwa_TU'  =>  substr($row, 234, 40),
            'wlasciciel_pojazdu'    => substr($row, 274, 55),
            'sprzedawca'    =>  substr($row, 329, 45),
            'dane_sprzedawcy'   =>  substr($row, 374, 75),
            'dealer_forda'  =>  substr($row, 449, 15),
            'data_zawarcia_UL'  =>  substr($row, 464, 20),
            'NIP_dostawcy'  =>  substr($row, 484, 12)
        ];

        $explodedRow = array_map('trim' , $explodedRow);

        return $explodedRow;
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
            'NIP_dostawcy'  =>  $row[17]
        ];

        $explodedRow = array_map('trim' , $explodedRow);
        foreach ($explodedRow as $k => $item)
        {
            $explodedRow[$k] = iconv('WINDOWS-1250','utf-8', $item);
        }

        return $explodedRow;
    }

    public function parseRow($row)
    {
        $owner = $this->getOwner($row['wlasciciel_pojazdu']);
        if(! $owner ) return;
        $vehicleInfo = $this->getCarInfo($row);
        $vehicle = \VmanageVehicle::where('vin', $row['vin'])->where('outdated', 0)->first();
        $vehicle_data = [
            'if_truck' => 1,
            'vmanage_company_id' => $owner->id,
            'owner_id' => $owner->owner_id,
            'brand_id' => $vehicleInfo['brand_id'],
            'model_id' => $vehicleInfo['model_id'],
            'version' => $row['typ_nadwozia'],
            'year_production' => $row['rok_produkcji'],
            'engine_capacity' => $row['pojemnosc_silnika'],
            'horse_power' => $row['moc_silnika'],
            'registration' => $row['registration'],
            'vin' => $row['vin'],
            'insurance_expire_date' => $this->getDate($row['data_konca_polisy'], 'Y-m-d', '0000-00-00'),
            'insurance_company_id' => $this->getInsuranceCompany($row),
            'vmanage_seller_id' => $this->getSeller($row),
            'agreement_date' => $this->getDate($row['data_zawarcia_UL'], 'd.m.Y', '00.00.0000'),
//            'end_leasing' => $this->getDate($row['prze_data'], 'd.m.Y', '00.00.0000'),
            'nr_policy' => $row['nr_polisy'],
            'nr_contract' => $row['nr_umowy'],
//            'contract_status' => $this->getContractStatus($row['stan_um']),
            'deleting_file' => null
        ];
        if(! $vehicle){
            $vehicle = \VmanageVehicle::where('vin', $row['vin'])->where('outdated', 0)->withTrashed()->first();
        }
        if($vehicle)
        {
            if ($vehicle->trashed()){
                $vehicle->restore();
            };

            if(! $this->compareVehicleInstances($vehicle, $vehicle_data)){
                $new_vehicle = VmanageVehicle::create($vehicle->toArray());
                $new_vehicle->save();
                $new_vehicle->update($vehicle_data);

                $vehicle->outdated = 1;
                $vehicle->save();

                $existing_history = VmanageVehicleHistory::where('vmanage_vehicle_id', $vehicle->id)->orWhere('previous_vmanage_vehicle_id', $vehicle->id)->first();

                if($existing_history)
                {
                    VmanageVehicleHistory::create([
                        'history_id' => $existing_history->history_id,
                        'vmanage_vehicle_id'    =>  $new_vehicle->id,
                        'previous_vmanage_vehicle_id'   => $vehicle->id
                    ]);
                }else{
                    $highest_history = VmanageVehicleHistory::orderBy('history_id', 'desc')->first();
                    if($highest_history)
                    {
                        $history_id = $highest_history->history_id + 1;
                    }else{
                        $history_id = 1;
                    }

                    VmanageVehicleHistory::create([
                        'history_id' => $history_id,
                        'vmanage_vehicle_id'    =>  $new_vehicle->id,
                        'previous_vmanage_vehicle_id'   => $vehicle->id
                    ]);
                }
            }else {
                $vehicle->update($vehicle_data);
            }
        }else {
            \VmanageVehicle::create($vehicle_data);
        }
    }



    private function getOwner($wlasciciel_pojazdu)
    {
        switch ($wlasciciel_pojazdu){
            case 'Idea Getin Leasing SA':
                return $this->owners[9];
            case 'IGL Sp Akcyjna Automotive':
                return $this->owners[12];
            case 'GL SA 3 Sp.Komand-Akcyjna':
                return $this->owners[12];
            case 'GL SA 2 Sp.Komand-Akcyjna':
                return $this->owners[11];
            case 'GL SA 2 Sp.KomandAkcyjna2':
                return $this->owners[11];
            default:
                return null;
        }
    }

    private function getCarInfo($info)
    {
        $vehicleInfo['brand_id'] = $this->findBrand($info['brand']);
        $vehicleInfo['model_id'] = $this->findModel($vehicleInfo['brand_id'], $info['model']);

        return $vehicleInfo;
    }

    private function findBrand($brand_name)
    {
        $brand = \Brands::where('typ', 2)->where('name', 'like', trim($brand_name))->first();
        if( !$brand ) {
            $brand = \Brands::where('typ', 1)->where('name', 'like', trim($brand_name))->first();
        }

        if( !$brand ) {
            $brand = \Brands::create(['typ' => 2, 'name' => trim($brand_name)]);
        }


        return $brand->id;
    }

    private function findModel($brand_id, $model_name)
    {
        $model = \Brands_model::where('brand_id', $brand_id)->where('typ', 2)->where('name', 'like', trim($model_name) )->first();

        if(! $model) {
            $model = \Brands_model::where('brand_id', $brand_id)->where('typ', 1)->where('name', 'like', trim($model_name) )->first();
        }

        if(! $model) {
            $model = \Brands_model::create(['typ' => 2, 'brand_id' => $brand_id, 'name' => trim($model_name) ]);
        }

        return $model->id;
    }

    private function getInsuranceCompany($row)
    {
        if($row['nazwa_TU'] != '')
        {
            $insurance_company = \Insurance_companies::where('name', 'like', $row['nazwa_TU'])->first();
            if($insurance_company)
                return $insurance_company->id;
            else{
                $insurance_company = \Insurance_companies::create(['name' => $row['nazwa_TU']]);
                return $insurance_company->id;
            }
        }
        return null;
    }

    private function getSeller($row)
    {
        $addr_explode = explode('TEL.', mb_strtoupper($row['dane_sprzedawcy']));
        if(isset($addr_explode[1]))
            $phone = trim($addr_explode[1]);
        else
            $phone = null;

        $address = $this->addressParser->parseAddress(trim($addr_explode[0]));
        $nip = str_replace(' ','' , $row['NIP_dostawcy']);
        $name = $row['sprzedawca'];

        $seller = \VmanageSeller::where('nip', $nip)->first();
        if(! $seller)
        {
            $seller = \VmanageSeller::create([
                'name' => $name,
                'nip' => $nip,
                'street' => (isset($address['STREET'])) ? $address['STREET'] : null,
                'post' => (isset($address['POSTAL_CODE'])) ? $address['POSTAL_CODE'] : null,
                'city' => (isset($address['CITY'])) ? $address['CITY'] : null,
                'phone'  => $phone
            ]);
        }else{
            $seller->update([
                'name' => $name,
                'street' => (isset($address['STREET'])) ? $address['STREET'] : $seller->street,
                'post' => (isset($address['POSTAL_CODE'])) ? $address['POSTAL_CODE'] : $seller->post,
                'city' => (isset($address['CITY'])) ? $address['CITY'] : $seller->city,
                'phone'  => ($phone != '') ? $phone : $seller->phone
            ]);
        }

        return $seller->id;
    }

    private function getDate($row_date, $date_format, $date_zero)
    {
        if($row_date != '' &&  $row_date != $date_zero) {
            $date = Carbon::createFromFormat($date_format, $row_date);
            if ($date) {
                $date = $date->format('Y-m-d');

                return $date;
            }
        }

        return null;
    }

    private function compareVehicleInstances($vehicle, $vehicle_data)
    {
        if($vehicle->vmanage_company_id != $vehicle_data['vmanage_company_id']) {
            return false;
        }

        if($vehicle->owner_id != $vehicle_data['owner_id']) {
            return false;
        }

        if($vehicle->registration != $vehicle_data['registration']) {
            return false;
        }

        if($vehicle->insurance_company_id != $vehicle_data['insurance_company_id']) {
            return false;
        }

        if($vehicle->insurance_expire_date != $vehicle_data['insurance_expire_date'] ) {
            if (is_null($vehicle_data['insurance_expire_date']) &&   $vehicle->insurance_expire_date == '0000-00-00') {
                return true;
            } else {
                return false;
            }
        }

        return true;
    }

    private function getContractStatus($stan_um)
    {
        $contract_status = \VmanageContractStatus::where('code', 'like', $stan_um)->first();

        if($contract_status){
            return $contract_status->status;
        }

        return null;
    }


}

