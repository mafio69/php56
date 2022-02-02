<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class GenerateEndingStepDates extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'debug:generate-ending-step-dates';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';

	protected $ending_steps = [
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
                26,
                34,
                35,
                36,
                37,
                38,
                45,
                44
            ];

    protected $total_steps = [34,35,36,37];
    protected $theft_steps = [45,46];

    protected $history_types = [
                180,            //step 34
                181,            //step 35
                179,            //step 44
                178,            //step 45
                162,            //step 14
                114,            //step 15
                174,            //step 23
                173,            //step 24
                206,            //step 26
                211,            //step 38
            ];
    protected $step_stages = [];
    protected $history_total_steps = [];
    protected $history_theft_steps = [];

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
        $this->stepStages = InjuryStepStage::has('nextInjuryStep')->whereIn('next_injury_step_id', $this->ending_steps)->lists('next_injury_step_id', 'id');

        $this->history_total_steps = History_type::where('injury_processing_type_id', 2)->lists('id');
        $this->history_theft_steps = History_type::where('injury_processing_type_id', 3)->lists('id');

        $this->info(Injury::whereNotNull('date_end')->count());
        $counter = 0;
        Injury::whereNotNull('date_end')->chunk(1000, function($injuries) use(&$counter){
            $injuries->load('historyEntries');
            foreach ($injuries as $injury)
            {
                $endingDates = $this->endDays($injury);
                if(! is_array($endingDates)) dd($endingDates, $injury->id);
                $injury->update($endingDates);
            }
            $counter+= 1000;
            $this->info($counter);
        });
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array();
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array();
	}

    private function endDays($injury)
    {
        $date_end_total = null;
        $date_end_theft = null;
        $date_end_normal = null;

        $histories = array_reverse($injury->historyEntries->toArray());

        $helper = array(
            '-10'=>29,
            //'-7' => 142,
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
            26=>206,
            38=>211
        );

        $contents = [
            '-10' => 'Na STATUS - anulowana',
            15 => 'Na STATUS - zakończona - wypłatą',
            16 => 'Na STATUS - rozliczona',
            17 => 'Na STATUS - zakończone bez likwidacji',
            18 => 'Na STATUS - rezygnacja z roszczeń',
            21 => 'Na STATUS - rozliczona asysta',
            23 => 'Na STATUS - zakończona - wystawiono upoważnienie',
            20 => 'Na STATUS - zakończona - odmową ZU',
            25 => 'Na STATUS - zakończona',
            26 => 'Na STATUS - zakończona bez asysty',
            34 => 'Na STATUS - Szkoda całkowita zakończona wypłatą',
            35 => 'Na STATUS - Szkoda całkowita zakończona odmową',
            36 => 'Na STATUS - Szkoda całkowita rezygnacja z roszczeń',
            37 => 'Na STATUS - Szkoda całkowita – umowa rozliczona',
            44 => 'Na STATUS - Kradzież zakończona wypłatą',
            45 => 'Na STATUS - Kradzież zakończona odmową'
        ];
        foreach($histories as $history){
            if( in_array($history['history_type_id'], $helper) ){
                $step_id = array_search($history['history_type_id'], $helper);
                if(in_array($step_id , $this->total_steps)){
                    if(!$date_end_total) {
                        $date_end_total = $history['created_at'];
                    }
                }elseif(in_array($step_id, $this->theft_steps)){
                    if(!$date_end_theft) {
                        $date_end_theft = $history['created_at'];
                    }
                }elseif(!$date_end_normal){
                    $date_end_normal = $history['created_at'];
                }
            }elseif($history['history_type_id'] == 140 && in_array($history['value'], $contents)){
                $history_type_id = array_search($history['value'], $contents);
                if(in_array($history_type_id, $this->total_steps)){
                    if(!$date_end_total) {
                        $date_end_total = $history['created_at'];
                    }
                }elseif(in_array($history_type_id, $this->theft_steps)){
                    if(!$date_end_theft) {
                        $date_end_theft = $history['created_at'];
                    }
                }elseif(!$date_end_normal){
                    $date_end_normal = $history['created_at'];
                }
            }elseif($history['history_type_id'] == 142){
                if($injury->prev_step){
                    if(in_array( $injury->prev_step, $this->total_steps) && !$date_end_total ){
                        $date_end_total = $history['created_at'];
                    }elseif(in_array( $injury->prev_step, $this->theft_steps) && !$date_end_theft ) {
                        $date_end_theft = $history['created_at'];
                    }
                }else{
                    foreach(array_unique(array_fetch($histories, 'history_type_id')) as $history_type_id)
                    {
                        if (!$date_end_total) {
                            if(in_array($history_type_id, $this->history_total_steps)) {
                                $date_end_total = $history['created_at'];
                            }elseif($history_type_id == 128){
                                foreach(array_where($histories, function($key, $value)
                                {
                                    return $value['history_type_id'] == 128;
                                }) as $sub_history){
                                    if( strpos( mb_strtolower( $sub_history['value'] ), 'całkow') !== false ){
                                        $date_end_total = $history['created_at'];
                                    }
                                }
                            }
                        }


                        if(!$date_end_theft) {
                            if(in_array($history_type_id, $this->history_theft_steps)) {
                                $date_end_theft = $history['created_at'];
                            }elseif($history_type_id == 128){
                                foreach(array_where($histories, function($key, $value)
                                {
                                    return $value['history_type_id'] == 128;
                                }) as $sub_history){
                                    if( strpos( mb_strtolower( $sub_history['value'] ), 'kradzi') !== false ){
                                        $date_end_theft = $history['created_at'];
                                    }
                                }
                            }
                        }
                    }

                    if(!$date_end_total && !$date_end_theft){
                        dd($injury->id);
                    }
                }
            }
        }

        return [
            'date_end_total' => $date_end_total,
            'date_end_theft' => $date_end_theft,
            'date_end_normal' => $date_end_normal
        ];
    }

}
