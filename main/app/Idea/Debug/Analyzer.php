<?php


namespace Idea\Debug;


use Branch;
use SyjonProgram;
use Typegarage;
use VmanageVehicle;

class Analyzer
{
    public $vehicle;
    public $contract;
    public $sales_program;

    public function mapGarages($vehicle_id, $vehicle_type, $contract_id)
    {
        $start_time = microtime(true);

        if($vehicle_type == 'vmanage')
        {
            $vehicle = VmanageVehicle::withTrashed()->where('id', $vehicle_id)->with('salesProgram')->first();
        }else{
            $syjonService = new \Idea\SyjonService\SyjonService();
            $vehicle = json_decode($syjonService->loadVehicle($vehicle_id))->data;
            $contract = json_decode($syjonService->loadContract($contract_id))->data;
            $syjonProgram = SyjonProgram::find($contract->program_id);
            $vehicle->salesProgram = $syjonProgram;

            $this->contract = $contract;
        }

        $sales_program = $vehicle->salesProgram ? $vehicle->salesProgram->name_key : '';

        $this->vehicle = $vehicle;
        $this->sales_program = $sales_program;

        $branches = Branch::where('if_map', 1)
            ->where(function ($query) use($sales_program) {
                $group = 1;
                $query->where(function ($query) use ($group) {
                    $query->whereHas('company', function ($query) use ($group) {
                        $query->whereHas('groups', function ($query) use ($group) {
                            $query->where('company_group_id', $group);
                        });
                    });
                });

                $query->whereHas('branchPlanGroups', function ($query) use($sales_program){
                    $query->whereHas('planGroup', function($query) use($sales_program){
                        $query->whereHas('plan', function($query) use($sales_program){
                            $query->where('sales_program', $sales_program);
                        });
                    });
                });
            })
            ->with(['branchPlanGroups' => function($query) use($sales_program) {
                $query->with(['planGroup' => function ($query) use($sales_program){
                    $query->whereHas('plan', function ($query) use ($sales_program) {
                        $query->where('sales_program', $sales_program);
                    });

                    $query->with('companyGroups');
                }]);
                $query->with('branchBrands');
            }, 'typegarages', 'brands', 'authorizations', 'typevehicles.typevehicles',
                'company', 'company.groups', 'company.commissions'])
            ->orderBy('lat')->orderBy('lng');

        $result = [];

        $markers_input = [
            1 => 'on',
            2 => 'on',
            3 => 'on',
            4 => 'on',
            5 => 'on'
        ];
        $typegarages = Typegarage::lists('type', 'id');

        $branches->chunk(50, function ($branches) use(&$result, $markers_input, $typegarages){
            foreach ($branches as $branch) {
                $marker = '/images/markers/yellow.png';
                $marker_id = 4;

                $garagetype = array(0 => 0, 1 => 0, 2 => 0);
                foreach ($branch->typegarages as $val) {
                    if ($val->pivot->typegarages_id && isset($typegarages[$val->pivot->typegarages_id]))
                        $garagetype[$typegarages[$val->pivot->typegarages_id]]++;
                }

                if ($garagetype[0] > 0 && $garagetype[1] > 0) {
                    $marker = '/images/markers/purple.png';
                    $marker_id = 3;
                } elseif ($garagetype[0] > 0) {
                    $marker = '/images/markers/blue.png';
                    $marker_id = 1;
                } elseif ($garagetype[1] > 0) {
                    $marker = '/images/markers/red.png';
                    $marker_id = 2;
                }

                if ($garagetype[2] > 0) {
                    $marker = '/images/markers/purple.png';
                    $marker_id = 3;
                }

                if ($branch->suspended == 1 && $branch->suspended != null) {
                    $marker = '/images/markers/black.png';
                    $marker_id = 5;
                }

                if (isset($markers_input[$marker_id])) {
                    $brands = array(
                        1 => array(),
                        2 => array(),
                    );
                    foreach ($branch->brands->sortBy('name') as $brand) {
                        $brands[$brand->typ][] = $brand->name;
                    }
                    foreach ($brands as $key => $brand) {
                        $brands[$key] = $brand = implode(', ', $brand);
                    }


                    $authorizations_s = implode(', ', $branch->authorizations->sortBy('name')->lists('name'));

                    $typevehicles = '';
                    foreach ($branch->typevehicles as $type_temp) {
                        if ($type_temp->typevehicles && $type_temp->value != 0)
                            $typevehicles .= $type_temp->typevehicles->name . ': ' . $type_temp->value . ' | ';
                    }

                    $result[] = [
                        'id' => $branch->id,
                        'short_name' => $branch->short_name,
                        'company_type' => $branch->company->type,
                        'company_name' => $branch->company->name,
                        'address' => $branch->code . ' ' . $branch->city . ', ' . $branch->street,
                        'email' => ($branch->email != '') ? 'email: ' . $branch->email : '',
                        'phone' => ($branch->phone != '') ? 'telefon: ' . $branch->phone : '',
                        'lat' => $branch->lat,
                        'lng' => $branch->lng,
                        'brands' => $brands,
                        'typevehicles' => $typevehicles,
                        'marker' => $marker,
                        'remarks' => $branch->remarks,
                        'nip' => $branch->nip,
                        'suspended' => $branch->suspended,

                        'open_time' => $branch->open_time ? $branch->open_time : '',
                        'close_time' => $branch->close_time ? $branch->close_time : '',
                        'contact_people' => $branch->contact_people == null ? '' : $branch->contact_people,
                        'priorities' => $branch->priorities == null ? '' : $branch->priorities,
                        'tug_remarks' => $branch->tug_remarks == null ? '' : $branch->tug_remarks,
                        'delivery_cars' => $branch->delivery_cars == null ? '' : $branch->delivery_cars,
                        'commission' => (isset($branch->company) && $branch->company->commissions->first()) ? $branch->company->commissions->first()->commission : 'brak',//company->commissions->first()->commission,
                        'authorizations' => $authorizations_s,
                        'branchPlanGroups' => $branch->branchPlanGroups->toArray()
                    ];
                }
            }
        });




        echo " end = ".(microtime(true) - $start_time)." sec".PHP_EOL;
        return count($result);
    }
}