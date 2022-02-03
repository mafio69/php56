<?php

namespace Idea\LeasingAgreements\InprogressAgreement;


use Config;
use DB;
use Log;
use Session;

class ImportInprogress {

    function import($filename)
    {
        set_time_limit(500);
        $file = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/insurances/collection/'.$filename;
        if(($handle = fopen($file, 'r')) !== false)
        {
            // get the first row, which contains the column-titles (if necessary)
            $header = fgetcsv($handle);
            $countLine = 0;
            $parsedLines = 0;
            // loop through the file line-by-line
            $start = microtime(true);
            $insertDataGroup = array();
            DB::disableQueryLog();
            Session::set('avoid_query_logging', true);
            while(($data = fgetcsv($handle)) !== false)
            {
                $countLine++;
                $parsedLines++;
                $data = array_map('trim', $data);
                $insertData = array(
                    'no' => $data[0],
                    'nr_contract' => $data[1],
                    'nr_contract_pure' => $this->simplifyContract($data[1]),
                    'if_exist_cession' => $this->checkIfCession($data[1]),
                    'if_cession' => $this->checkIfCession($data[1]),
                    'correction' => $data[3],
                    'if_data_change' => $data[4],
                    'if_refund_contribution' => $data[5],
                    'insurance_number' => $data[6],
                    'if_continuation' => $data[7],
                    'insurance_type' => $data[8],
                    'months' => $data[9],
                    'agreement_type' => $data[10],
                    'notification_number' => $data[11],
                    'insurance_date' => $data[12],
                    'date_from' => $data[13],
                    'date_to' => $data[14],
                    'insurance_company' => $data[15],
                    'client_name' => $data[16],
                    'client_address' => $data[17],
                    'client_REGON' => $data[18],
                    'client_NIP' => $this->simplifyNIP($data[19]),
                    'owner' => $data[20],
                    'owner_address' => $data[21],
                    'owner_post' => $data[22],
                    'owner_city' => $data[23],
                    'owner_REGON' => $data[24],
                    'owner_NIP' => $data[25],
                    'agreement_payment_way' => $data[26],
                    'loan_net_value' => $data[27],
                    'net_gross' => $data[28],
                    'contribution' => $data[29],
                    'rate' => $data[30],
                    'rate_vbl' => $data[31],
                    'refund' => $data[32],
                    'agreement_insurance_group' => $data[34],
                    'if_load_decision' => $data[35]
                );
                unset($data);

                $insertDataGroup[] = $insertData;

                unset($insertData);

                if($parsedLines == 200) {

                    DB::table('leasing_agreement_imports')->insert($insertDataGroup);

                    unset($insertDataGroup);
                    $parsedLines = 0;

                    Log::info($countLine.' - '.round(memory_get_usage(true)/1024,2)." kilobytes");
                }

            }

            if($parsedLines > 0 ) {
                DB::table('leasing_agreement_imports')->insert($insertDataGroup);
                unset($insertDataGroup);
                Log::info($countLine.' - '.round(memory_get_usage(true)/1024,2)." kilobytes");
            }

            $time_elapsed_us = microtime(true) - $start;

            fclose($handle);
            Session::set('avoid_query_logging', false);

        }
        $result['lines'] = $countLine;
        $result['time_elapsed'] = $time_elapsed_us;
        return $result;
    }

    private function checkIfCession($nr_contract)
    {
        $eploded_contract = explode('/', $nr_contract);
        if(strtoupper(end($eploded_contract)) == 'CESJA') {
            unset($eploded_contract);
            return 1;
        }

        unset($eploded_contract);
        return 0;
    }

    private function simplifyNIP($NIP)
    {
        $NIP = str_replace(' ', '', $NIP);
        return str_replace('-', '', $NIP);
    }

    private function simplifyContract($nr_contract)
    {
        $exploded_contract = explode('/', $nr_contract);
        if(strtoupper(end($exploded_contract)) == 'CESJA') {
            $keys = array_keys($exploded_contract);
            unset($exploded_contract[end($keys)]);
            return implode('/', $exploded_contract);
        }

        return $nr_contract;
    }

}