<?php
/**
 * Created by PhpStorm.
 * User: przemek
 * Date: 29.12.14
 * Time: 11:36
 */

namespace Idea\Vmanage\Imports;


use Config;
use Excel;
use VipClient;

class VipClientsImport {

    private $file;
    public $msg = '';
    private $vip_import;

    function __construct($import)
    {
        $this->file = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/imports/vip_clients/'.($import->filename);
        $this->vip_import = $import;
    }

    public function import()
    {
        if(file_exists($this->file)) {
            $sheet = Excel::load($this->file, function($reader){
              })->getActiveSheet();
            $highest_row = $sheet->getHighestRow();
            $rows = $sheet->rangeToArray('A1:A'.$highest_row, NULL, TRUE, FALSE);

            foreach($rows as $row){
              $registration = $row[0];

              $vip = VipClient::where('registration', $registration)->first();
              if(! $vip) {
                  VipClient::create(['registration' => $registration, 'vip_clients_import_id' => $this->vip_import->id]);
              }
            }

            $this->msg = "Wgrano numery rejestracyjne.";
            return true;
        }

        $this->msg = "Błąd odczytu pliku, skontaktuj się z administratorem.";
        return false;
    }



}
