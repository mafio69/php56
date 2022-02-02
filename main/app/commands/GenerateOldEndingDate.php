<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class GenerateOldEndingDate extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'debug:generate-old-ending-dates';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';

	protected $matched = 0;

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

        Injury::whereIn('step', [
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
        ])->chunk(1000, function($injuries) use(&$i)
        {
            $injuries->load([
                'historyEntries' => function($query){
                    $query->orderBy('id', 'desc');
                },
                'documents' => function($query){
                    $query->where(function($query){
                        $query->where(function ($query){
                            $query->where('document_type', 'InjuryDocumentType')->whereIn('document_id', [26, 2, 32, 72, 7, 68, 3]);
                        });
                        $query->orWhere(function ($query) {
                            $query->where('document_type', 'InjuryUploadedDocumentType')->whereIn('document_id', [6, 7 ,27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37]);
                        });
                    });

                    $query->orderBy('id', 'desc');
                }
            ]);

            foreach ($injuries as $injury)
            {
                if(!$injury->date_end) {
                    $ending_date = $this->endDate($injury);

                    if ($ending_date) {
                        $injury->update(['date_end' => $ending_date]);
                        $this->matched++;
                    }
                }
            }
            $i += 1000;
            $this->info($i);
        });

        $this->info('matched: '.$this->matched);
	}

    private function endDate($injury){
        $end_date=null;
        $histories = $injury->historyEntries->toArray();
        $helper = array(
            '-10'=>29,
            '-7' => 142,
            15=>114,
            16=>163,
            17=>115,
            18=>164,
            21=>163,
            23=>174,
            //24=>173,
            24=>117,
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
            'Na STATUS - rozliczona EDB',
            'Na STATUS - zakończona - wystawiono upoważnienie',
            'Na STATUS - zakończona - odmową ZU',
            'Na STATUS - zakończona',
            'Na STATUS - Szkoda całkowita zakończona wypłatą',
            'Na STATUS - Szkoda całkowita zakończona odmową',
            'Na STATUS - Szkoda całkowita rezygnacja z roszczeń',
            'Na STATUS - Szkoda całkowita – umowa rozliczona',
            'Na STATUS - Szkoda całkowita ? umowa rozliczona',
            'Na STATUS - Kradzież zakończona wypłatą',
            'Na STATUS - Kradzież zakończona odmową',
            'Na STATUS - zakończone totalnie'
        ];

        foreach($histories as $history)
        {
            if( in_array($history['history_type_id'], $helper) ){
                return $history['created_at'];
            }elseif($history['history_type_id'] == 140 && in_array($history['value'], $contents)){
                return $history['created_at'];
            }elseif($history['history_type_id'] == 128 && $history['value'] == 'Przeniesiono na etap zakończone w trakcie grupowej zmiany statusów.'){
                return $history['created_at'];
            }
        }

        if($injury->step == '-7')
        {
            if($injury->user_id == 1)
            {
                return $injury->created_at;
            }
        }

        if($injury->step == '23')
        {
            foreach($injury->documents as $document)
            {
                if($document->document_type == 'InjuryDocumentType')
                    return $document->created_at;
            }

        }

        if($injury->step == '15')
        {
            foreach($injury->documents as $document)
            {
                if($document->document_type == 'InjuryUploadedDocumentType' && $document->document_id == 6)
                    return $document->created_at;
            }

        }

        if($injury->step == '24')
        {
            foreach($injury->documents as $document)
            {
                if($document->document_type == 'InjuryUploadedDocumentType' && in_array($document->document_id, [ 7 ,27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37]) )
                    return $document->created_at;
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
