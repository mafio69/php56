<?php

namespace Idea\Vmanage\Imports;


use Idea\AddressParser\AddressParser;
use VmanageVehicle;
use VmanageVehicleHistory;

class ImportTruckPartial
{
    protected $owners;
    protected $addressParser;
    protected $rows;

    public function fire($job, $data)
    {
        \Log::info('fired');
        $importer = new ImportTruck();

        $rows = $data['rows'];

        foreach ($rows as $row)
        {
            $importer->parseRow($row);
        }

        if($data['import_id'])
        {
            $import_id = $data['import_id'];
            $import = \VmanageImport::find($import_id);
            $import->update(['parsed' => date('Y-m-d H:i:s')]);
            \Log::info('finished');
        }

        $job->delete();
    }

}
