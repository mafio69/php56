<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class DebugOtherReport extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'debug:other-report';

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
        set_time_limit(500);

        Excel::create('debug', function($excel) {

            $excel->sheet('Export', function($sheet) {

                $date_from = $this->parseDate('2015-01-01', '0');
                $date_to = $this->parseDate('2020-03-23', '+1');

                $sheet->appendRow($this->generateTheads());

                \LeasingAgreement::
                whereBetween('created_at', array($date_from, $date_to))
                    ->where(function($query){
                        $query->where('import_insurance_company', 'like', '%obc%')
                            ->orWhereHas('insurances', function($query){
                                $query->where('if_foreign_policy', 1);
                            });
                    })
                    ->with('client', 'insurances', 'insurances.insuranceCompany', 'withCurrentResume', 'withArchiveResume')
                    ->chunk(100, function($agreements) use (&$sheet){
                        foreach ($agreements as $k => $leasingAgreement) {
                            $status = '';
                            if($leasingAgreement->insurances->isEmpty() && is_null($leasingAgreement->withdraw)){
                                $status = 'nowe';
                            }elseif(is_null($leasingAgreement->withdraw) && !is_null($leasingAgreement->archive)){
                                $status = 'archiwum';
                            }elseif( !is_null($leasingAgreement->withdraw) ){
                                $status = 'wycofana';
                            }elseif($leasingAgreement->insurances->count() > 0 && is_null($leasingAgreement->withdraw) && is_null($leasingAgreement->archive) && $leasingAgreement->withCurrentResume->first()){
                                $status = 'wznowienie aktualne';
                            }elseif($leasingAgreement->insurances->count() > 0 && is_null($leasingAgreement->withdraw) && is_null($leasingAgreement->archive) && $leasingAgreement->withArchiveResume->first()){
                                $status = 'wznowienie archiwalne';
                            }elseif($leasingAgreement->insurances->count() > 0 &&
                                $leasingAgreement->whereNull('withdraw')->whereNull('archive')
                                    ->whereHas('insurances', function($query){
                                        $query->active();
                                    })->first()){
                                $status = 'trwająca';
                            }
                            $sheet->appendRow(array(
                                $leasingAgreement->nr_contract,
                                $leasingAgreement->nr_agreement,
                                $leasingAgreement->client->name,
                                substr($leasingAgreement->created_at, 0, -3),
                                $status,
                                ($leasingAgreement->insurances->count() > 0) ? $leasingAgreement->insurances->last()->date_from : '---',
                                ($leasingAgreement->insurances->count() > 0) ? $leasingAgreement->insurances->last()->date_to : '---',
                                ($leasingAgreement->insurances->count() > 0) ? $leasingAgreement->insurances->last()->insuranceCompany->name : '---'
                            ));
                        }
                    });
            });

        })->store('xls');
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

    protected function parseDate($date, $modify_days)
    {
        if($date != '') {
            $date_from = new DateTime($date);
            $date_from->modify($modify_days.' day');

            return $date_from->format('Y-m-d H:i:s');
        }
        return 0;
    }
    public function generateTheads()
    {
        return [
            'nr umowy',
            'nr zgłoszenia',
            'leasingobiorca',
            'data zgłoszenia',
            'status umowy',
            'okres ubezp. Od',
            'okres ubezp. Do',
            'ubezpieczyciel'
        ];
    }

}
