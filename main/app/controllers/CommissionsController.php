<?php

class CommissionsController extends \BaseController {


	/**
	 * CommissionsController constructor.
	 */
	public function __construct()
	{
		$this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
		$this->beforeFilter('permitted:prowizje#wejscie');
	}

	public function getNew()
	{
		$commissions = Commission::where('commission_step_id', 1)
			->where(function($query){
				if(Request::has('invoice_date_to')){
				    $query->whereHas('invoice', function ($query){
                        $query->where('created_at', '<=', Request::get('invoice_date_to'));
                    });
				}
				if(Request::has('empty_invoice_date') && Request::get('empty_invoice_date') == 0){
					$query->whereNotNull('invoice_date')->where('invoice_date', '!=', '0000-00-00');
				}
				if(Request::has('nip')){
					$query->whereHas('company', function($query){
						$query->where('nip', 'like', Request::get('nip'));
					});
				}
			})
			->with('invoice', 'company')
            ->orderBy('created_at')
            ->paginate(Session::get('search.pagin', '10'));

		return View::make('commissions.new', compact('commissions'));
	}

	public function getVerification()
	{
		$reports = CommissionReport::with('commissions')->where('commission_step_id', 2)->latest()->paginate(Session::get('search.pagin', '10'));
		return View::make('commissions.verification', compact('reports'));
	}

	public function getSettled()
	{
		$reports = CommissionReport::with('commissions')->where('commission_step_id', 3)->latest()->paginate(Session::get('search.pagin', '10'));
		return View::make('commissions.settled', compact('reports'));
	}

	public function getOmitted()
	{
		$commissions = Commission::where('commission_step_id', 4)
			->where(function($query){
				if(Request::has('invoice_date_to')){
					$query->where('invoice_date', '<=', Request::get('invoice_date_to'));
				}
				if(Request::has('empty_invoice_date') && Request::get('empty_invoice_date') == 0){
					$query->whereNotNull('invoice_date')->where('invoice_date', '!=', '0000-00-00');
				}
				if(Request::has('nip')){
					$query->whereHas('company', function($query){
						$query->where('nip', 'like', Request::get('nip'));
					});
				}
			})
			->with('invoice', 'invoice.injury.branch.company')->orderBy('invoice_date')->paginate(Session::get('search.pagin', '10'));

		return View::make('commissions.omitted', compact('commissions'));
	}

	public function getNotIncluded()
    {
        $query = InjuryInvoices::
            select('injury_invoices.*', 'companies.nip', 'injury.branch_id')
            ->leftJoin('injury', 'injury_invoices.injury_id', '=', 'injury.id')
            ->leftJoin('branches', 'branches.id', '=', 'injury.branch_id')
            ->leftJoin('companies', 'companies.id', '=', 'branches.company_id');


        if(Request::has('nip')){
            $query->where('companies.nip', 'like', Request::get('nip'));
        }

        if(Request::has('invoice_date_to')){
            $query->where('injury_invoices.invoice_date', '<=', Request::get('invoice_date_to'));
        }

        if(Request::has('create_date_from')){
            $query->where('injury_invoices.created_at', '>=', Request::get('create_date_from').' 00:00:00');
        }

        if(Request::has('create_date_to')){
            $query->where('injury_invoices.created_at', '<=', Request::get('create_date_to').' 59:00:00');
        }

        if(Request::has('empty_invoice_date') && Request::get('empty_invoice_date') == 0){
            $query->whereNotNull('injury_invoices.invoice_date')->where('injury_invoices.invoice_date', '!=', '0000-00-00');
        }

        $query->where('injury.branch_id', '>', 0);

        $query->whereRaw('
        (
            select count(*) from `company_groups` 
                inner join `company_company_group` on `company_groups`.`id` = `company_company_group`.`company_group_id` 
            where `company_company_group`.`company_id` = `companies`.`id` and `company_groups`.`deleted_at` is null
        ) >= 1');

        $invoices = $query->where('injury_invoices.commission', 0)
            ->where('injury_invoices.active', 0)
            ->orderBy('injury_invoices.invoice_date')
            ->with('injury.branch.company')
            ->paginate(Session::get('search.pagin', '10'));

        return View::make('commissions.not-included', compact('invoices'));
    }

	public function getGenerateTrialReports($selected = null)
	{
		return View::make('commissions.generate-trial-report', compact('selected'));
	}

	public function postGenerateTrialReports()
	{
		$reports = new \Idea\Commissions\Report(Request::instance());
		$filename = $reports->generate();

		Session::put('download.in.the.next.request', url('commissions/download', [$filename]));

		$commissionReports = CommissionReport::create([
			'user_id' => Auth::user()->id,
			'filename' => $filename,
			'is_trial'  =>  1,
			'is_individual' =>  1,
			'commission_step_id' => 2
		]);
		\Commission::where('commission_step_id', 1)
			->where(function($query){
                if(Request::has('invoice_date_to')){
                    $query->whereHas('invoice', function ($query){
                        $query->where('created_at', '<=', Request::get('invoice_date_to'));
                    });
                }
				if(Request::has('empty_invoice_date') && Request::get('empty_invoice_date') == 0){
					$query->whereNotNull('invoice_date')->where('invoice_date', '!=', '0000-00-00');
				}

				if(Request::has('nip')){
					$query->whereHas('company', function($query){
						$query->where('nip', 'like', Request::get('nip'));
					});
				}
                if(Request::has('commissions')){
                    $query->whereIn('id', Request::get('commissions', []));
                }
			})
			->has('invoice.injury.branch')
			->update(['commission_step_id' => 2, 'commission_individual_report_id' => $commissionReports->id]);

		return 0;
	}

	public function getDownload($filename)
	{
		$filepath = Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER').'/reports/commissions/'.$filename;
		return Response::download($filepath);
	}

    public function getDownloadFile($filename)
    {
        $filepath = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER').'/files/'.$filename;
        return Response::download($filepath);
    }

	public function getReportInvoices($report_id)
	{
		$report = CommissionReport::find($report_id);
		$commissions = $report->commissions()
					->where(function($query){
						if(Request::has('invoice_number')){
							$query->whereHas('invoice', function($query){
								$query->where('invoice_nr', 'like', '%'.Request::get('invoice_number').'%');
							});
						}

						if(Request::has('nip')){
							$query->whereHas('company', function($query){
								$query->where('nip', 'like', Request::get('nip'));
							});
						}

						if(Request::has('empty_commission') && Request::get('empty_commission') == 0){
							$query->whereNull('commission');
						}

                        if(Request::has('category') && Request::get('category') != '0'){
                            $query->whereHas('invoice', function($query){
                                $query->whereHas('injury_files', function ($query){
                                    $query->where('category', Request::get('category'));
                                });
                            });
                        }

                        if(Request::has('injury_invoice_service_type_id') && Request::get('injury_invoice_service_type_id') != '0'){
                            $query->whereHas('invoice', function($query){
                                $query->where('injury_invoice_service_type_id', Request::get('injury_invoice_service_type_id'));
                            });
                        }
					})
					->with('company', 'invoice.injury', 'invoice.serviceType', 'invoice.parent', 'invoice.injury_files')->paginate(Session::get('search.pagin', '10'));

        $serviceTypes = InjuryInvoiceServiceType::lists('name', 'id');
        $serviceTypes[0] = '--- wszystkie ---';

		return View::make('commissions.report-invoices', compact('report', 'commissions', 'serviceTypes'));
	}

	public function getReportCompanies($report_id)
	{
		$report = CommissionReport::find($report_id);

		$companies = Company::
				whereIn('id', $report->commissions->lists('company_id', 'company_id'))
				->where(function($query){
					if(Request::has('company_name')){
						$query->where('name', 'like', '%'.Request::get('company_name').'%');
					}

					if(Request::has('nip')){
						$query->where('nip', 'like', Request::get('nip'));
					}
				})
				->with(['commissions', 'invoiceCommissions' => function($query) use($report_id){
					$query->where('commission_individual_report_id', $report_id);
				}])
				->paginate(Session::get('search.pagin', '10'));

		return View::make('commissions.report-companies', compact('report', 'companies'));
	}

	public function getReportCompanyInvoices($report_id, $company_id)
	{
		$report = CommissionReport::find($report_id);
		$company = Company::find($company_id);

		$commissions = $report->commissions()
			->where(function($query){
				if(Request::has('invoice_number')){
					$query->whereHas('invoice', function($query){
						$query->where('invoice_nr', 'like', '%'.Request::get('invoice_number').'%');
					});
				}

				if(Request::has('empty_commission') && Request::get('empty_commission') == 0){
					$query->whereNull('commission');
				}
			})
			->where('company_id', $company_id)->paginate(Session::get('search.pagin', '10'));

		return View::make('commissions.report-company-invoices', compact('report', 'company', 'commissions'));
	}

	public function getRemoveFromReport()
	{
		return View::make('commissions.remove-from-report');
	}

	public function postRemoveFromReport()
	{
		Commission::whereIn('id', Request::get('commissions', []))->update(['commission_step_id' => 1, 'commission_individual_report_id' => null, 'commission_group_report_id' => null]);

		Flash::success('Faktury zostały pominięte.');

		$report = CommissionReport::find(Request::get('commission_report_id'));
		$report->update(['is_uptodate' => 0]);

		return Response::make(0);
	}

	public function getOmit()
	{
		return View::make('commissions.omit-commissions');
	}

	public function postUploadAttachment()
	{
		\Debugbar::disable();

		$result = array();
		$file = Input::file('file');

		if($file) {
			$destinationPath = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/files';

			$randomKey  = sha1( time() . microtime() );
			$filename = $randomKey.'.'.$file->getClientOriginalExtension();

			if(!File::exists($destinationPath)) {
				File::makeDirectory($destinationPath,511, true);
			}

			$upload_success = Input::file('file')->move($destinationPath, $filename);

			if ($upload_success) {
				$result['status'] = 'success';
				$result['filename'] = $filename;
				return json_encode($result);
			} else {
				$result['status'] = 'error';
				return json_encode($result);
			}
		}
		return Response::json('error', 400);
	}

	public function postOmit()
	{
		Commission::whereIn('id', Request::get('commissions', []))->update(
			[
				'commission_step_id' => 4,
				'commission_individual_report_id' => null,
				'commission_group_report_id' => null,
				'omission_reason' => Request::get('omission_reason'),
				'omission_attachment' => Request::get('omission_attachment')
			]);

		Flash::success('Faktury zostały oznaczone jako nieliczone do prowizji');

		$report = CommissionReport::find(Request::get('commission_report_id'));
		$report->update(['is_uptodate' => 0]);

		return Response::make(0);
	}

	public function getRegenerateTrialReport($report_id)
	{
		return View::make('commissions.regenerate-trial-report', compact('report_id'));
	}

	public function postRegenerateTrialReport($report_id)
	{
		$report = CommissionReport::find($report_id);

		$reports = new \Idea\Commissions\Report(['report_id' => $report_id]);
		$filename = $reports->regenerate();

		Session::put('download.in.the.next.request', url('commissions/download', [$filename]));

		$report->update(['filename' => $filename, 'is_uptodate' => 1]);

		return Response::json(['code' => 0]);
	}

	public function getAcceptSettlement($report_id)
	{
		$report = CommissionReport::find($report_id);

		return View::make('commissions.accept-settlement', compact('report'));
	}

	public function postAcceptSettlement($report_id)
	{
		$report = CommissionReport::find($report_id);
        $report->commissions()->update([
            'acceptation_date' => \Carbon\Carbon::now()
        ]);

		$reports = new \Idea\Commissions\Report(['report_id' => $report_id]);
		$filename = $reports->regenerate();

        $reports = new \Idea\Commissions\SettledReport($report_id);
        $filename_settled = $reports->generate();

        $reports = new \Idea\Commissions\AccountingReport($report_id);
        $filenameAccounting = $reports->generate();
		$settledReport = CommissionReport::create([
			'test_report_id' => $report_id,
			'user_id' => Auth::user()->id,
			'filename' => $filename,
			'filename_settled' => $filename_settled,
			'filename_accounting' => $filenameAccounting,
			'is_trial'  =>  0,
			'is_individual' =>  1,
			'commission_step_id' => 3
		]);

		$report->commissions()->update([
		    'commission_individual_report_id' => $settledReport->id,
            'commission_step_id' => 3,
        ]);
		$report->delete();

		Session::put('download.in.the.next.request', url('commissions/download', [$filename]));

		return Response::json(['code' => 0]);
	}

    public function postGenerateCompanyReport($report_id, $company_id)
    {
        $report = new \Idea\Commissions\CompanyReport($report_id, $company_id);
        $filepath = $report->generate();

        return Response::download($filepath);
    }

    public function getRollbackReport($report_id)
    {
        $report = CommissionReport::find($report_id);
        return View::make('commissions.rollback-report', compact('report'));
    }

    public function postRollbackReport($report_id)
    {
        $report = CommissionReport::find($report_id);

        $report->commissions()->update(['commission_step_id' => 1]);
        $report->delete();

        return Response::json(['code' => 0]);
    }
}
