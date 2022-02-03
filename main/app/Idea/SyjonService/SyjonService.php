<?php
namespace Idea\SyjonService;

use Config;

class SyjonService
{
    private $data = [];
    private $curl;

    /**
     * SyjonService constructor.
     */
    public function __construct()
    {
        $this->data['api_token'] = Config::get('webconfig.SYJON_API_TOKEN');
        $this->curl = new Curl();
    }

    public function searchContracts($request)
    {
        $data = $this->data;

        foreach($request as $field_name => $value)
        {
            if($value != '')
            {
                $data[$field_name] = $value;
            }
        }

        $curl = $this->curl->post(Config::get('webconfig.SYJON_URL').'/api/dls/searcher/search', $data);

        try {
            return $curl->getResponse();
        } catch (\RuntimeException $ex) {
            \Log::error('api/dls/searcher/search', ['error' => $ex->getMessage(), 'code' => $ex->getCode(), $data]);
            return json_encode(['error' => $ex->getMessage(), 'code' => $ex->getCode()]);
        }
    }

    public function loadContract($contract_id)
    {
        $data = $this->data;
        $data['id'] = $contract_id;

        $curl = $this->curl->post(Config::get('webconfig.SYJON_URL').'/api/dls/searcher/contract', $data);

        try {
            return $curl->getResponse();
        } catch (\RuntimeException $ex) {
            \Log::error('api/dls/searcher/contract', ['error' => $ex->getMessage(), 'code' => $ex->getCode(), $data]);
            return json_encode(['error' => $ex->getMessage(), 'code' => $ex->getCode()]);
        }
    }

    public function loadVehicle($vehicle_id, $contact_id = null)
    {
        $data = $this->data;
        $data['id'] = $vehicle_id;

        if($contact_id){
            $data['contract_id'] = $contact_id;
        }

        $curl = $this->curl->post(Config::get('webconfig.SYJON_URL').'/api/dls/searcher/vehicle', $data);

        try {
            return $curl->getResponse();
        } catch (\RuntimeException $ex) {
            \Log::error('api/dls/searcher/vehicle', ['error' => $ex->getMessage(), 'code' => $ex->getCode(), $data]);
            return json_encode(['error' => $ex->getMessage(), 'code' => $ex->getCode()]);
        }
    }

    public function loadPolicy($policy_id)
    {
        $data = $this->data;
        $data['id'] = $policy_id;

        $curl = $this->curl->post(Config::get('webconfig.SYJON_URL').'/api/dls/searcher/policy', $data);

        try {
            return $curl->getResponse();
        } catch (\RuntimeException $ex) {
            \Log::error('api/dls/searcher/policy', ['error' => $ex->getMessage(), 'code' => $ex->getCode(), $data]);
            return json_encode(['error' => $ex->getMessage(), 'code' => $ex->getCode()]);
        }
    }

    public function loadVehicleContracts($vehicle_id)
    {
        $data = $this->data;
        $data['id'] = $vehicle_id;

        $curl = $this->curl->post(Config::get('webconfig.SYJON_URL').'/api/dls/searcher/vehicle-contracts', $data);

        try {
            return $curl->getResponse();
        } catch (\RuntimeException $ex) {
            \Log::error('api/dls/searcher/vehicle-contracts', ['error' => $ex->getMessage(), 'code' => $ex->getCode(), $data]);
            return json_encode(['error' => $ex->getMessage(), 'code' => $ex->getCode()]);
        }
    }

    public function updateStage($syjon_vehicle_id, $stage_id)
    {
        $data = $this->data;
        $data['id'] = $syjon_vehicle_id;
        $data['stage_id'] = $stage_id;

        $curl = $this->curl->post(Config::get('webconfig.SYJON_URL').'/api/dls/vehicle/stage', $data);

        try {
            return $curl->getResponse();
        } catch (\RuntimeException $ex) {
            \Log::error('api/dls/vehicle/stage', ['error' => $ex->getMessage(), 'code' => $ex->getCode(), $data]);
            return json_encode(['error' => $ex->getMessage(), 'code' => $ex->getCode()]);
        }
    }

    public function updateStatus($syjon_vehicle_id, $status_id)
    {
        $data = $this->data;
        $data['id'] = $syjon_vehicle_id;
        $data['status_id'] = $status_id;

        $curl = $this->curl->post(Config::get('webconfig.SYJON_URL').'/api/dls/vehicle/status', $data);

        try {
            return $curl->getResponse();
        } catch (\RuntimeException $ex) {
            \Log::error('api/dls/vehicle/status', ['error' => $ex->getMessage(), 'code' => $ex->getCode(), $data]);
            return json_encode(['error' => $ex->getMessage(), 'code' => $ex->getCode()]);
        }
    }

    public function updateTotalStatus($syjon_vehicle_id, $total_status_id)
    {
        $data = $this->data;
        $data['id'] = $syjon_vehicle_id;
        $data['total_status_id'] = $total_status_id;

        $curl = $this->curl->post(Config::get('webconfig.SYJON_URL').'/api/dls/vehicle/total-status', $data);

        try {
            return $curl->getResponse();
        } catch (\RuntimeException $ex) {
            \Log::error('api/dls/vehicle/total-status', ['error' => $ex->getMessage(), 'code' => $ex->getCode(),$data]);
            return json_encode(['error' => $ex->getMessage(), 'code' => $ex->getCode()]);
        }
    }

    public function updateTheftStatus($syjon_vehicle_id, $theft_status_id)
    {
        $data = $this->data;
        $data['id'] = $syjon_vehicle_id;
        $data['theft_status_id'] = $theft_status_id;

        $curl = $this->curl->post(Config::get('webconfig.SYJON_URL').'/api/dls/vehicle/theft-status', $data);

        try {
            return $curl->getResponse();
        } catch (\RuntimeException $ex) {
            \Log::error('api/dls/vehicle/theft-status', ['error' => $ex->getMessage(), 'code' => $ex->getCode(), $data]);
            return json_encode(['error' => $ex->getMessage(), 'code' => $ex->getCode()]);
        }
    }

    public function updateField($syjon_vehicle_id, $field, $value)
    {
        $data = $this->data;
        $data['id'] = $syjon_vehicle_id;
        $data['field'] = $field;
        $data['value'] = $value;

        $curl = $this->curl->post(Config::get('webconfig.SYJON_URL').'/api/dls/vehicle/field', $data);

        try {
            return $curl->getResponse();
        } catch (\RuntimeException $ex) {
            \Log::error('api/dls/vehicle/field', ['error' => $ex->getMessage(), 'code' => $ex->getCode(), $data]);
            return json_encode(['error' => $ex->getMessage(), 'code' => $ex->getCode()]);
        }
    }

    public function syncDictionaryTotalStatuses()
    {
        $data = $this->data;

        $totalStatuses = \InjuryTotalStatuses::get()->toArray();
        $data['statuses'] = json_encode( $totalStatuses );

        $curl = $this->curl->post(Config::get('webconfig.SYJON_URL').'/api/dls/vehicle/dictionary-total-statuses', $data);

        try {
            return $curl->getResponse();
        } catch (\RuntimeException $ex) {
            \Log::error('api/dls/vehicle/dictionary-total-statuses', ['error' => $ex->getMessage(), 'code' => $ex->getCode(), $data]);
            return json_encode(['error' => $ex->getMessage(), 'code' => $ex->getCode()]);
        }
    }

    public function syncDictionaryTheftStatuses()
    {
        $data = $this->data;

        $theftStatuses = \InjuryTheftStatuses::get()->toArray();
        $data['statuses'] = json_encode( $theftStatuses );

        $curl = $this->curl->post(Config::get('webconfig.SYJON_URL').'/api/dls/vehicle/dictionary-theft-statuses', $data);

        try {
            return $curl->getResponse();
        } catch (\RuntimeException $ex) {
            \Log::error('api/dls/vehicle/dictionary-theft-statuses', ['error' => $ex->getMessage(), 'code' => $ex->getCode(), $data]);
            return json_encode(['error' => $ex->getMessage(), 'code' => $ex->getCode()]);
        }
    }

    public function syncDictionaryStages()
    {
        $data = $this->data;

        $stages = \InjuryStepStage::get()->toArray();
        $data['stages'] = json_encode( $stages );

        $curl = $this->curl->post(Config::get('webconfig.SYJON_URL').'/api/dls/vehicle/dictionary-stages', $data);

        try {
            return $curl->getResponse();
        } catch (\RuntimeException $ex) {
            \Log::error('api/dls/vehicle/dictionary-stages', ['error' => $ex->getMessage(), 'code' => $ex->getCode(), $data]);
            return json_encode(['error' => $ex->getMessage(), 'code' => $ex->getCode()]);
        }
    }

    public function syncDictionaryStatuses()
    {
        $data = $this->data;

        $stages = \InjurySteps::get()->toArray();
        $data['statuses'] = json_encode( $stages );

        $curl = $this->curl->post(Config::get('webconfig.SYJON_URL').'/api/dls/vehicle/dictionary-statuses', $data);

        try {
            return $curl->getResponse();
        } catch (\RuntimeException $ex) {
            \Log::error('api/dls/vehicle/dictionary-statuses', ['error' => $ex->getMessage(), 'code' => $ex->getCode(), $data]);
            return json_encode(['error' => $ex->getMessage(), 'code' => $ex->getCode()]);
        }
    }
}
