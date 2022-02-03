<?php

use Illuminate\Console\Command;

class GenerateGeneralSapReport extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'system:generate-general-sap-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $filename = 'raport_sap_'.\Carbon\Carbon::now()->format('Y_m_d');
        Excel::create($filename, function($excel) {
            $excel->sheet('Export', function($sheet) {

                $sheet->appendRow([
                    'nr umowy leasingu z SAP',
                    'rok umowy leasingu z SAP',
                    'nr szkody powiązanej CAS',
                    'data wygenerowania komunikatu'
                ]);

                InjurySapResponse::whereDate('created_at', '<', \Carbon\Carbon::now()->format('Y-m-d'))
                    ->where('kod', '065')
                    ->with('entity', 'entity.injury')
                    ->chunk(100, function($responses) use (&$sheet){
                        foreach($responses as $response) {
                            $sheet->appendRow(array(
                                $response->entity->nrum,
                                $response->entity->rokum,
                                $response->entity->injury ? $response->entity->injury->case_nr : '',
                                $response->created_at->format('Y-m-d H:i')
                            ));
                        }
                    });
            });

        })->store('xls', Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER').'/reports/sap', true);

        InjurySapReport::create([
            'filename' => $filename.'.xls',
            'report_date' => \Carbon\Carbon::now()->format('Y-m-d'),
        ]);

    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
        );
    }


}
