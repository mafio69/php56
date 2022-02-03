<?php

namespace Idea\Logging\LeasingAgreements;


use Idea\Logging\LeasingAgreements\Translations\AgreementTranslator;
use Idea\Logging\LeasingAgreements\Translations\ClientTranslator;
use Idea\Logging\LeasingAgreements\Translations\InsuranceTranslator;
use Idea\Logging\LeasingAgreements\Translations\ObjectTranslator;

class Logger {

    private $historyType;
    private $dataToCompare;
    private $history_id;
    private $leasingAgreement_id;


    /**
     * Logger constructor.
     * @param $historyType
     * @param $dataToCompare
     * @param $history_id
     */
    public function __construct($historyType, $dataToCompare, $history_id, $leasingAgreement_id)
    {
        $this->historyType = $historyType;
        $this->dataToCompare = $dataToCompare;
        $this->history_id = $history_id;
        $this->leasingAgreement_id = $leasingAgreement_id;

        $this->log();
    }

    public function log()
    {
        $historyType = \LeasingAgreementHistoryType::find($this->historyType);

        if($historyType->log_changes == 0)
        {
            return null;
        }
        $currentState = $this->generateLeasingAgreementCurrentState();
        $toCompare = explode(',',$historyType->to_compare);
        $logs = array();
        foreach($toCompare as $comparer)
        {
            $func = 'compare'.ucfirst($comparer);
            if(method_exists($this, $func))
            {
                $log = $this->$func();
                if(count($log) > 0)
                    $logs = array_merge($logs, $log);

            }
        }

        return $this->insertLogToDB($currentState, $logs);
    }

    private function generateLeasingAgreementCurrentState()
    {
        $leasingAgreement = \LeasingAgreement::find($this->leasingAgreement_id);
        if(!$leasingAgreement->insurances->isEmpty())
            $currentInsurance = $leasingAgreement->insurances()->active()->first();
        else
            $currentInsurance = null;
        $objects = $leasingAgreement->objects->lists('name');
        $client = $leasingAgreement->client;

        $currentState = [
            'object_name' => implode(";\n", $objects),
            'nr_contract' => $leasingAgreement->nr_contract,
            'client_name' => $client->name,
            'client_address' => $client->registry_street.' '.$client->registry_post.' '.$client->registry_city,
            'client_NIP' => $client->NIP,
            'client_REGON' => $client->REGON,
            'loan_value' => ($leasingAgreement->net_gross == 1) ? $leasingAgreement->loan_net_value : $leasingAgreement->loan_gross_value,
            'net_gross' => ($leasingAgreement->net_gross == 1) ? 'netto' : 'brutto',
            'rate' => checkObjectIfNotNull($currentInsurance, 'rate'),
            'contribution' => checkObjectIfNotNull($currentInsurance, 'contribution'),
            'insurance_from' => checkObjectIfNotNull($currentInsurance, 'date_from'),
            'insurance_to' => checkObjectIfNotNull($currentInsurance, 'date_to'),
            'months' => checkObjectIfNotNull($currentInsurance, 'months')
        ];

        return $currentState;
    }

    private function compareObject()
    {
        $currentObject = $this->dataToCompare['object']['current'];
        $previousObject = $this->dataToCompare['object']['previous'];
        $keys = \LeasingAgreementObject::getModel()->getFillable();

        $log = array();

        $objectTranslator = new ObjectTranslator();
        foreach($keys as $key)
        {
            if($currentObject[$key] != $previousObject[$key])
            {
                $translation = $objectTranslator->translate($key, ['old_value' => $previousObject[$key], 'new_value' => $currentObject[$key]] );
                if(!is_null($translation))
                    $log = array_merge($log, $translation);
            }
        }

        return $log;
    }

    private function compareAgreement()
    {
        $currentAgreement = $this->dataToCompare['agreement']['current'];
        $previousAgreement = $this->dataToCompare['agreement']['previous'];
        $keys = \LeasingAgreement::getModel()->getFillable();
        $log = array();
        $agreementTranslator = new AgreementTranslator();
        foreach($keys as $key)
        {
            if($currentAgreement[$key] != $previousAgreement[$key])
            {
                $translation = $agreementTranslator->translate($key, ['old_value' => $previousAgreement[$key], 'new_value' => $currentAgreement[$key] ] );
                if(!is_null($translation))
                    $log = array_merge($log, $translation);
            }
        }

        return $log;
    }

    private function compareClient()
    {
        $currentClient = $this->dataToCompare['client']['current'];
        $previousClient = $this->dataToCompare['client']['previous'];

        $keys = \Clients::getModel()->getFillable();

        $log = array();
        $agreementTranslator = new ClientTranslator();
        foreach($keys as $key)
        {
            if(isset($previousClient[$key]) && !isset($currentClient[$key]))
                $currentClient[$key] = null;
            elseif(isset($currentClient[$key]) && !isset($previousClient[$key]))
                $previousClient[$key] = null;
            elseif(!isset($currentClient[$key]) && !isset($previousClient[$key]))
            {
                $currentClient[$key] = $previousClient[$key] = null;
            }

            if($currentClient[$key] != $previousClient[$key])
            {
                $translation = $agreementTranslator->translate($key, ['old_value' => $previousClient[$key], 'new_value' => $currentClient[$key] ] );

                if(!is_null($translation))
                    $log = array_merge($log, $translation);
            }
        }
        return $log;
    }

    private function compareInsurance()
    {
        $currentInsurance = $this->dataToCompare['insurance']['current'];
        $previousInsurance = $this->dataToCompare['insurance']['previous'];

        $keys = \LeasingAgreementInsurance::getModel()->getFillable();
        $log = array();
        $insuranceTranslator = new InsuranceTranslator();
        foreach($keys as $key)
        {
            if(isset($previousInsurance[$key]) && !isset($currentInsurance[$key]))
                $previousInsurance[$key] = null;
            elseif(isset($currentInsurance[$key]) && !isset($previousInsurance[$key]))
                $previousInsurance[$key] = null;
            elseif(!isset($currentInsurance[$key]) && !isset($previousInsurance[$key]))
            {
                $currentInsurance[$key] = $previousInsurance[$key] = null;
            }

            if($currentInsurance[$key] != $previousInsurance[$key])
            {
                $translation = $insuranceTranslator->translate($key, ['old_value' => $previousInsurance[$key], 'new_value' => $currentInsurance[$key] ] );

                if(!is_null($translation))
                    $log = array_merge($log, $translation);
            }
        }
        return $log;
    }

    private function insertLogToDB($currentState, $logs)
    {
        $textLogs = $this->prepareTextLogs($logs);
        $insertRow = array_merge($currentState, $textLogs);

        $insertRow['leasing_agreement_history_id'] = $this->history_id;
        return \LeasingAgreementHistoryLog::create($insertRow)->id;
    }

    private function prepareTextLogs($logs)
    {
        $newA = array();
        $previousA = array();
        foreach($logs as $description => $log)
        {
            $newA[] = $description.": ".$log['new_value'];
            $previousA[] = $description.": ".$log['old_value'];
        }
        $log_new = implode("\n", $newA);
        $log_previous = implode("\n", $previousA);


        return ['log_new' => $log_new, 'log_previous' => $log_previous];
    }


}
