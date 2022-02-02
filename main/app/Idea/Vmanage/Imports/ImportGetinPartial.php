<?php

namespace Idea\Vmanage\Imports;


use Idea\AddressParser\AddressParser;
use VmanageVehicle;
use VmanageVehicleHistory;

class ImportGetinPartial
{
    protected $owners;
    protected $addressParser;
    protected $rows;

    public function fire($job, $data)
    {
        $importer = new ImportGetin();

        $rows = $data['rows'];

        foreach ($rows as $row)
        {
            \Log::info('row', $row);
            $importer->parseRow($row);
        }

        if($data['import_id'])
        {
            $import_id = $data['import_id'];
            $import = \VmanageImport::find($import_id);
            $import->update(['parsed' => date('Y-m-d H:i:s')]);
        }

        $job->delete();
    }

}