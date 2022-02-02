<?php

ini_set("soap.wsdl_cache_enabled", 0);

class GETVEHICLEDTAInput
{
    function GETVEHICLEDTAInput($contract, $regnumber, $username)
    {
        $this->CONTRACT = $contract;
        $this->REGNUMBER = $regnumber;
        $this->USERNAME = $username;
    }
}


class REGINSISSUEInput
{
	
	function REGINSISSUEInput($contract, $issuedate, $issuenumber, $issuetype, $username) 
	{
		$this->CONTRACT = $contract;
        $this->ISSUEDATE = $issuedate;
        $this->ISSUENUMBER = $issuenumber;
        $this->ISSUETYPE = $issuetype;
        $this->USERNAME = $username;
	}

}

class CHGISSUETYPEInput
{
	function CHGISSUETYPEInput($issuenumber, $issuetype, $username) 
	{
        $this->ISSUENUMBER = $issuenumber;
        $this->ISSUETYPE = $issuetype;
        $this->USERNAME = $username;
	}
}


class ADDISSUEFEEInput
{
	function ADDISSUEFEEInput($issuenumber, $feeamount, $username) 
	{
        $this->ISSUENUMBER = $issuenumber;
        $this->FEEAMOUNT = $feeamount;
        $this->USERNAME = $username;
	}
}


class CLOSEISSUEInput
{
	function CLOSEISSUEInput($issuenumber, $closecode, $username) 
	{
        $this->ISSUENUMBER = $issuenumber;
        $this->CLOSECODE = $closecode;
        $this->USERNAME = $username;
	}
}

class REOPENISSUEInput
{
    function REOPENISSUEInput($issuenumber, $comment, $username)
    {
        $this->ISSUENUMBER = $issuenumber;
        $this->COMMENT = $comment;
        $this->USERNAME = $username;
    }
}


$client = new SoapClient(Config::get('webconfig.WEBCONFIG_API_wsdl'),
	array(
		'location' => Config::get('webconfig.WEBCONFIG_API_location'),
		'soap_version'   => SOAP_1_2, 
		'login' => Config::get('webconfig.WEBCONFIG_API_login'), 
		'password' => Config::get('webconfig.WEBCONFIG_API_password')
	));



function logISDL($params, $function, $xml){
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

	$data = "~".date("Y-m-d, H:i:s",time())."~".$_SERVER['REMOTE_ADDR']."~".gethostbyaddr($_SERVER['REMOTE_ADDR'])."~".Auth::user()->login."~".Auth::user()->name."\n";

	$data .= "\t funkcja: ".$function."\n";

	$data .= "\t parametry: ";
	foreach ($params as $k => $v) {
		$data .= $v.", ";
	}
	$data .= "\n";

	$data .= "\t odpowiedź: ";
	$data .= "errorCode -> ".$xml->ErrorCde."; errorDes -> ".$xml->ErrorDes;
	$data .= "\n\n";

	fwrite(fopen(Config::get('webconfig.WEBCONFIG_LOGS_FOLDER')."/".$logDir."/isdl.log","a"),$data);
}

?>