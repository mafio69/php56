<?php namespace Idea\Webservice;

use Auth;
use Config;
use Exception;
use Idea\Exceptions\SoapErrorException;
use Idea\Structures\StructuresInterface;
use Owners;
use SimpleXMLElement;
use SoapClient;

class Webservice {

    private $dtaStructure;

    private $client;

    private $owner;

    private $params;

    private $response;

    /**
     * @return mixed
     */
    public function getParams()
    {
        return $this->params;
    }

    public function generateParameters(StructuresInterface $dtaStructure)
    {
        $this->dtaStructure = $dtaStructure;

        $this->params = array(
            "args0" => $this->dtaStructure,
        );
        return $this;
    }

    public function establishSoap($owner_id)
    {
        $this->owner = Owners::find($owner_id);
        $this->client = new SoapClient($this->owner->wsdl,
            array(
                'location' => $this->owner->wsdl_location,
                'soap_version' => SOAP_1_2,
                'login' => $this->owner->wsdl_login,
                'password' => $this->owner->wsdl_password
            )
        );

        return $this;

    }

    public function callSoap($function_name)
    {
        $this->response = $this->client->__soapCall($function_name, array($this->params));

        $this->logISDL($function_name);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return mixed
     */
    public function getResponseXML()
    {
        if( isset($this->response->return->ANSWER) )
            $xml = $this->response->return->ANSWER;
        else $xml = $this->response->return;

        $xml = preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $xml);

        $xml2 = new SimpleXMLElement($xml);

        return $xml2;
    }

    public function logISDL($function_name){

        $params = $this->dtaStructure->getStructure();

        $dateNow = explode('-',date("Y-m-d-H-i-s"));
        $dateNow = array(
            'year' => $dateNow[0],
            'month' => $dateNow[1],
            'day' => $dateNow[2],
            'hour' => $dateNow[3],
            'minute' => $dateNow[4],
            'second' => $dateNow[5]
        );

        $logDir = $dateNow['year'].'-'.$dateNow['month'].'/';
        if(!is_dir(Config::get('webconfig.WEBCONFIG_LOGS_FOLDER').'/'.$logDir)){mkdir(Config::get('webconfig.WEBCONFIG_LOGS_FOLDER').'/'.$logDir,0777,true);}

        $data = "~".date("Y-m-d, H:i:s",time())."~".$_SERVER['REMOTE_ADDR']."~".gethostbyaddr($_SERVER['REMOTE_ADDR'])."~".(Auth::check() ? Auth::user()->login."~".Auth::user()->name : 'unlogin user')."\n";

        $data .= "\t wsdl: ".$this->owner->wsdl."\n";
        $data .= "\t funkcja: ".$function_name."\n";

        $data .= "\t parametry: ";
        foreach ($params as $k => $v) {
            $data .= $k.' -> '.$v.", ";
        }
        $data .= "\n";

        $data .= "\t odpowiedź: ";
        if($function_name == 'chkcontstate_XML')
            $data .= "errorCode -> ".$this->getResponseXML()->ANSWER->chkContStateReturn->Error->ErrorCde."; errorDes -> ".$this->getResponseXML()->ANSWER->chkContStateReturn->Error->ErrorDes->__toString();
        else if($function_name == 'getvehicledta_XML')
            $data .= "errorCode -> ".$this->getResponseXML()->ANSWER->getVehicleDataReturn->Error->ErrorCde."; errorDes -> ".$this->getResponseXML()->ANSWER->getVehicleDataReturn->Error->ErrorDes->__toString();
        else
            $data .= "errorCode -> ".$this->getResponseXML()->Error->ErrorCde."; errorDes -> ".$this->getResponseXML()->Error->ErrorDes->__toString();

        $data .= "\n\n";

        fwrite(fopen(Config::get('webconfig.WEBCONFIG_LOGS_FOLDER')."/".$logDir."/isdl.log","a"),$data);
    }

    /**
     * @return mixed
     */
    public function getClient()
    {
        return $this->client;
    }


} 