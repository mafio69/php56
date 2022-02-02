<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class GenerateEndingDate extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'debug:generate-ending-dates';

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
	    $i = 0;
        //DB::table('injury')->update(['date_end' => null]);

        Injury::with('historyEntries')->chunk(1000, function($injuries) use(&$i)
        {
            foreach ($injuries as $injury)
            {
                if(!$injury->date_end) {
                    $ending_date = $this->endDate($injury);

                    if ($ending_date) {
                        if (in_array($injury->step, [
                            '-10',
                            '-7',
                            15,
                            16,
                            17,
                            18,
                            21,
                            23,
                            24,
                            25,
                            34,
                            35,
                            36,
                            37,
                            45,
                            44
                        ])) {
                            $injury->update(['date_end' => $ending_date]);
                        }
                    }
                }
            }
            $i += 1000;
            $this->info($i);
        });
	}

    private function endDate($injury){
        $end_date=null;
        $histories = array_reverse($injury->historyEntries->toArray());
        $helper = array(
            '-10'=>29,
            '-7' => 142,
            15=>114,
            16=>163,
            17=>115,
            18=>164,
            21=>163,
            23=>174,
            24=>173,
            25=>74,
            34=>180,
            35=>181,
            36=>182,
            37=>183,
            45=>178,
            44=>179,

        );

        $contents = [
            'Na STATUS - anulowana',
            'Na STATUS - zakończona - wypłatą',
            'Na STATUS - rozliczona',
            'Na STATUS - zakończone bez likwidacji',
            'Na STATUS - rezygnacja z roszczeń',
            'Na STATUS - rozliczona asysta',
            'Na STATUS - zakończona - wystawiono upoważnienie',
            'Na STATUS - zakończona - odmową ZU',
            'Na STATUS - zakończona',
            'Na STATUS - Szkoda całkowita zakończona wypłatą',
            'Na STATUS - Szkoda całkowita zakończona odmową',
            'Na STATUS - Szkoda całkowita rezygnacja z roszczeń',
            'Na STATUS - Szkoda całkowita – umowa rozliczona',
            'Na STATUS - Kradzież zakończona wypłatą',
            'Na STATUS - Kradzież zakończona odmową'
        ];

        foreach($histories as $history)
        {
            if( in_array($history['history_type_id'], $helper) ){
                return $history['created_at'];
            }elseif($history['history_type_id'] == 140 && in_array($history['value'], $contents)){
                return $history['created_at'];
            }
        }

        return $end_date;
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
