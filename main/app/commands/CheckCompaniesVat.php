<?php

use Illuminate\Console\Command;

class CheckCompaniesVat extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'system:check-companies-vat';

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

        $count = Company::
            whereHas('groups', function ($query){
                $query->whereIn('company_group_id', [1,5]);
            }
        )->count();

        $counter = 0;

        Company::
            whereHas('groups', function ($query){
                $query->whereIn('company_group_id', [1,5]);
            }
        )->chunk(200, function ($companies) use ($count, &$counter) {
            $companies->load('companyVatCheck');

            foreach($companies as $company){
                $tries = 0;

                do {
                    $result = $this->check($company);
                    if (!$result) {
                        $tries++;
                        sleep(1);

                    }
                } while (!$result && $tries < 2);

                $this->info('Checking status: ' . ++$counter . '/' . $count);
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

    private function check($company)
    {
        $result = json_decode($this->vat->checkNip(trim($company->nip)), true);
        if (!$result) {
            dd($this->vat->getCurl());
        }
        if (!isset($result['status'])) {
            return false;
        } else {
            if ($result['status'] != 200) {
                $this->error('vat validation failed');
                Log::info('vat validation failed', [$company->toArray(), $result]);
                $code = $result['status'];

                if ($code == 500 || $code == '500') {
                    return false;
                }

            } else {
                $code = $result['code'];
            }
            $status = $result['message'];
            if ($status == 'Nieprawidłowy format zapytania') {
                $status = 'Nieprawidłowy NIP';
            }

            $company_vat_check = CompanyVatCheck::create([
                'company_id' => $company->id,
                'status_code' => $code,
                'status' => $status,
            ]);

            if (!$company->companyVatCheck) {
                $company->created_at = \Carbon\Carbon::now()->subMonth();
                $company->save(['timestamps' => false]);
            }

            $company->update([
                'is_active_vat' => $code == 'C' ? 1 : 0,
                'company_vat_check_id' => $company_vat_check->id,
            ]);

            return true;
        }
    }

}
