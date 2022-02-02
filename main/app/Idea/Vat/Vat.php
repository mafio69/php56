<?php


namespace Idea\Vat;


use Config;
use Idea\Synchronization\Curl;

class Vat
{

    private $data = [];
    private $curl;
    /**
     * Vat constructor.
     */
    public function __construct()
    {
        $this->data['token'] =  Config::get('webconfig.VAT_API_TOKEN');
    }

    public function checkClient($nip)
    {
        $this->data['nip[]'] = $nip;

        $query = http_build_query($this->data);

        $this->curl = new Curl(Config::get('webconfig.VAT_API_URL') . '/api/v2/accounts?' . $query, [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ]);
        try {
            return $this->curl->getResponse();
        } catch (\RuntimeException $ex) {
            return json_encode(['error' => $ex->getMessage(), 'code' => $ex->getCode()]);
        }
    }

    public function checkNip($nip)
    {
        $this->data['nip'] = $nip;

        $query = http_build_query($this->data);

        $this->curl = new Curl(Config::get('webconfig.VAT_API_URL').'/api/v1/checkNIP?'.$query,[
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ]);

        try {
            return $this->curl->getResponse();
        } catch (\RuntimeException $ex) {
            return json_encode(['error' => $ex->getMessage(), 'code' => $ex->getCode()]);
        }
    }

    public function getCurl()
    {
        return $this->curl;
    }
}
