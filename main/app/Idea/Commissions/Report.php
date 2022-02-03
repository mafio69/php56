<?php
namespace Idea\Commissions;

use Config;
use Excel;
use PHPExcel_Style_Fill;

class Report
{
	protected $request;
	protected $commissions = ['quarterly' => [], 'monthly' => []];
	public $alphabet = [
		'A' ,
		'B' ,
		'C' ,
		'D' ,
		'E' ,
		'F' ,
		'G' ,
		'H' ,
		'I' ,
		'J' ,
		'K' ,
		'L' ,
		'M' ,
		'N' ,
		'O' ,
		'P' ,
		'Q' ,
		'R' ,
		'S' ,
		'T' ,
		'U' ,
		'V' ,
		'W' ,
		'X' ,
		'Y' ,
		'Z' ,
		'AA',
		'AB',
		'AC',
		'AD',
		'AE',
		'AF',
		'AG',
		'AH',
		'AI',
		'AJ',
		'AK',
		'AL',
		'AM',
		'AN',
		'AO',
		'AP',
		'AQ',
		'AR',
		'AS',
		'AT',
		'AU',
		'AV',
		'AW',
		'AX',
		'AY',
		'AZ',
		'BA',
		'BB',
		'BC',
		'BD',
		'BE',
		'BF',
		'BG',
		'BH',
		'BI',
		'BJ',
		'BK',
		'BL',
		'BM',
		'BN',
		'BO',
		'BP',
		'BQ',
		'BR',
		'BS',
		'BT',
		'BU',
		'BV',
		'BW',
		'BX',
		'BY',
		'BZ',
		'CA',
		'CB',
		'CC',
		'CD',
		'CE',
		'CF',
		'CG',
		'CH',
		'CI',
		'CJ',
		'CK',
		'CL',
		'CM',
		'CN',
		'CO',
		'CP',
		'CQ',
		'CR',
		'CS',
		'CT',
		'CU',
		'CV',
		'CW',
		'CX',
		'CY',
		'CZ',
		'DA',
		'DB',
		'DC',
		'DD',
		'DE',
		'DF',
		'DG',
		'DH',
		'DI',
		'DJ',
		'DK',
		'DL',
		'DM',
		'DN',
		'DO',
		'DP',
		'DQ',
		'DR',
		'DS',
		'DT',
		'DU',
		'DV',
		'DW',
		'DX',
		'DY',
		'DZ',
		'EA',
		'EB',
		'EC',
		'ED',
		'EE',
		'EF',
		'EG',
		'EH',
		'EI',
		'EJ',
		'EK',
		'EL',
		'EM',
		'EN',
		'EO',
		'EP',
		'EQ',
		'ER',
		'ES',
		'ET',
		'EU',
		'EV',
		'EW',
		'EX',
		'EY',
		'EZ'
	];
	protected $brands = [];

	/**
	 * TrialReports constructor.
	 * @param $request
	 */
	public function __construct($request)
	{
		$this->request = $request;
		$this->brands = \Brands::lists('name','id');
		$this->brands = array_map("mb_strtoupper", $this->brands);
		$this->brands = array_flip($this->brands);
	}

	public function generate(){
		\Commission::where('commission_step_id', 1)
			->where(function($query){
                if($this->request->has('invoice_date_to')){
                    $query->whereHas('invoice', function ($query){
                        $query->where('created_at', '<=', $this->request->get('invoice_date_to'));
                    });
                }
				if($this->request->has('empty_invoice_date') && $this->request->get('empty_invoice_date') == 0){
					$query->whereNotNull('invoice_date')->where('invoice_date', '!=', '0000-00-00');
				}

				if($this->request->has('nip')){
					$query->whereHas('company', function($query){
						$query->where('nip', 'like', $this->request->get('nip'));
					});
				}
				if($this->request->has('commissions')){
                    $query->whereIn('id', $this->request->get('commissions', []));
                }
			})
			->has('company')
			->orderBy('invoice_date')
            ->chunk(100, function($commissions){
                $commissions->load('invoice', 'company.commissions', 'invoice.injury.vehicle', 'invoice.injury_files');
				foreach($commissions as $commission) {
					if($commission->company->billing_cycle_id == 2){ //kwartalnie
						if(!isset($this->commissions['quarterly'][$commission->invoice_date->format('Y')][$commission->invoice_date->quarter][$commission->company->id]['company'])) { //inicjowanie serwisu w tabeli
                            $this->commissions['quarterly']
                                [$commission->invoice_date->format('Y')]
                                [$commission->invoice_date->quarter]
                                [$commission->company->id]
                                ['company'] = $commission->company;
                        }

                        $base_netto = $this->calculateBaseNetto($commission);

						$this->commissions['quarterly']
											[$commission->invoice_date->format('Y')]
											[$commission->invoice_date->quarter]
											[$commission->company->id]
											['injuries']
											[$commission->invoice->injury->id]
											['values'][$commission->invoice->id] = $base_netto;

						if($commission->invoice->injury->vehicle_type == 'Vehicles')
						{
							$brand_name = mb_strtoupper($commission->invoice->injury->vehicle->brand);
							if(isset($this->brands[$brand_name])){
								$brand_id = $this->brands[$brand_name];
							}else{
								$brand_id = 0;
							}
						}else{
							$brand_id = $commission->invoice->injury->vehicle->brand_id;
						}

						$this->commissions['quarterly']
											[$commission->invoice_date->format('Y')]
											[$commission->invoice_date->quarter]
											[$commission->company->id]
											['injuries']
											[$commission->invoice->injury->id]
											['brands'][$commission->invoice->id] = $brand_id;

						$this->commissions['quarterly']
											[$commission->invoice_date->format('Y')]
											[$commission->invoice_date->quarter]
											[$commission->company->id]
											['ids'][$commission->invoice->id] = $commission->id;

					}else{ //miesięcznie
						if(!isset($this->commissions['monthly'][$commission->invoice_date->format('Y')][$commission->invoice_date->format('m')][$commission->company->id]['company'])) {
                            $this->commissions['monthly']
                                [$commission->invoice_date->format('Y')]
                                [$commission->invoice_date->format('m')]
                                [$commission->company->id]
                                ['company'] = $commission->company;
                        }

                        $base_netto = $this->calculateBaseNetto($commission);

						$this->commissions['monthly']
											[$commission->invoice_date->format('Y')]
											[$commission->invoice_date->format('m')]
											[$commission->company->id]
											['injuries']
											[$commission->invoice->injury->id]
											['values'][$commission->invoice->id] = $base_netto;

						if($commission->invoice->injury->vehicle_type == 'Vehicles')
						{
							$brand_name = mb_strtoupper($commission->invoice->injury->vehicle->brand);
							if(isset($this->brands[$brand_name])){
								$brand_id = $this->brands[$brand_name];
							}else{
								$brand_id = 0;
							}
						}else{
							$brand_id = $commission->invoice->injury->vehicle->brand_id;
						}
						$this->commissions['monthly']
											[$commission->invoice_date->format('Y')]
											[$commission->invoice_date->format('m')]
											[$commission->company->id]
											['injuries']
											[$commission->invoice->injury->id]
											['brands'][$commission->invoice->id] = $brand_id;

						$this->commissions['monthly']
											[$commission->invoice_date->format('Y')]
											[$commission->invoice_date->format('m')]
											[$commission->company->id]
											['ids'][$commission->invoice->id] = $commission->id;
					}
				}
			});

		$filename = 'raport_prowizji_'.date('Y_m_d_h_i_s');

		Excel::create($filename, function($excel) {

			$excel->sheet('miesięczne', function($sheet) {

				$sheet->appendRow(array('serwis', 'nip'));

				$currentRow = 1;
				$companyRow = [];
				$row_time_mapping = [];
				$current_column = 2;
				foreach($this->commissions['monthly'] as $year => $monthly_year_items) {
					foreach ($monthly_year_items as $month => $monthly_month_items) {
						$sheet->setCellValue($this->alphabet[$current_column].'1', $year.'-'.$month);
						$row_time_mapping[$year][$month] = $this->alphabet[$current_column];
						$current_column++;
					}
				}

				foreach($this->commissions['monthly'] as $year => $monthly_year_items)
				{
					foreach ($monthly_year_items as $month => $monthly_month_items)
					{
						foreach ($monthly_month_items as $company_id => $company_items)
						{
							$company = $company_items['company'];

							$values = [];
							foreach($company_items['injuries'] as $injury_id => $injury_items) {
								$values[$injury_id] = array_sum($injury_items['values']);
							}

							switch ($company->commission_type_id) {
								case 1: //Prowizja liniowa
									$companyCommission = $company->commissions->first();
									$commission = ($companyCommission->commission / 100) * array_sum($values);

									$this->setCommission($company_items['ids'], $companyCommission->commission, $company_items['injuries']);
									break;
								case 2: //Prowizja progowa, wg ilości zleceń
									$ct_invoices = count($values);
									$companyCommission = 100;
									$threshold = 0;
									foreach ($company->commissions as $companyCommissionThreshold) {
										if ($ct_invoices >= $companyCommissionThreshold->min_amount && $threshold <= $companyCommissionThreshold->min_amount) {
											$threshold = $companyCommissionThreshold->min_amount;
											$companyCommission = $companyCommissionThreshold->commission;
										}
									}
									$commission = ($companyCommission / 100) * array_sum($values);
									$this->setCommission($company_items['ids'], $companyCommission, $company_items['injuries']);
									break;
								case 3: //Prowizja progowa, wg wartości zleceń
									$companyCommission = 100;
									$threshold = 0;
									$values_sum = array_sum($values);
									foreach ($company->commissions as $companyCommissionThreshold) {
										if ($values_sum >= $companyCommissionThreshold->min_value && $threshold <= $companyCommissionThreshold->min_value) {
											$threshold = $companyCommissionThreshold->min_value;
											$companyCommission = $companyCommissionThreshold->commission;
										}
									}
									$commission = ($companyCommission / 100) * $values_sum;
									$this->setCommission($company_items['ids'], $companyCommission, $company_items['injuries']);
									break;
								case 4: //Wg marki pojazdu
									$brandCommissions = [];
									foreach ($company->commissions as $companyCommissionThreshold) {
										$brandCommissions[$companyCommissionThreshold->brand_id] = $companyCommissionThreshold->commission;
									}

									$values = [];

									foreach($company_items['injuries'] as $injury_id => $injury_items){
										foreach ($injury_items['values'] as $k => $value) {
											if (isset($brandCommissions[$injury_items['brands'][$k]])) {
												if (!isset($values[$brandCommissions[$injury_items['brands'][$k]]]))
													$values[$brandCommissions[$injury_items['brands'][$k]]] = 0;

												$values[$brandCommissions[$injury_items['brands'][$k]]] += $value;
											} else {
												if (!isset($values[0]))
													$values[0] = 0;

												$values[0] += $value;
											}
										}
									}

									$commission = 0;
									foreach ($values as $brand_id => $value) {
										if (isset($brandCommissions[$brand_id])) $brand_commission = $brandCommissions[$brand_id];
										elseif (isset($brandCommissions[0])) $brand_commission = $brandCommissions[0];
										else $brand_commission = 0;

										$sub_commission = ($brand_commission / 100) * $value;
										$commission += $sub_commission;
									}
                                    $this->setCommission($company_items['ids'], $brand_commission, $company_items['injuries']);
									if ($commission == 0) $commission = null;
									break;
								default:
									$commission = null;
									break;
							}

							if($commission){
                                $commission = round($commission, 2);
                                $commission = number_format((float)$commission, 2, '.', '');
                            }

							if(isset($companyRow[$company->id])){
								$sheet->setCellValue($row_time_mapping[$year][$month].$companyRow[$company->id], $commission);
							}else{
								$sheet->appendRow(array($company->name, $company->nip));
								$currentRow++;
								$companyRow[$company->id] = $currentRow;
								$sheet->setCellValue($row_time_mapping[$year][$month].$currentRow, $commission);
							}

							if(!$commission)
							{
								$sheet->getStyle($row_time_mapping[$year][$month].$companyRow[$company->id])->applyFromArray(
									array(
										'fill' => array(
											'type' => PHPExcel_Style_Fill::FILL_SOLID,
											'color' => array('rgb' => 'FFB8B8')
										)
									)
								);
							}
						}
					}
				}


			});

			$excel->sheet('kwartalne', function($sheet) {

				$sheet->appendRow(array('serwis', 'nip'));

				$currentRow = 1;
				$companyRow = [];
				$row_time_mapping = [];
				$current_column = 2;
				foreach($this->commissions['quarterly'] as $quarter_year => $quarter_year_items) {
					foreach ($quarter_year_items as $quarter => $quarter_items) {
						$sheet->setCellValue($this->alphabet[$current_column].'1', $quarter_year.' kw. '.$quarter);
						$row_time_mapping[$quarter_year][$quarter] = $this->alphabet[$current_column];
						$current_column++;
					}
				}

				foreach($this->commissions['quarterly'] as $quarter_year => $quarter_year_items)
				{
					foreach ($quarter_year_items as $quarter => $quarter_items)
					{
						foreach ($quarter_items as $company_id => $company_items)
						{
							$company  = $company_items['company'];

							$values = [];
							foreach($company_items['injuries'] as $injury_id => $injury_items) {
								$values[$injury_id] = array_sum($injury_items['values']);
							}

							switch ($company->commission_type_id) {
								case 1: //Prowizja liniowa
									$companyCommission = $company->commissions->first();
									$commission = ($companyCommission->commission / 100) * array_sum($values);
                                    $this->setCommission($company_items['ids'], $companyCommission->commission, $company_items['injuries']);
									break;
								case 2: //Prowizja progowa, wg ilości zleceń
									$ct_invoices = count($values);
									$companyCommission = 100;
									$threshold = 0;
									foreach ($company->commissions as $companyCommissionThreshold) {
										if ($ct_invoices >= $companyCommissionThreshold->min_amount && $threshold <= $companyCommissionThreshold->min_amount) {
											$threshold = $companyCommissionThreshold->min_amount;
											$companyCommission = $companyCommissionThreshold->commission;
										}
									}
									$commission = ($companyCommission / 100) * array_sum($values);
                                    $this->setCommission($company_items['ids'], $companyCommission, $company_items['injuries']);
									break;
								case 3: //Prowizja progowa, wg wartości zleceń
									$companyCommission = 100;
									$threshold = 0;
									$values_sum = array_sum($values);
									foreach ($company->commissions as $companyCommissionThreshold) {
										if ($values_sum >= $companyCommissionThreshold->min_value && $threshold <= $companyCommissionThreshold->min_value) {
											$threshold = $companyCommissionThreshold->min_value;
											$companyCommission = $companyCommissionThreshold->commission;
										}
									}
									$commission = ($companyCommission / 100) * $values_sum;
                                    $this->setCommission($company_items['ids'], $companyCommission, $company_items['injuries']);
									break;
								case 4: //Wg marki pojazdu
									$brandCommissions = [];
									foreach ($company->commissions as $companyCommissionThreshold) {
										$brandCommissions[$companyCommissionThreshold->brand_id] = $companyCommissionThreshold->commission;
									}

									$values = [];

									foreach($company_items['injuries'] as $injury_id => $injury_items){
										foreach ($injury_items['values'] as $k => $value) {
											if (isset($brandCommissions[$injury_items['brands'][$k]])) {
												if (!isset($values[$brandCommissions[$injury_items['brands'][$k]]]))
													$values[$brandCommissions[$injury_items['brands'][$k]]] = 0;

												$values[$brandCommissions[$injury_items['brands'][$k]]] += $value;
											} else {
												if (!isset($values[0]))
													$values[0] = 0;

												$values[0] += $value;
											}
										}
									}

									$commission = 0;
									foreach ($values as $brand_id => $value) {
										if (isset($brandCommissions[$brand_id])) $brand_commission = $brandCommissions[$brand_id];
										elseif (isset($brandCommissions[0])) $brand_commission = $brandCommissions[0];
										else $brand_commission = 0;

										$sub_commission = ($brand_commission / 100) * $value;
										$commission += $sub_commission;
									}
                                    $this->setCommission($company_items['ids'], $brand_commission, $company_items['injuries']);
									if ($commission == 0) $commission = null;
									break;
								default:
									$commission = null;
									break;
							}

                            if($commission){
                                $commission = round($commission, 2);
                                $commission = number_format((float)$commission, 2, '.', '');
                            }

							if(isset($companyRow[$company->id])){
								$sheet->setCellValue($row_time_mapping[$quarter_year][$quarter].$companyRow[$company->id], $commission);
							}else{
								$sheet->appendRow(array($company->name, $company->nip));
								$currentRow++;
								$companyRow[$company->id] = $currentRow;
								$sheet->setCellValue($row_time_mapping[$quarter_year][$quarter].$currentRow, $commission);
							}

							if(! $commission)
							{
								$sheet->getStyle($row_time_mapping[$quarter_year][$quarter].$companyRow[$company->id])->applyFromArray(
									array(
										'fill' => array(
											'type' => PHPExcel_Style_Fill::FILL_SOLID,
											'color' => array('rgb' => 'FFB8B8')
										)
									)
								);
							}
						}
					}
				}
			});

		})->store('xlsx', Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER').'/reports/commissions', true);

		return $filename.'.xlsx';
	}

	public function regenerate(){
		$report = \CommissionReport::find( $this->request['report_id'] );

		$report->commissions()
            ->orderBy('invoice_date')
            ->chunk(100, function($commissions){
                $commissions->load('invoice', 'company.commissions', 'invoice.injury.vehicle', 'invoice.injury_files');
				foreach($commissions as $commission) {
					if($commission->company->billing_cycle_id == 2){ //kwartalnie
						if(!isset($this->commissions['quarterly'][$commission->invoice_date->format('Y')][$commission->invoice_date->quarter][$commission->company->id]['company'])) {
                            $this->commissions['quarterly']
                                [$commission->invoice_date->format('Y')]
                                [$commission->invoice_date->quarter]
                                [$commission->company->id]
                                ['company'] = $commission->company;
                        }

                        $base_netto = $this->calculateBaseNetto($commission);

						$this->commissions['quarterly']
                            [$commission->invoice_date->format('Y')]
                            [$commission->invoice_date->quarter]
                            [$commission->company->id]
                            ['injuries']
                            [$commission->invoice->injury->id]
                            ['values'][$commission->invoice->id] = $base_netto;

						if($commission->invoice->injury->vehicle_type == 'Vehicles')
						{
							$brand_name = mb_strtoupper($commission->invoice->injury->vehicle->brand);
							if(isset($this->brands[$brand_name])){
								$brand_id = $this->brands[$brand_name];
							}else{
								$brand_id = 0;
							}
						}else{
							$brand_id = $commission->invoice->injury->vehicle->brand_id;
						}

						$this->commissions['quarterly']
						[$commission->invoice_date->format('Y')]
						[$commission->invoice_date->quarter]
						[$commission->company->id]
						['injuries']
						[$commission->invoice->injury->id]
						['brands'][$commission->invoice->id] = $brand_id;

						$this->commissions['quarterly']
						[$commission->invoice_date->format('Y')]
						[$commission->invoice_date->quarter]
						[$commission->company->id]
						['ids'][$commission->invoice->id] = $commission->id;

					}else{ //miesięcznie
						if(!isset($this->commissions['monthly'][$commission->invoice_date->format('Y')][$commission->invoice_date->format('m')][$commission->company->id]['company'])) {
                            $this->commissions['monthly']
                            [$commission->invoice_date->format('Y')]
                            [$commission->invoice_date->format('m')]
                            [$commission->company->id]
                            ['company'] = $commission->company;
                        }

                        $base_netto = $this->calculateBaseNetto($commission);

						$this->commissions['monthly']
                            [$commission->invoice_date->format('Y')]
                            [$commission->invoice_date->format('m')]
                            [$commission->company->id]
                            ['injuries']
                            [$commission->invoice->injury->id]
                            ['values'][$commission->invoice->id] = $base_netto;

						if($commission->invoice->injury->vehicle_type == 'Vehicles')
						{
							$brand_name = mb_strtoupper($commission->invoice->injury->vehicle->brand);
							if(isset($this->brands[$brand_name])){
								$brand_id = $this->brands[$brand_name];
							}else{
								$brand_id = 0;
							}
						}else{
							$brand_id = $commission->invoice->injury->vehicle->brand_id;
						}
						$this->commissions['monthly']
						[$commission->invoice_date->format('Y')]
						[$commission->invoice_date->format('m')]
						[$commission->company->id]
						['injuries']
						[$commission->invoice->injury->id]
						['brands'][$commission->invoice->id] = $brand_id;

						$this->commissions['monthly']
						[$commission->invoice_date->format('Y')]
						[$commission->invoice_date->format('m')]
						[$commission->company->id]
						['ids'][$commission->invoice->id] = $commission->id;
					}
				}
			});

		$filename = 'raport_prowizji_'.date('Y_m_d_h_i_s');
		Excel::create($filename, function($excel) {

			$excel->sheet('miesięczne', function($sheet) {

				$sheet->appendRow(array('serwis', 'nip'));

				$currentRow = 1;
				$companyRow = [];
				$row_time_mapping = [];
				$current_column = 2;
				foreach($this->commissions['monthly'] as $year => $monthly_year_items) {
					foreach ($monthly_year_items as $month => $monthly_month_items) {
						$sheet->setCellValue($this->alphabet[$current_column].'1', $year.'-'.$month);
						$row_time_mapping[$year][$month] = $this->alphabet[$current_column];
						$current_column++;
					}
				}

				foreach($this->commissions['monthly'] as $year => $monthly_year_items)
				{
					foreach ($monthly_year_items as $month => $monthly_month_items)
					{
						foreach ($monthly_month_items as $company_id => $company_items)
						{
							$company = $company_items['company'];

							$values = [];
							foreach($company_items['injuries'] as $injury_id => $injury_items) {
								$values[$injury_id] = array_sum($injury_items['values']);
							}

							switch ($company->commission_type_id) {
								case 1: //Prowizja liniowa
									$companyCommission = $company->commissions->first();
									$commission = ($companyCommission->commission / 100) * array_sum($values);
									$this->setCommission($company_items['ids'], $companyCommission->commission, $company_items['injuries']);
									break;
								case 2: //Prowizja progowa, wg ilości zleceń
									$ct_invoices = count($values);
									$companyCommission = 100;
									$threshold = 0;
									foreach ($company->commissions as $companyCommissionThreshold) {
										if ($ct_invoices >= $companyCommissionThreshold->min_amount && $threshold <= $companyCommissionThreshold->min_amount) {
											$threshold = $companyCommissionThreshold->min_amount;
											$companyCommission = $companyCommissionThreshold->commission;
										}
									}
									$commission = ($companyCommission / 100) * array_sum($values);
									$this->setCommission($company_items['ids'], $companyCommission, $company_items['injuries']);
									break;
								case 3: //Prowizja progowa, wg wartości zleceń
									$companyCommission = 100;
									$threshold = 0;
									$values_sum = array_sum($values);
									foreach ($company->commissions as $companyCommissionThreshold) {
										if ($values_sum >= $companyCommissionThreshold->min_value && $threshold <= $companyCommissionThreshold->min_value) {
											$threshold = $companyCommissionThreshold->min_value;
											$companyCommission = $companyCommissionThreshold->commission;
										}
									}
									$commission = ($companyCommission / 100) * $values_sum;
									$this->setCommission($company_items['ids'], $companyCommission, $company_items['injuries']);
									break;
								case 4: //Wg marki pojazdu
									$brandCommissions = [];
									foreach ($company->commissions as $companyCommissionThreshold) {
										$brandCommissions[$companyCommissionThreshold->brand_id] = $companyCommissionThreshold->commission;
									}

									$values = [];

									foreach($company_items['injuries'] as $injury_id => $injury_items){
										foreach ($injury_items['values'] as $k => $value) {
											if (isset($brandCommissions[$injury_items['brands'][$k]])) {
												if (!isset($values[$brandCommissions[$injury_items['brands'][$k]]]))
													$values[$brandCommissions[$injury_items['brands'][$k]]] = 0;

												$values[$brandCommissions[$injury_items['brands'][$k]]] += $value;
											} else {
												if (!isset($values[0]))
													$values[0] = 0;

												$values[0] += $value;
											}
										}
									}

									$commission = 0;
									foreach ($values as $brand_id => $value) {
										if (isset($brandCommissions[$brand_id])) $brand_commission = $brandCommissions[$brand_id];
										elseif (isset($brandCommissions[0])) $brand_commission = $brandCommissions[0];
										else $brand_commission = 0;

										$sub_commission = ($brand_commission / 100) * $value;
										$commission += $sub_commission;
									}
                                    $this->setCommission($company_items['ids'], $brand_commission, $company_items['injuries']);
									if ($commission == 0) $commission = null;
									break;
								default:
									$commission = null;
									break;
							}

                            if($commission){
                                $commission = round($commission, 2);
                                $commission = number_format((float)$commission, 2, '.', '');
                            }

							if(isset($companyRow[$company->id])){
								$sheet->setCellValue($row_time_mapping[$year][$month].$companyRow[$company->id], $commission);
							}else{
								$sheet->appendRow(array($company->name, $company->nip));
								$currentRow++;
								$companyRow[$company->id] = $currentRow;
								$sheet->setCellValue($row_time_mapping[$year][$month].$currentRow, $commission);
							}

							if(!$commission)
							{
								$sheet->getStyle($row_time_mapping[$year][$month].$companyRow[$company->id])->applyFromArray(
									array(
										'fill' => array(
											'type' => PHPExcel_Style_Fill::FILL_SOLID,
											'color' => array('rgb' => 'FFB8B8')
										)
									)
								);
							}
						}
					}
				}


			});

			$excel->sheet('kwartalne', function($sheet) {

				$sheet->appendRow(array('serwis', 'nip'));

				$currentRow = 1;
				$companyRow = [];
				$row_time_mapping = [];
				$current_column = 2;
				foreach($this->commissions['quarterly'] as $quarter_year => $quarter_year_items) {
					foreach ($quarter_year_items as $quarter => $quarter_items) {
						$sheet->setCellValue($this->alphabet[$current_column].'1', $quarter_year.' kw. '.$quarter);
						$row_time_mapping[$quarter_year][$quarter] = $this->alphabet[$current_column];
						$current_column++;
					}
				}

				foreach($this->commissions['quarterly'] as $quarter_year => $quarter_year_items)
				{
					foreach ($quarter_year_items as $quarter => $quarter_items)
					{
						foreach ($quarter_items as $company_id => $company_items)
						{
							$company  = $company_items['company'];

							$values = [];
							foreach($company_items['injuries'] as $injury_id => $injury_items) {
								$values[$injury_id] = array_sum($injury_items['values']);
							}

							switch ($company->commission_type_id) {
								case 1: //Prowizja liniowa
									$companyCommission = $company->commissions->first();
									$commission = ($companyCommission->commission / 100) * array_sum($values);
                                    $this->setCommission($company_items['ids'], $companyCommission->commission, $company_items['injuries']);
									break;
								case 2: //Prowizja progowa, wg ilości zleceń
									$ct_invoices = count($values);
									$companyCommission = 100;
									$threshold = 0;
									foreach ($company->commissions as $companyCommissionThreshold) {
										if ($ct_invoices >= $companyCommissionThreshold->min_amount && $threshold <= $companyCommissionThreshold->min_amount) {
											$threshold = $companyCommissionThreshold->min_amount;
											$companyCommission = $companyCommissionThreshold->commission;
										}
									}
									$commission = ($companyCommission / 100) * array_sum($values);
                                    $this->setCommission($company_items['ids'], $companyCommission, $company_items['injuries']);

									break;
								case 3: //Prowizja progowa, wg wartości zleceń
									$companyCommission = 100;
									$threshold = 0;
									$values_sum = array_sum($values);
									foreach ($company->commissions as $companyCommissionThreshold) {
										if ($values_sum >= $companyCommissionThreshold->min_value && $threshold <= $companyCommissionThreshold->min_value) {
											$threshold = $companyCommissionThreshold->min_value;
											$companyCommission = $companyCommissionThreshold->commission;
										}
									}
									$commission = ($companyCommission / 100) * $values_sum;
                                    $this->setCommission($company_items['ids'], $companyCommission, $company_items['injuries']);
									break;
								case 4: //Wg marki pojazdu
									$brandCommissions = [];
									foreach ($company->commissions as $companyCommissionThreshold) {
										$brandCommissions[$companyCommissionThreshold->brand_id] = $companyCommissionThreshold->commission;
									}

									$values = [];

									foreach($company_items['injuries'] as $injury_id => $injury_items){
										foreach ($injury_items['values'] as $k => $value) {
											if (isset($brandCommissions[$injury_items['brands'][$k]])) {
												if (!isset($values[$brandCommissions[$injury_items['brands'][$k]]]))
													$values[$brandCommissions[$injury_items['brands'][$k]]] = 0;

												$values[$brandCommissions[$injury_items['brands'][$k]]] += $value;
											} else {
												if (!isset($values[0]))
													$values[0] = 0;

												$values[0] += $value;
											}
										}
									}

									$commission = 0;
									foreach ($values as $brand_id => $value) {
										if (isset($brandCommissions[$brand_id])) $brand_commission = $brandCommissions[$brand_id];
										elseif (isset($brandCommissions[0])) $brand_commission = $brandCommissions[0];
										else $brand_commission = 0;

										$sub_commission = ($brand_commission / 100) * $value;
										$commission += $sub_commission;
									}
                                    $this->setCommission($company_items['ids'], $brand_commission, $company_items['injuries']);
									if ($commission == 0) $commission = null;
									break;
								default:
									$commission = null;
									break;
							}

                            if($commission){
                                $commission = round($commission, 2);
                                $commission = number_format((float)$commission, 2, '.', '');
                            }

							if(isset($companyRow[$company->id])){
								$sheet->setCellValue($row_time_mapping[$quarter_year][$quarter].$companyRow[$company->id], $commission);
							}else{
								$sheet->appendRow(array($company->name, $company->nip));
								$currentRow++;
								$companyRow[$company->id] = $currentRow;
								$sheet->setCellValue($row_time_mapping[$quarter_year][$quarter].$currentRow, $commission);
							}

							if(! $commission)
							{
								$sheet->getStyle($row_time_mapping[$quarter_year][$quarter].$companyRow[$company->id])->applyFromArray(
									array(
										'fill' => array(
											'type' => PHPExcel_Style_Fill::FILL_SOLID,
											'color' => array('rgb' => 'FFB8B8')
										)
									)
								);
							}
						}
					}
				}
			});

		})->store('xlsx', Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER').'/reports/commissions', true);

		return $filename.'.xlsx';
	}

	private function setCommission($ids, $commission, $injuries)
	{
		$commission = ($commission / 100);

		foreach($injuries as $injury_id => $injury_items){
		    foreach($injury_items['values'] as $invoice_id => $base_netto)
            {
                if(key_exists($invoice_id, $ids)){
                    \Commission::where('id', $ids[$invoice_id])->update([
                        'commissions.commission' => round( $commission * $base_netto , 2),
                        'commissions.commission_percentage' => $commission
                    ]);
                }
            }
        }
	}

    private function calculateBaseNetto($commission)
    {
        if($commission->invoice->injury_files->category == 3){
            return $commission->invoice->base_netto;
        }elseif( $commission->invoice->parent_id != 0 ){
            $korekta = $commission->invoice->base_netto;
            $parent = $commission->invoice->parent->base_netto;
            return $korekta - $parent;
        }

        return $commission->invoice->base_netto;
    }


}
