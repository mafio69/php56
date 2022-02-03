<?php


namespace Idea\SyjonService;


class Curl
{
    public function post($url, $data)
    {
        return new \Idea\Synchronization\Curl($url,[
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ]);
    }
}