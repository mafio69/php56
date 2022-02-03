<?php
namespace Idea\AsService;
use Auth;
use Idea\Structures\CHGISSUETYPEInput;
use Idea\Structures\GETVEHICLEDTAInput;
use Idea\Structures\REGINSISSUEInput;
use Injury;
use InjuryWreck;
use REOPENISSUEInput;
use Session;
use URL;
use Webservice;

class AsService {

    public static function theft($id){
        $injury = Injury::find($id);

        $contract = $injury->vehicle->nr_contract;
        $issuedate = $injury->date_event;
        $issuenumber = $injury->case_nr;
        $issuetype = 'K';
        $username = substr(Auth::user()->login, 0, 10);

        $owner_id = $injury->vehicle->owner_id;

        if($injury->vehicle->owner->wsdl != '' && $injury->vehicle->register_as == 1) {
            if ($injury->step == 0 && ( is_null($injury->prev_step) || $injury->prev_step == '-10')) {
                //jeśli zmiana statusu następuje z poziomu szkód nowych i szkoda nie była jeszcze rejestrowana w As
                $data = new REGINSISSUEInput($contract, $issuedate, $issuenumber, $issuetype, $username);

                $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('reginsissue');

                $xml = $webservice->getResponseXML();
            } else {
                //zmiana kwalifikacja szkody
                $data = new CHGISSUETYPEInput($issuenumber, $issuetype, $username);

                $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('chgissuetype');

                $xml = $webservice->getResponseXML();
            }
        }

        if( $injury->vehicle->owner->wsdl == '' || $injury->vehicle->register_as == 0 || $xml->Error->ErrorCde == 'ERR0000' || $xml->Error->ErrorCde == 'ERR0010' ){

            if($injury->vehicle->owner->wsdl != ''  && $injury->vehicle->register_as == 1 && $xml->Error->ErrorCde == 'ERR0010' ){
                //jeśli szkoda nie jest jednak zarejestrowana w AS, następuje jej zarejestrowanie
                $data = new REGINSISSUEInput($contract,$issuedate, $issuenumber, $issuetype, $username);

                $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('reginsissue');

                $xml = $webservice->getResponseXML();

                if( $xml->Error->ErrorCde != 'ERR0000'){
                    //jeśli nie udało się zarejestrować szkody
                    $result['code'] = 2;
                    $result['error'] = $xml->Error->ErrorDes->__toString();
                    return $result;
                }
            }

            if( $injury->save() ){
                Session::put('last_injury', $id);
                Session::put('last_injury_case_nr', $injury->case_nr);

                //zaktualizowanie statusu pojazdu
                if($injury->vehicle->owner->wsdl != ''  && $injury->vehicle->register_as == 1) {
                    $data = new GETVEHICLEDTAInput($contract, $injury->vehicle->registration, $username);
                    $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('getvehicledta_XML');
                    $xml = $webservice->getResponseXML();
                    if ($xml->ANSWER->getVehicleDataReturn->Error->ErrorCde != 'ERR0000') {
                        $result['code'] = 2;
                        $result['error'] = $xml->ANSWER->getVehicleDataReturn->Error->ErrorDes->__toString();
                        $result['url'] = URL::route('injuries-theft');
                        return $result;
                    }
                    $status = $xml->ANSWER->getVehicleDataReturn->getVehicle->contract->status;
                }else{
                    $status = "KRADZIEŻ";
                }

                $vehicle = $injury->vehicle;
                $vehicle->contract_status = $status;
                $vehicle->touch();
                $vehicle->save();

                $result['code'] = 1;
                $result['url'] = URL::route('injuries-theft');
                return $result;
            }
        }elseif($xml->Error->ErrorCde == 'ERR0006'){
            $ISSUENUMBER = $injury->case_nr;
            $COMMENT = '';
            $USERNAME = substr(Auth::user()->login, 0, 10);
            $data = new REOPENISSUEInput($ISSUENUMBER, $COMMENT, $USERNAME);

            $owner_id = $injury->vehicle->owner_id;

            $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('reopenissue');
            $xml = $webservice->getResponseXML();
            if ($xml->Error->ErrorCde != 'ERR0000'  ) {
                if($xml->Error->ErrorCde ==  'ERR0014'){
                    $data = new CHGISSUETYPEInput($ISSUENUMBER, $issuetype,$USERNAME);
                    $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('chgissuetype');
                    $xml = $webservice->getResponseXML();
                    if ($xml->Error->ErrorCde != 'ERR0000'  ) {
                        $result['code'] = 2;
                        $result['error'] = $xml->Error->ErrorDes->__toString();
                        $result['ErrorCde'] = $xml->Error->ErrorCde->__toString();
                        $error=true;
                        //return json_encode($result);
                    }
                }else {
                    $result['code'] = 2;
                    $result['error'] = $xml->Error->ErrorDes->__toString();
                    $result['ErrorCde'] = $xml->Error->ErrorCde->__toString();
                    $error=true;
                    //return json_encode($result);
                }
            }
        }else{
            $result['code'] = 2;
            $result['error'] = $xml->Error->ErrorDes->__toString();
            return $result;
        }
    }

    public static function total($id){
        $injury = Injury::find($id);

        $contract = $injury->vehicle->nr_contract;
        $issuedate = $injury->date_event;
        $issuenumber = $injury->case_nr;
        $issuetype = 'C';
        $username = substr(Auth::user()->login, 0, 10);

        $owner_id = $injury->vehicle->owner_id;
        if($injury->vehicle->owner->wsdl != '' && $injury->vehicle->register_as == 1) {
            if ($injury->step == 0 && ( is_null($injury->prev_step) || $injury->prev_step == '-10')) {
                //jeśli zmiana statusu następuje z poziomu szkód nowych i szkoda nie była jeszcze rejestrowana w As
                $data = new REGINSISSUEInput($contract, $issuedate, $issuenumber, $issuetype, $username);

                $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('reginsissue');

                $xml = $webservice->getResponseXML();

            } else {
                //zmiana kwalifikacja szkody
                $data = new CHGISSUETYPEInput($issuenumber, $issuetype, $username);

                $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('chgissuetype');

                $xml = $webservice->getResponseXML();
            }
        }

        if( $injury->vehicle->owner->wsdl == '' || $injury->vehicle->register_as == 0 || $xml->Error->ErrorCde == 'ERR0000' || $xml->Error->ErrorCde == 'ERR0010' ) {

            if ($injury->vehicle->owner->wsdl != '' && $injury->vehicle->register_as == 1 && $xml->Error->ErrorCde == 'ERR0010') {
                //jeśli szkoda nie jest jednak zarejestrowana w AS, następuje jej zarejestrowanie
                $data = new REGINSISSUEInput($contract, $issuedate, $issuenumber, $issuetype, $username);

                $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('reginsissue');

                $xml = $webservice->getResponseXML();

                if ($xml->Error->ErrorCde != 'ERR0000') {
                    //jeśli nie udało się zarejestrować szkody
                    $result['code'] = 2;
                    $result['error'] = $xml->Error->ErrorDes->__toString();
                    return $result;
                }
            }

            if ($injury->save()) {
                Session::put('last_injury', $id);
                Session::put('last_injury_case_nr', $injury->case_nr);

                if ($injury->vehicle->owner->wsdl != '' && $injury->vehicle->register_as == 1) {
                    //zaktualizowanie statusu pojazdu
                    $data = new GETVEHICLEDTAInput($contract, $injury->vehicle->registration, $username);
                    $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('getvehicledta_XML');
                    $xml = $webservice->getResponseXML();
                    if ($xml->ANSWER->getVehicleDataReturn->Error->ErrorCde != 'ERR0000') {
                        $result['code'] = 2;
                        $result['error'] = $xml->ANSWER->getVehicleDataReturn->Error->ErrorDes->__toString();
                        $result['url'] = URL::route('injuries-total');
                        return $result;
                    }
                    $status = $xml->ANSWER->getVehicleDataReturn->getVehicle->contract->status;
                } else
                    $status = "CAŁKOWITA";

                $vehicle = $injury->vehicle;
                $vehicle->contract_status = $status;
                $vehicle->touch();
                $vehicle->save();

                $result['code'] = 1;
                $result['url'] = URL::route('injuries-total');
                return $result;
            }
        }elseif($xml->Error->ErrorCde == 'ERR0006'){
            $ISSUENUMBER = $injury->case_nr;
            $COMMENT = '';
            $USERNAME = substr(Auth::user()->login, 0, 10);
            $data = new REOPENISSUEInput($ISSUENUMBER, $COMMENT, $USERNAME);

            $owner_id = $injury->vehicle->owner_id;

            $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('reopenissue');
            $xml = $webservice->getResponseXML();
            if ($xml->Error->ErrorCde != 'ERR0000'  ) {
                if($xml->Error->ErrorCde ==  'ERR0014'){
                    $data = new CHGISSUETYPEInput($ISSUENUMBER, $issuetype,$USERNAME);
                    $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('chgissuetype');
                    $xml = $webservice->getResponseXML();
                    if ($xml->Error->ErrorCde != 'ERR0000'  ) {
                        $result['code'] = 2;
                        $result['error'] = $xml->Error->ErrorDes->__toString();
                        $result['ErrorCde'] = $xml->Error->ErrorCde->__toString();
                        $error=true;
                        //return json_encode($result);
                    }
                }else {
                    $result['code'] = 2;
                    $result['error'] = $xml->Error->ErrorDes->__toString();
                    $result['ErrorCde'] = $xml->Error->ErrorCde->__toString();
                    $error=true;
                    //return json_encode($result);
                }
            }
        }else{
            $result['code'] = 2;
            $result['error'] = $xml->Error->ErrorDes->__toString();
            return $result;
        }
    }

    public static function checkStatus(){

    }
}