<?php

class CronController extends BaseController {

    public function updateStatuses()
    {
        \Debugbar::disable();
        if( in_array( $_SERVER['REMOTE_ADDR'], Config::get('definition.allowedIP')) ){

            $injuries = Injury::whereIn('step', array(0, 10))->whereActive(0)->get();

            $content = "~".date("Y-m-d, H:i:s",time())."~".$_SERVER['REMOTE_ADDR']."~".gethostbyaddr($_SERVER['REMOTE_ADDR'])." \n";
            $content .= "\t wywołanie crona na funkcji updateStatuses \n";
            custom_log(Config::get('webconfig.WEBCONFIG_LOGS_FOLDER'), 'cron', $content);

            foreach( $injuries as $injury ) {
                $vehicle = Vehicles::find($injury->vehicle_id);

                $CONTRACT = $vehicle->nr_contract;
                $REGNUMBER = $vehicle->registration;
                $USERNAME = 'cron';

                $data = new Idea\Structures\GETVEHICLEDTAInput($CONTRACT, $REGNUMBER, $USERNAME);
                $webservice = Webservice::establishSoap($vehicle->owner_id)->generateParameters($data)->callSoap('getvehicledta_XML');
                $xml = $webservice->getResponseXML();
                if ($xml && $xml != '') {

                    if ($xml->ANSWER->getVehicleDataReturn->Error->ErrorCde != 'ERR0000') {
                        $content = "\t wystąpił błąd dla pojazdu o id ->" . $vehicle->id . " : " . $xml->ANSWER->getVehicleDataReturn->Error->ErrorDes . "'\n";
                        custom_log(Config::get('webconfig.WEBCONFIG_LOGS_FOLDER'), 'cron', $content);
                    } else {
                        $status = $xml->ANSWER->getVehicleDataReturn->getVehicle->contract->status;

                        if (mb_strtoupper($vehicle->contract_status, 'UTF-8') != mb_strtoupper($status)) {

                            $content = "\t zmiana statusu pojazdu o id ->" . $vehicle->id . " z '" . $vehicle->contract_status . "' na '" . $status . "'\n";
                            custom_log(Config::get('webconfig.WEBCONFIG_LOGS_FOLDER'), 'cron', $content);

                            $vehicle->contract_status = $status;
                            $vehicle->touch();
                            $vehicle->save();

                        }
                    }
                }

            }

            $content = "\n\n";
            custom_log(Config::get('webconfig.WEBCONFIG_LOGS_FOLDER'), 'cron', $content);

        }else{
            $content = "~".date("Y-m-d, H:i:s",time())."~".$_SERVER['REMOTE_ADDR']."~".gethostbyaddr($_SERVER['REMOTE_ADDR'])." \n";
            $content .= "\t Próba wywołania crona na funkcji updateStatuses z nieuprawnionego adresu IP \n\n";
            custom_log(Config::get('webconfig.WEBCONFIG_LOGS_FOLDER'), 'cron', $content);
        }

    }


}
