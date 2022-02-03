<?php

use Illuminate\Console\Command;

class GenerateArchiveSapError extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'system:generate-archive-sap-error';

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
        ini_set('memory_limit', '4096M');

        $path = '/media/dlsdata/dls/logs';

        $logReader = new \Idea\Logging\Parser\Reader();
        $logDates = $logReader->getLogFileDates($path);

        $regex = '(\[\{.+\}\])';

        foreach ($logDates as $logDate)
        {
            $this->info($logDate);
            foreach($logReader->get($logDate) as $log)
            {
                $subject = substr($log['message'], 0, 7);
                if($subject == 'szkoda ') {
                    preg_match($regex, $log['message'], $matches);
                    if (isset($matches[0])) {
                        $response = json_decode($matches[0], true);
                        foreach ($response as $block)
                        {
                            if(isset($block['ftReturn']) && isset($block['fsSzkodaOut'])){
                                $ftReturn = $block['ftReturn'];
                                $szkodaId = $block['fsSzkodaOut']['szkodaId'];
                                $sapEntity = InjurySapEntity::where('szkodaId', $szkodaId)->first();

                                foreach($ftReturn as $item) {
                                    $injurySapResponse = new InjurySapResponse();
                                    $injurySapResponse->injury_sap_entity_id = $sapEntity ? $sapEntity->id : null;
                                    $injurySapResponse->szkoda_id = $szkodaId;
                                    $injurySapResponse->typ = $item['typ'];
                                    $injurySapResponse->kod = $item['kod'];
                                    $injurySapResponse->message = $item['message'];
                                    $injurySapResponse->created_at = $log['timestamp'];
                                    $injurySapResponse->updated_at = \Carbon\Carbon::now();
                                    $injurySapResponse->save(['timestamps' => false]);
                                }
                            }
                        }
                    }
                }
            }
        }
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
