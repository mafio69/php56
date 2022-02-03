<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class FetchCompanyAccountNumbers extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'system:fetch-companies-accounts';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';

	/**
     * @var \Idea\Vat\Vat
     */
    private $vat;

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
		$this->vat = new \Idea\Vat\Vat();
		
		$count = Company::whereRaw('length(nip) > 0')->count();
		$acc_counter = 0;

        Company::whereRaw('length(nip) > 0')
        ->chunk(200, function ($companies) use ($count, &$acc_counter){
            foreach ($companies as $company) {
                $tries = 0;
                do {
                    $result = $this->fetchAccounts($company);
                    if (!$result) {
                        $tries++;
                        sleep(1);
                    }
                } while (!$result && $tries < 2);

                $this->info('Fetching accounts numbers: ' . ++$acc_counter . '/' . $count);
            }
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


    private function fetchAccounts($company)
    {
        $nip = preg_replace('~\D~', '', $company->nip);
        $result = json_decode($this->vat->checkClient($nip), true);
        // $result = json_decode('6731711090', true);
        if (!isset($result['status'])) {
            return false;
        } else {
            if ($result['status'] != 200) {
                $this->error('fetch failed');
                Log::info('fetch failed', [$company->toArray(), $result]);
            }
            if (isset($result['data'])) {
                $trashed = $company->accountNumbersOnlyTrashed;
                $fetched = $result['data'][0];
                $toDelete = [];
                $toAdd = [];
                
                $currentCollected = [];
                if (array_key_exists('accountNumbers', is_array($fetched)?$fetched:[])>0) {
                    
                    foreach ($trashed as $trash) {
                        if (in_array($trash->account_number, $fetched['accountNumbers'])) {
                            Log::info($trash);
                            $trash->restore();
                        }
                    }
                    
                    $currents = $company->accountNumbers;
                    
                    foreach ($currents as $number) {
                        if (!in_array($number->account_number, $fetched['accountNumbers'])) {
                            if(!$number->if_user_insert)array_push($toDelete, $number->id);
                        }

                        array_push($currentCollected, $number->account_number);
                    }

                    foreach ($fetched['accountNumbers'] as $number) {
                        if (!in_array($number, $currentCollected)) {
                            array_push($toAdd, $number);
                        }
                    }

                } else {
                    $this->error('fetch failed');
                }

                CompanyAccountNumbers::destroy($toDelete);
                foreach ($toAdd as $add) {
                    $new = new CompanyAccountNumbers;
                    $new->company_id = $company->id;
                    $new->account_number = $add;
                    $new->save();
                }
            }
        }
        return true;
    }

}
